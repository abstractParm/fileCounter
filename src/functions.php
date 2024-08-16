<?php

declare(strict_types=1);

function getDirectory(string $path): array
{
    if (!is_dir($path)) {
        return [];
    }

    return glob("$path/*");
}

function isNeededFile(string $path, array $fileNames): bool
{
    return (in_array(basename($path), $fileNames)) && is_readable($path);
}

function countNumbersInFile(string $path, array $separators): float
{
    if (!($content = file_get_contents($path))) {
        return 0;
    }
    $content = preg_replace($separators, " ", $content);
    $x = array_reduce(explode(" ", $content), function (int|float $carry, string $num): float {
        if (!is_numeric($num)) {
            return $carry;
        }
        return $carry + (float) $num;
    }, .0);

    return $x;
}

/**
 * @return string[]
 */
function findFiles(string $dirPath, array $neededFileNames): array
{
    $neededFiles = [];
    if (!$pathes = getDirectory($dirPath)) {
        return $neededFiles;
    }
    // для меньших затрат по памяти лучше извлекать из начала массива, чтобы полностью опустошать ранний уровень, а не идти по ветке
    // но тогда возрастут временные потери при array_reverse() array_pop() array_reverse()
    while ($path = array_pop($pathes)) {
        if (is_link($path)) {
            continue;
        } elseif (is_dir($path)) {
            foreach (getDirectory($path) as $subPath) {
                $pathes[] = $subPath;
            }
            continue;
        }
        if (isNeededFile($path, $neededFileNames)) {
            $neededFiles[] = $path;
        }
    }

    return $neededFiles;
}

function printMessage(string $message): void
{
    echo PHP_EOL . $message . PHP_EOL . PHP_EOL;
}
