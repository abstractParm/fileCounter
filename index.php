<?php

declare(strict_types=1);

require 'vendor/autoload.php';

const DIR_PATH_OPT = "d",
    FILE_NAME_OPT = "file",
    REGEXP_SEPARATOR_OPT = "sep"
;

$options = getopt(DIR_PATH_OPT.":", [FILE_NAME_OPT."::", REGEXP_SEPARATOR_OPT."::"]);
if (!$dirPath = $options[DIR_PATH_OPT] ?? null) {
    printMessage("Set required option \033[01;31m".DIR_PATH_OPT."\033[0m to define initial directory");
    return 1;
}
$fileNames = $options[FILE_NAME_OPT] ?? "count";
if (is_string($fileNames)) {
    $fileNames = [$fileNames];
}
$separators = ($options[REGEXP_SEPARATOR_OPT] ?? null) ?: [];
$separators = array_merge((is_string($separators) ? [$separators] : $separators), ["\n", " +"]);
$separators = array_map(fn (string $sep) => "/$sep/i", $separators);

$counter = 0;
try {
    foreach (findFiles(realpath($dirPath), $fileNames) as $path) {
        $counter += countNumbersInFile($path, $separators);
    }
    printMessage(
        "Sum of nums in all files \x1b[1m\x1b[34m"
        . implode(" ", $fileNames)
        . "\x1b[0m: \x1b[4m$counter\x1b[0m"
    );

    return 0;
} catch (\Throwable $e) {
    printMessage("\033[31mError\033[0m" . PHP_EOL . "\t" . $e->getMessage());
    return 1;
}