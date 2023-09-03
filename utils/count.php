<?php

/**
 * Affiche le nombre de records (hash) se trouvant dans les fichiers ci-dessous mentionnés.
 */

try {
    // set_time_limit isn't used when safe_mode is active
    if (!get_cfg_var('safe_mode')) {
        // No max execution time
        @ini_set('max_execution_time', '0');
        // Remove time limit; avoid 504 HTTP errors
        @ini_set('set_time_limit', '0');
    }

    // Allocate the maximum allowed memory to the script (-1 = no limit)
    @ini_set('memory_limit', -1);
} catch (Exception $e) {
}

// Loop
$arrFiles=[
    __DIR__ . '/aesecure_quickscan_blacklist.json',
    __DIR__ . '/aesecure_quickscan_edited.json',
    __DIR__ . '/aesecure_quickscan_whitelist.json',
    __DIR__ . '/aesecure_quickscan_other.json',
];

$wTotal=0;
foreach ($arrFiles as $file) {
    if (file_exists($file)) {
        try {
            $arr = json_decode(file_get_contents($file, true), true);

            $wTotal += count($arr);

            echo '<h4>File ' . $file . ' has ' . count($arr) . ' entries</h4>';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}

echo '<hr/>';
echo '<h4>Total ' . $wTotal . ' entries</h4>';