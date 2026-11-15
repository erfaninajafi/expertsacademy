# Phase 1 — verified backup and rollback

Backup completed **before Phase 1 application modifications**, on 2026-11-01. No production/original source or database was written. This backup captures the current development CMS, including the owner's earlier Builder changes; it is not a reconstruction of the older production release.

## Private backup location

`/home/igx7/.codex/visualizations/2026/10/27/01a17c31-7846-7222-8dcc-72ce7dfc5b2a/.private/phase1_20261101`

This directory is outside Apache's web root. Archive/dump/manifest files have mode 0600 and private parent directories. They contain application configuration, uploads, account/content data and Git metadata; keep them private. Do not move them into the repository or an HTTP download directory.

| File | Bytes | SHA-256 |
| --- | --- | --- |
| `application.tar.zst` | 2,179,055,986 | `5fbabfa9c954945aa014f0e537dec3a6c87366f8e3a285c53f49d9e79b20e31c` |
| `database.sql` | 3,247,872 | `2c85f15670297dd7618856e6cfe0b7f9d8a01cc90cad1f7dbe09c5125e52d188` |

Supporting private evidence: `application-manifest.json`, `file-backup-verification.json`, `database-backup-verification.json`, and `legacy-http-before.tsv`. The full application archive includes all original regular files/symlinks, `.git`, uploads and configuration: **11,484 entries / 2,420,543,890 source bytes**. It does not claim to capture XAMPP/system service configuration outside the application directory.

## Integrity and restoration already verified

1. The compressed application archive passed integrity checking.
2. It was extracted into private `restore-check/expertsacademy/`. Every original file's size/SHA-256 and every symlink target matched the pre-change manifest; the verification records `restore_verified:true`.
3. The SQL dump was taken with table locks, utf8mb4 and hex blobs, including the existing MyISAM table. Credentials were supplied privately to the subprocess, not printed or placed in command arguments.
4. The dump was restored into newly created `expertsacademy_studio_test_backup`. All **27 original table** row counts and canonical row-multiset hashes matched the live pre-change database and remained unchanged during backup. No routines/events/triggers existed. The XAMPP system-metadata warning and direct zero-object checks are recorded in the private verification JSON.
5. After Phase 1, [preservation verification](PHASE1_PRESERVATION.json) again matched all 27 original tables. [HTTP comparison](PHASE1_COMPARISON.json) matched all 48 original measured routes.

The restored test schema has subsequently been used for automated fixture accounts and Studio migrations. The immutable SQL dump and its verification manifest remain the authoritative backup, not that now-mutated disposable test schema.

## Restore rehearsal on a new isolated target

Do not overwrite a running directory or populated database to rehearse restoration. Use a fresh private directory and a newly named isolated database. Verify the two hashes above before importing anything. Example archive check and extraction, with a fresh empty destination:

```bash
rtk proxy install -d -m 700 /tmp/ea-phase1-restore
rtk proxy zstd --test /home/igx7/.codex/visualizations/2026/10/27/01a17c31-7846-7222-8dcc-72ce7dfc5b2a/.private/phase1_20261101/application.tar.zst
rtk proxy tar --zstd -xf /home/igx7/.codex/visualizations/2026/10/27/01a17c31-7846-7222-8dcc-72ce7dfc5b2a/.private/phase1_20261101/application.tar.zst -C /tmp/ea-phase1-restore
```

Check each relative path against `application-manifest.json` using SHA-256 and symlink targets. The archive root is `expertsacademy/`. Do not serve the restored tree before reviewing credentials and reapplying the new hidden-metadata/SQL deny rules: those rules intentionally improve the pre-change state.

For a private socket-only test MySQL instance prepared as described in [setup](PHASE1_DEVELOPER_SETUP.md), use a **new, empty** schema:

```bash
rtk proxy /usr/bin/mysql --socket=/tmp/ea-studio-mysql8/mysql.sock -u root -e 'CREATE DATABASE expertsacademy_studio_test_restore CHARACTER SET utf8mb4 COLLATE utf8mb4_bin'
rtk proxy /usr/bin/mysql --socket=/tmp/ea-studio-mysql8/mysql.sock -u root expertsacademy_studio_test_restore < /home/igx7/.codex/visualizations/2026/10/27/01a17c31-7846-7222-8dcc-72ce7dfc5b2a/.private/phase1_20261101/database.sql
```

Compare restored tables to `tables_manifest` in the private database verification JSON using the same mysqli utf8mb4 row hashing method as `DUCS/tools/verify_phase1_preservation.php`. That tool is intentionally fixed to read the live development schema for post-change preservation; do not edit its target merely to run destructive tests. Successful import alone is not an integrity comparison.

## Rollback choices

### Stop serving a Studio publication

No development page is currently published through Studio, so there is nothing to revert publicly. For a future published bound page, an operator can clear only that page's `published_revision_id` and increment `lock_version` under a transaction/row lock with an expected-version condition. Confirm the exact database, page UUID, locale and current pointer first; record the previous pointer and operation. This returns the existing URL to the retained renderer without deleting Studio revisions or modifying old content. There is no unpublish UI/API in Phase 1.

### Roll back application integration, retain data

Review the four application changes in [the manifest](PHASE1_FILES.tsv). Restoring `index.php`, `header_site.inc.php` and `site_admin/header.inc.php` from the verified extracted backup disconnects Studio rendering/navigation. Keep the hidden metadata and SQL export protection in `.htaccess`. New tables and source can remain for inspection; removing them is unnecessary for this fallback. Preserve any Studio work created since backup before removing access.

### Schema reversal

`001_foundation.down.sql` drops only the eight new Studio tables in dependency order. It **destroys Studio drafts, revisions, audit history and metadata** and is not an appropriate casual development rollback. `Migrator::downForTest()` refuses every database outside the disposable test prefix; down/up has been verified on both database engines with legacy tables preserved.

For development/production reversal, first take a new verified backup/export of post-Phase-1 work and review the affected rows. Obtain explicit approval before executing a destructive table drop. The application provides no automatic destructive development migration command.

### Full database/application replacement

Replacing the development database or tree discards changes made after this backup, including new content and administrative actions. **Stop and report the affected target and expected loss, then obtain approval before replacement.** Restore into an isolated destination first, verify all hashes/table manifests, arrange maintenance access, and switch only the reviewed development target. Never import this dump over protected `expertsacademy` or write `/opt/lampp/htdocs/expertsacademy-original`.

Keep configuration consistent with the chosen restored development schema, preserve public URLs, and recheck authentication, representative CMS pages and the 48-route baseline. Retain the new access-deny rules independently of an application rollback. No full replacement, legacy table drop, service upgrade or production deployment was performed during Phase 1.

## Ongoing backup/deployment boundary

The files above are a verified pre-change snapshot, not an automated retention service. Take a fresh private database/files backup before future schema or publication migrations. MySQL DDL has implicit commits; a transaction cannot reverse partially applied DDL. Do not edit migration 001 after installation or attempt a MySQL/MariaDB service upgrade as part of retrying it. The documented private restore procedure remains the recovery path if a new migration cannot be reversed safely.
