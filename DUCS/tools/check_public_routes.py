#!/usr/bin/env python3
"""Read-only local HTTP smoke check; stores hashes/counts, never response content."""
import argparse
import collections
import csv
import hashlib
from html.parser import HTMLParser
import json
from pathlib import Path
import re
import subprocess
import urllib.error
import urllib.parse
import urllib.request

ROOT = Path(__file__).resolve().parents[2]
BASE = "http://127.0.0.1/expertsacademy/"


class VisibleText(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.hidden = 0
        self.parts = []
        self.tags = collections.Counter()

    def handle_starttag(self, tag, attrs):
        self.tags[tag] += 1
        if tag in ("head", "script", "style", "noscript"): self.hidden += 1

    def handle_endtag(self, tag):
        if tag in ("head", "script", "style", "noscript"): self.hidden = max(0, self.hidden - 1)

    def handle_data(self, data):
        if not self.hidden: self.parts.append(data)


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl): return None


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--output", required=True)
    args = parser.parse_args()
    php = '''$_SERVER["HTTP_HOST"]="localhost"; require "include_site/config.php";
if($dbname!=="expertsacademy_dev")exit(2);
$db=new mysqli($dbserver,$dbusername,$dbpassword,$dbname);$db->set_charset("utf8mb4");
$db->query("SET TRANSACTION READ ONLY");$db->begin_transaction();
$q=$db->query("SELECT tbl_categories_code code,tbl_categories_lng locale,tbl_categories_url url,tbl_categories_index home FROM tbl_categories WHERE tbl_categories_type=4 ORDER BY tbl_categories_code,tbl_categories_lng");
echo json_encode($q->fetch_all(MYSQLI_ASSOC),JSON_THROW_ON_ERROR);$db->rollback();'''
    result = subprocess.run(["rtk", "proxy", "/opt/lampp/bin/php", "-r", php], cwd=ROOT, text=True, capture_output=True)
    if result.returncode: raise SystemExit("Cannot read development page routes; no HTTP checks performed.")
    routes, skipped = [], 0
    for page in json.loads(result.stdout):
        if page["locale"] not in ("fa", "en"): continue
        query = {"lng": page["locale"]}
        if str(page["home"]) != "1":
            if not page["url"]:
                skipped += 1
                continue
            query["section"] = page["url"]
        routes.append((page["code"], page["locale"], "home" if str(page["home"]) == "1" else "page", "index.php?" + urllib.parse.urlencode(query), 200))
    routes.extend([
        ("invalid", "fa", "invalid", "index.php?lng=fa&section=__documentation_review_missing_20261101__", 404),
        ("builder-api", "", "auth", "site_admin/page_builder_api.php?code=C57FBD6", 403),
        ("cms-ajax", "", "auth", "site_admin/ajax.php?do=select2ajax", 403),
        ("filemanager", "", "auth", "site_admin/asset/editor/tinymce/plugins/filemanager/config.php", 403),
        ("builder-page", "", "auth", "site_admin/page_builder.php?code=C57FBD6", 302),
        ("builder-css", "", "asset", "site_admin/asset/css/page-builder.css", 200),
        ("builder-js", "", "asset", "site_admin/asset/js/page-builder.js", 200),
    ])
    opener = urllib.request.build_opener(NoRedirect)
    rows = []
    for code, locale, kind, path, expected in routes:
        request = urllib.request.Request(BASE + path, headers={"User-Agent": "ExAcd documentation read-only review"})
        try:
            response = opener.open(request, timeout=15)
            status, body = response.status, response.read()
        except urllib.error.HTTPError as error:
            status, body = error.code, error.read()
        except OSError:
            status, body = 0, b""
        text = body.decode("utf-8", errors="replace")
        visible = VisibleText()
        visible.feed(text)
        normalized = " ".join(" ".join(visible.parts).split())
        php_error = bool(re.search(r"(?:<b>)?(?:Fatal error|Warning|Notice|Parse error)(?:</b>)?:", text))
        rows.append([code, locale, kind, status, expected, len(body), hashlib.sha256(normalized.encode()).hexdigest(), visible.tags["section"], visible.tags["form"], "yes" if php_error else "no"])
    with Path(args.output).open("w", newline="", encoding="utf-8") as file:
        writer = csv.writer(file, delimiter="\t", lineterminator="\n")
        writer.writerow(["code", "locale", "kind", "http_status", "expected_status", "bytes", "visible_text_sha256", "section_count", "form_count", "php_error_marker"])
        writer.writerows(rows)
    print(json.dumps({"checked": len(rows), "cms_page_routes": sum(r[2] in ("home", "page") for r in rows), "skipped_blank_slug": skipped, "status_mismatches": [{"code": r[0], "locale": r[1], "status": r[3], "expected": r[4]} for r in rows if r[3] != r[4]], "php_error_markers": sum(r[9] == "yes" for r in rows)}))


if __name__ == "__main__": main()
