<?php

declare (strict_types=1);
namespace Symplify\RuleDocGenerator\FileSystem;

use RuleDocGenerator202408\Symfony\Component\Filesystem\Filesystem;
final class PathsHelper
{
    public static function relativeFromDirectory(string $filePath, string $directory) : string
    {
        $fileSystem = new Filesystem();
        $relativeFilePath = $fileSystem->makePathRelative((string) \realpath($filePath), (string) \realpath($directory));
        return \rtrim($relativeFilePath, '/');
    }
}
