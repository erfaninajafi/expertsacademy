# Local XAMPP database isolation and project switching

## Current local state (verified 2026-11-01)

- Active improved source is `/opt/lampp/htdocs/expertsacademy`; it uses `expertsacademy_dev`.
- Protected original source is `/opt/lampp/htdocs/expertsacademy-original`; the protected original database is `expertsacademy`.
- The Desktop-to-XAMPP switch described below already happened. The folder-switch instructions are historical operating procedures, not an outstanding setup step.
- The development database was originally cloned from the local original. It now has 27 tables, including two Builder tables, and later independent edits. The older statement that both copies still have identical 25-table data is no longer current. See [the live snapshot](DEVELOPMENT_SCHEMA_2026-11-01.json).
- Local base URL is `http://localhost/expertsacademy/`. Databases and media files do not synchronize between copies.

### MySQL service conflict

On 2026-11-01, the separate system `mysql` service occupied port 3306 again. XAMPP printed MySQL `ok`, but the configured socket failed and the site returned 500. The owner stopped the system service and started XAMPP MySQL; the project connection then succeeded. `lampp status` as an unprivileged user can also misread PID-file permissions. Check a real connection rather than a stale socket filename or startup message. [Setup and operations](SETUP_AND_OPERATIONS.md) records diagnosis and commands. No permanent service/autostart setting was changed.

## One active web path

Keep the active version at `/opt/lampp/htdocs/expertsacademy`. XAMPP's URL is `http://localhost/expertsacademy/`. The local virtual host `http://expertsacademy.local/` also points at this path, but the improved database's base URL currently uses the `localhost` URL.

The active project is writable by the development user; protected sibling directories can require administrator access to rename or move. Namespace-mapped ownership names can differ between the task environment and the host, so inspect host permissions before changing folders. The Linux `sudo` password is your Linux account password, not the MySQL `root` password. Do not delete or overwrite either version during a switch.

### Historical first activation of the improved version

1. Close project tabs in the browser and finish any ongoing uploads or form requests.
2. Rename `/opt/lampp/htdocs/expertsacademy` to `/opt/lampp/htdocs/expertsacademy-original`.
3. Move `/home/igx7/Desktop/expertsacademy-improved-20261025` to `/opt/lampp/htdocs/expertsacademy`.
4. Browse `http://localhost/expertsacademy/` and reload the page. MySQL remains running; no database command is needed.

After that first switch, the inactive version can remain at `/opt/lampp/htdocs/expertsacademy-original` or `/opt/lampp/htdocs/expertsacademy-improved`. Keep the name of the active directory exactly `expertsacademy`.

### Switch back to the original

1. Rename the active `/opt/lampp/htdocs/expertsacademy` to `/opt/lampp/htdocs/expertsacademy-improved`.
2. Rename `/opt/lampp/htdocs/expertsacademy-original` to `/opt/lampp/htdocs/expertsacademy`.
3. Reload `http://localhost/expertsacademy/` (a hard refresh helps if CSS or JavaScript is cached).

### Switch to the improved version again

1. Rename the active `/opt/lampp/htdocs/expertsacademy` to `/opt/lampp/htdocs/expertsacademy-original`.
2. Rename `/opt/lampp/htdocs/expertsacademy-improved` to `/opt/lampp/htdocs/expertsacademy`.
3. Reload the browser. The improved version automatically connects to `expertsacademy_dev`.

If a rename fails because the target directory already exists, inspect the names and preserve both folders; do not merge or overwrite them. Apache normally does not need a restart for a folder rename because its DocumentRoot stays the same. A browser hard refresh or a new private window can avoid stale assets or sessions after a switch.

## Verify the active connection

Read the active file `/opt/lampp/htdocs/expertsacademy/include_site/config.php`:

- `$dbname = 'expertsacademy';` means the original local database is selected.
- `$dbname = 'expertsacademy_dev';` means the independent clone is selected.

For a read-only MySQL check, run `/opt/lampp/bin/mysql -u root -e "SHOW DATABASES LIKE 'expertsacademy%';"` on this XAMPP installation. Do not edit the source database when checking the improved version.

## Limits of isolation

The database clone isolates only requests that actually use the improved project's primary `Query()` connection. Active public and panel AJAX URLs were changed to stay on the current host. The project still contains links to the external ExAcd login, external services, and flows using `QueryExpert()` whose separate credentials are absent from this snapshot. `register.php` also makes active SMS calls, which may send real messages even while the database is cloned. Avoid testing those external or unsupported flows as though they were local. The source snapshot also lacks legacy `include/` and root `components/` dependencies; some member and panel flows may fail even with the cloned database. See `ARCHITECTURE.md`, `DATABASE.md`, and `IMPROVEMENTS_2026-10-26.md` for the audit details.

At clone time, the local XAMPP MySQL server had an older system-table mismatch (`mysql.proc`) and its event scheduler is disabled. The source database contains no stored routines or events, and no triggers were present in either schema. The clone succeeded as a table/data dump despite the server metadata issue. That historical issue was not re-tested in November; verify server metadata separately before relying on stored routines/events.
