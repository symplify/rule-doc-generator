<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\FileSystem;

final class PathsHelper
{
    public static function relativeFromCwd(string $filePath): string
    {
        $relativeFilePath = (string) realpath($filePath);

        return str_replace(getcwd() . DIRECTORY_SEPARATOR, '', $relativeFilePath);
    }
}
