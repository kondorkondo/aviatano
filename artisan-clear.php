<?php
function runArtisanCommand($command) {
    $output = shell_exec("php artisan $command 2>&1");
    if (strpos($output, 'error') !== false) {
        echo "Error with $command: $output\n";
        return false;
    }
    return true;
}

// Usage
runArtisanCommand('config:clear');
// ... etc