<?php
// reset.php

echo "---- Resetting Laravel Authentication ----\n";

// Run Artisan commands
function run($cmd) {
    echo "Running: $cmd\n";
    echo shell_exec("php artisan $cmd 2>&1");
}

// Clear all caches
run('config:clear');
run('cache:clear');
run('route:clear');
run('view:clear');

// Generate new app key
run('key:generate --force');

// Run composer dump-autoload
echo "Running: composer dump-autoload\n";
echo shell_exec("composer dump-autoload 2>&1");

// Run migrations fresh (WARNING: this deletes all DB data!)
run('migrate:fresh --seed');

echo "✅ Done! Authentication reset completed.\n";
