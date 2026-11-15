#!/usr/bin/env python3
"""Rebuild documentation indexes without executing application PHP or reading DB rows."""
import argparse
import collections
import csv
import json
import os
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[2]
DOCS = ROOT / "DUCS"


def kind(path):
    name = path.as_posix()
    parts = path.parts
    if path.suffix.lower() == ".php":
        if parts[0] == "components_site": return "Site component template"
        if parts[0] == "include_site": return "Site bootstrap/helper/language"
        if parts[0] == "lib": return "Bundled NuSOAP PHP"
        if parts[0] in ("upload_site", "uploadfile_site"): return "Upload directory PHP"
        if any(p in ("asset", "assets", "vendors") for p in parts): return "Bundled library PHP"
        return "Application PHP"
    if name == "site.sql": return "SQL dump"
    if parts[0] in ("upload_site", "uploadfile_site"): return "Uploaded/public media"
    if parts[0] == "asset_site": return "Frontend source/asset"
    if parts[0] == "asset" and "img" in parts: return "Image asset"
    if any(p in ("asset", "assets", "vendors") for p in parts): return "Bundled asset/library"
    return "Other file"


def write_tsv(name, columns, rows):
    with (DOCS / name).open("w", newline="", encoding="utf-8") as output:
        writer = csv.writer(output, delimiter="\t", lineterminator="\n")
        writer.writerow(columns)
        writer.writerows(rows)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--date", required=True, help="Review date in YYYY-MM-DD")
    args = parser.parse_args()
    if not re.fullmatch(r"\d{4}-\d{2}-\d{2}", args.date): parser.error("Invalid date")
    paths, errors = [], []
    for folder, directories, files in os.walk(ROOT, onerror=lambda e: errors.append(str(e))):
        directories[:] = sorted(d for d in directories if d != ".git" and not (Path(folder) == ROOT and d == "DUCS"))
        for name in sorted(files):
            absolute = Path(folder) / name
            if absolute.is_file(): paths.append(absolute.relative_to(ROOT))
    paths.sort(key=lambda p: p.as_posix())
    manifest, php_rows, dispatch, tables, includes = [], [], [], set(), []
    for path in paths:
        absolute = ROOT / path
        category = kind(path)
        manifest.append([path.as_posix(), absolute.stat().st_size, path.suffix.lower() or "(none)", path.parts[0] if len(path.parts) > 1 else "(root)", category])
        if path.suffix.lower() != ".php": continue
        source = absolute.read_text(encoding="utf-8", errors="replace")
        # These indexes are lexical aids: comments/strings can yield false positives.
        literal_includes = sorted(set(m.group(2) for m in re.finditer(r"\b(?:include|require)(?:_once)?\s*\(?\s*(['\"])([^'\"\r\n]+)\1", source)))
        inputs = sorted(set(f"{m.group(1)}:{m.group(3)}" for m in re.finditer(r"\$_(GET|POST|REQUEST|FILES|COOKIE|SERVER)\s*\[\s*(['\"])([^'\"]+)\2\s*\]", source)))
        sessions = sorted(set(m.group(2) for m in re.finditer(r"\$_SESSION\s*\[\s*(['\"])([^'\"]+)\1\s*\]", source)))
        references = sorted(set(m.group(1).lower() for m in re.finditer(r"\b(?:FROM|JOIN|UPDATE|INTO|TABLE(?:\s+IF\s+NOT\s+EXISTS)?)\s+`?(tbl_[a-zA-Z0-9_]+)", source, re.I)))
        actions = []
        for match in re.finditer(r"(?:\$do|\$_(?:GET|POST|REQUEST)\s*\[\s*['\"]do['\"]\s*\])\s*(?:===|==|!==|!=|=)\s*(['\"])([^'\"\r\n]+)\1", source):
            value = match.group(2)
            actions.append(value)
            dispatch.append([path.as_posix(), source.count("\n", 0, match.start()) + 1, value])
        functions = sorted(set(m.group(1) for m in re.finditer(r"\bfunction\s+&?\s*([A-Za-z_][A-Za-z0-9_]*)\s*\(", source)))
        php_rows.append([path.as_posix(), len(source.splitlines()), category, ", ".join(literal_includes), ", ".join(inputs), ", ".join(sessions), ", ".join(references), ", ".join(dict.fromkeys(actions)), ", ".join(functions)])
        tables.update((table, path.as_posix()) for table in references)
        # Only complete quoted include statements can be resolved without evaluation.
        for match in re.finditer(r"\b(?:include|require)(?:_once)?\s*\(?\s*(['\"])([^'\"\r\n]+)\1\s*\)?\s*;", source):
            target = match.group(2)
            if "://" in target or (match.group(1) == '"' and '$' in target): continue
            resolved = (absolute.parent / target).resolve()
            try: relative = resolved.relative_to(ROOT).as_posix()
            except ValueError: relative = "(outside project)"
            includes.append([path.as_posix(), source.count("\n", 0, match.start()) + 1, target, relative, "present" if resolved.exists() else "missing_candidate"])
    write_tsv("FILE_MANIFEST.tsv", ["relative_path", "size_bytes", "extension", "area", "kind"], manifest)
    write_tsv("PHP_CODE_INDEX.tsv", ["relative_path", "lines", "kind", "literal_includes", "http_input_keys", "session_keys", "tbl_references", "do_literals", "declared_functions"], php_rows)
    write_tsv("DISPATCH_ACTION_INDEX.tsv", ["relative_path", "line", "do_value"], dispatch)
    write_tsv("TABLE_REFERENCES.tsv", ["table_name", "relative_php_path"], sorted(tables))
    write_tsv("LITERAL_INCLUDE_AUDIT.tsv", ["relative_php_path", "line", "literal_target", "resolved_from_file_directory", "status"], includes)
    areas = collections.Counter(row[3] for row in manifest)
    kinds = collections.Counter(row[4] for row in manifest)
    summary = {"review_date": args.date, "readable_files": len(manifest), "php_files": len(php_rows), "static_tables": len({r[0] for r in tables}), "dispatch_comparisons_or_assignments": len(dispatch), "areas": dict(sorted(areas.items())), "kinds": dict(sorted(kinds.items())), "traversal_errors": errors}
    (DOCS / "INVENTORY_METADATA.json").write_text(json.dumps(summary, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    lines = ["# Current source inventory", "", f"Generated on **{args.date}** by `DUCS/tools/rebuild_indexes.py`. `.git/` and `DUCS/` are excluded. No application handler is executed and no database rows are read.", "", f"- Readable files: **{len(manifest):,}**.", f"- PHP files: **{len(php_rows):,}** (including bundled libraries and upload-directory PHP).", f"- Distinct statically referenced `tbl_*` names: **{summary['static_tables']}**.", f"- Lexically extracted `do` comparisons/assignments: **{len(dispatch)}**.", f"- Traversal errors: **{len(errors)}**. Previously inaccessible `asset/img/` is readable in this review.", "", "| Area | Files |", "| --- | ---: |"]
    lines.extend(f"| `{area}` | {count:,} |" for area, count in sorted(areas.items()))
    lines += ["", "| Kind | Files |", "| --- | ---: |"]
    lines.extend(f"| {name} | {count:,} |" for name, count in sorted(kinds.items()))
    lines += ["", "The TSV files are static navigation aids, not a PHP parser or an authorization/schema guarantee. Comments and string literals can produce false positives. Dynamically built SQL/include paths can be omitted. `LITERAL_INCLUDE_AUDIT.tsv` resolves complete quoted paths relative to each source file; PHP's runtime working directory/include_path can differ, so `missing_candidate` needs source tracing before being called a broken dependency.", "", "Regenerate with:", "", "```bash", f"rtk proxy python3 DUCS/tools/rebuild_indexes.py --date {args.date}", "```", ""]
    (DOCS / "INVENTORY_SUMMARY.md").write_text("\n".join(lines), encoding="utf-8")
    print(json.dumps(summary, ensure_ascii=False))


if __name__ == "__main__": main()
