<?php

declare(strict_types=1);

require 'vendor/autoload.php';

const DIR_PATH_OPT = "d",
    FILE_NAME_OPT = "file",
    REGEXP_SEPARATOR_OPT = "sep"
;

$options = getopt(DIR_PATH_OPT.":", [FILE_NAME_OPT."::", REGEXP_SEPARATOR_OPT."::"]);
if (!$dirPath = $options[DIR_PATH_OPT] ?? null) {
    return 1;
}
$fileName = $options[FILE_NAME_OPT] ?? "counter";
$separators = ($options[REGEXP_SEPARATOR_OPT] ?? null) ?: [];
$separators = array_merge((is_string($separators) ? [$separators] : $separators), ["\n", " +"]);
$separators = array_map(fn (string $sep) => "/$sep/i", $separators);
$counter = 0;

$callback = function ($path) use (&$counter, $fileName, $separators) {
    $counter += countNumbersInFile($path, $fileName, $separators);
};

walkDirectory($dirPath, $callback);

echo PHP_EOL . $counter . PHP_EOL . PHP_EOL;

return 0;
