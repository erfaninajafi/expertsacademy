# Phase 2 backup and rollback

## Verified private backups

Private root (outside Apache document root):

`/home/igx7/.codex/visualizations/2026/10/27/01a17c31-7846-7222-8dcc-72ce7dfc5b2a/.private/phase2_20261101`

| File | Size | SHA-256 |
| --- | --- | --- |
| `application.tar.zst` | 2,218,872,010 bytes | `d22347fc9c71d7f2a2d351d7d2f19389158b3611698f3fbe1219bf306d5d0096` |
| `database.sql` | 3,261,884 bytes | `e7ffc071a3d93ace6c45a78798fee69c832f52efa4d1fe3ff1e5a419b5e07e0e` |
| `pre-migration/database.sql` | 3,262,031 bytes | `2f9fdd9d3f0f18c6b8aa664c8bd1bd2642412955571923b173a421060afe09ec` |

The full application archive predates Phase 2 modifications. Compression/integrity tests passed, it was extracted into private `restore-check/expertsacademy`, and all 18,914 file/symlink entries were verified against `application-manifest.json`. Database backups were restored into fresh isolated schemas and every table’s row multiset hash/count matched; original databases were not overwritten. Verification manifests are beside each backup.

The additional pre-migration database checkpoint includes the unexplained legacy Firefox edit documented in `PHASE2_PREEXISTING_EDIT.json`. Original 13:48 backup and its comparison are retained; no silent baseline replacement occurred. Use the later checkpoint for a Phase 2 migration rollback if that edit must be retained. Any newer content requires another backup and reconciliation first.

## Safe restoration rehearsal

1. Stop application writes during a planned recovery, take another private backup of current files/data and obtain approval for any overwrite or deletion.
2. Verify the selected archive/dump hashes against the table above. Test `zstd -t` and list the tar archive. Never extract over the live project for a rehearsal.
3. Extract the application into a fresh private staging directory. Compare file SHA-256 and symlink targets with `application-manifest.json`; the already verified extraction is available for reference.
4. Create a **new** empty, isolated database, import the selected dump using a private MySQL client configuration or environment-provided credentials, then compare table counts and sorted per-row hashes. Do not put passwords in shell arguments, documentation or the web root.
5. Point a private test copy at the restored schema and verify CMS login, original routes, assets and stored content. Keep the protected original project/database untouched.
6. Only after review, plan a controlled file/database switch with a downtime window and reconciliation of changes made after the selected backup. A restore overwrites later data; this is a destructive operation and is not authorized by ordinary test commands.

All shell commands in this workspace use `rtk`. Archive inspection can use `rtk proxy zstd -t /PRIVATE/application.tar.zst` and `rtk proxy tar --zstd -tf /PRIVATE/application.tar.zst`. Use an explicit fresh destination when extracting. No recovery command in this phase drops or overwrites development/production schemas.

## Migration rollback boundaries

Migration 002 is additive: three new tables, reviewed component metadata, theme v2, template v2 and one ledger row. It neither converts old pages nor modifies legacy content. The delivered demo is a new unbound/unpublished page.

`Migrator::downDesignForTest()` is restricted to disposable `expertsacademy_studio_test_*` schemas. It removes Phase 2 tables/seeds/ledger while preserving Phase 1 tables and existing pages/revisions. Integration tests exercise down/up on both database engines. `downForTest()` resets all Studio tables in disposable fixtures only.

**Do not execute the down SQL against development.** It would destroy collected leads, proposals and lifecycle records. Likewise, removing metadata or assets can break new v2 drafts/publications. There is intentionally no destructive development rollback CLI. A real rollback requires a fresh backup, explicit review of new content, asset/code compatibility and a restore or forward migration plan. Keeping the additive tables while rolling back an unbound UI deployment can be safer, but must be evaluated against actual new records.

The installed 001 seed checksum is `ae00f8f1708d01811b7201397fb9dc4df7460131ca420dc63dde588bdabeb893`. Its SQL and original seed snapshot remain immutable. Never edit a migration checksum to bypass a mismatch; investigate and add a new migration instead.

## Existing environment limitation

XAMPP reports MariaDB 10.4.32 with older `mysql.proc`/`mysql.event` metadata layouts. The backup verified zero routines/events/triggers through direct metadata counts where information_schema inspection failed. Dumps include all application tables and restored successfully. No global database upgrade or system table repair was attempted; that is separate DBA work. The owner’s XAMPP services are left running.
