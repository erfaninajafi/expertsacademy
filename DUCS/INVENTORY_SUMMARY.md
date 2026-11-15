# Current source inventory

Generated on **2026-11-01** by `DUCS/tools/rebuild_indexes.py`. `.git/` and `DUCS/` are excluded. No application handler is executed and no database rows are read.

- Readable files: **7,565**.
- PHP files: **453** (including bundled libraries and upload-directory PHP).
- Distinct statically referenced `tbl_*` names: **72**.
- Lexically extracted `do` comparisons/assignments: **1216**.
- Traversal errors: **0**. Previously inaccessible `asset/img/` is readable in this review.

| Area | Files |
| --- | ---: |
| `(root)` | 34 |
| `.well-known` | 1 |
| `asset` | 69 |
| `asset_site` | 254 |
| `company` | 1,749 |
| `components_site` | 37 |
| `include_site` | 10 |
| `lib` | 12 |
| `site_admin` | 969 |
| `sys_panel2` | 1,608 |
| `sys_panel3` | 1,826 |
| `upload_site` | 640 |
| `uploadfile_site` | 85 |
| `vendors` | 271 |

| Kind | Files |
| --- | ---: |
| Application PHP | 251 |
| Bundled NuSOAP PHP | 12 |
| Bundled asset/library | 6,060 |
| Bundled library PHP | 138 |
| Frontend source/asset | 254 |
| Image asset | 69 |
| Other file | 8 |
| SQL dump | 1 |
| Site bootstrap/helper/language | 10 |
| Site component template | 37 |
| Upload directory PHP | 5 |
| Uploaded/public media | 720 |

The TSV files are static navigation aids, not a PHP parser or an authorization/schema guarantee. Comments and string literals can produce false positives. Dynamically built SQL/include paths can be omitted. `LITERAL_INCLUDE_AUDIT.tsv` resolves complete quoted paths relative to each source file; PHP's runtime working directory/include_path can differ, so `missing_candidate` needs source tracing before being called a broken dependency.

Regenerate with:

```bash
rtk proxy python3 DUCS/tools/rebuild_indexes.py --date 2026-11-01
```
