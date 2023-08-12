<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\Fixture;

use Nette\Utils\FileSystem;

/**
 * @api
 */
final class StaticFixtureUpdater
{
    public static function updateFixtureContent(
        string $originalFilePath,
        string $changedContent,
        string $fixtureFilePath
    ): void {
        if (! getenv('UPDATE_TESTS') && ! getenv('UT')) {
            return;
        }

        $newOriginalContent = self::resolveNewFixtureContent($originalFilePath, $changedContent);

        FileSystem::write($fixtureFilePath, $newOriginalContent);
    }

    public static function updateExpectedFixtureContent(
        string $newOriginalContent,
        string $expectedFilePath
    ): void {
        if (! getenv('UPDATE_TESTS') && ! getenv('UT')) {
            return;
        }

        FileSystem::write($expectedFilePath, $newOriginalContent);
    }

    private static function resolveNewFixtureContent(
        string $originalFilePath,
        string $changedContent
    ): string {
        $originalContent = FileSystem::read($originalFilePath);

        if ($originalContent === $changedContent) {
            return $originalContent;
        }

        return $originalContent . '-----' . PHP_EOL . $changedContent;
    }
}
