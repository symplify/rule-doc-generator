<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\RuleDocGenerator\DirectoryToMarkdownPrinter;
use Symplify\RuleDocGenerator\Tests\AbstractTestCase;
use Symplify\RuleDocGenerator\Tests\Fixture\StaticFixtureUpdater;

final class DirectoryToMarkdownPrinterTest extends AbstractTestCase
{
    private DirectoryToMarkdownPrinter $directoryToMarkdownPrinter;

    protected function setUp(): void
    {
        $this->directoryToMarkdownPrinter = $this->make(DirectoryToMarkdownPrinter::class);
    }

    #[DataProvider('provideData')]
    public function test(string $directory, string $expectedFile, bool $shouldCategorize = false): void
    {
        $fileContent = $this->directoryToMarkdownPrinter->print(__DIR__, [$directory], $shouldCategorize, []);

        StaticFixtureUpdater::updateExpectedFixtureContent($fileContent, $expectedFile);

        $this->assertStringEqualsFile($expectedFile, $fileContent, $directory);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/PHPStan/Standard', __DIR__ . '/Expected/phpstan/phpstan_content.md'];

        yield [
            __DIR__ . '/Fixture/PHPStan/Configurable',
            __DIR__ . '/Expected/phpstan/configurable_phpstan_content.md',
        ];

        yield [__DIR__ . '/Fixture/PHPCSFixer/Standard', __DIR__ . '/Expected/php-cs-fixer/phpcsfixer_content.md'];

        yield [__DIR__ . '/Fixture/Rector/Standard', __DIR__ . '/Expected/rector/rector_content.md'];

        yield [__DIR__ . '/Fixture/Rector/Standard', __DIR__ . '/Expected/rector/rector_categorized.md', true];
    }
}
