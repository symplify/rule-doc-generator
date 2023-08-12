<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\FileSystem;

final class PathsHelper
{
    public static function relativeFromDirectory(string $filePath, string $directory): string
    {
        $relativeFilePath = (string) realpath($filePath);

        return str_replace($directory . DIRECTORY_SEPARATOR, '', $relativeFilePath);
    }
}
