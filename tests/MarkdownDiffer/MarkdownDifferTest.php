<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\MarkdownDiffer;

use Symplify\RuleDocGenerator\Printer\Markdown\MarkdownDiffer;
use Symplify\RuleDocGenerator\Tests\AbstractTestCase;

final class MarkdownDifferTest extends AbstractTestCase
{
    public function test(): void
    {
        $markdownDiffer = $this->make(MarkdownDiffer::class);

        $currentDiff = $markdownDiffer->diff('old code', 'new code');
        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_diff.txt', $currentDiff);
    }
}
