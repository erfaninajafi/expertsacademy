<?php
    declare(strict_types=1);
    // This legacy cron entrypoint is intentionally disabled. Scheduled publishing
    // was removed from the editor and API. Keep an inert file so existing cron
    // configurations cannot publish queued revisions or produce PHP fatal errors.
    if (PHP_SAPI === 'cli') {
        echo "Scheduled publishing is disabled. Remove its cron entry.\n";
    }
