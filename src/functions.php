<?php

declare(strict_types=1);

function getDirectory(string $path): array
{
    if (!is_dir($path)) {
        return [];
    }

    return glob("$path/*");
}

function isNeededFile(string $path, string $fileName): bool
{
    return (basename($path) === $fileName) && is_readable($path);
}

function countNumbersInFile(string $path, string $fileName, array $separators): float
{
    if (!isNeededFile($path, $fileName) || !($content = file_get_contents($path))) {
        return 0;
    }
    var_dump($separators);
    var_dump($path, $content);
    $content = preg_replace($separators, " ", $content);
    var_dump($path, $content);
    $x = array_reduce(explode(" ", $content), function (int|float $carry, string $num): float {
        if (!is_numeric($num)) {
            return $carry;
        }
        return $carry + (float) $num;
    }, .0);

    return $x;
}

/**
 * @param callable(string) $userFileHandler
 */
function walkDirectory(string $dirPath, callable $userFileHandler): void
{
    if (!$pathes = getDirectory($dirPath)) {
        return;
    }
    while ($path = array_pop($pathes)) {
        if (is_link($path)) {
            continue;
        } elseif (is_dir($path)) {
            array_map(function (string $subPath) use (&$pathes): void {
                $pathes[] = $subPath;
            }, getDirectory($path));
            continue;
        }
        $userFileHandler($path);
    }
}
