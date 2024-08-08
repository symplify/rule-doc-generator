<?php

declare (strict_types=1);
namespace Symplify\RuleDocGenerator\Printer\Markdown;

use RuleDocGenerator202408\Nette\Utils\Strings;
use RuleDocGenerator202408\SebastianBergmann\Diff\Differ;
/**
 * @see \Symplify\RuleDocGenerator\Tests\MarkdownDiffer\MarkdownDifferTest
 */
final class MarkdownDiffer
{
    /**
     * @readonly
     * @var \SebastianBergmann\Diff\Differ
     */
    private $differ;
    /**
     * @var string
     * @see https://regex101.com/r/LE9Xwo/1
     */
    private const METADATA_REGEX = '#^(.*\\n){1}#';
    /**
     * @var string
     * @see https://regex101.com/r/yf7u2L/1
     */
    private const SPACE_AND_NEWLINE_REGEX = '#( ){1,}\\n#';
    public function __construct(Differ $differ)
    {
        $this->differ = $differ;
    }
    public function diff(string $old, string $new) : string
    {
        if ($old === $new) {
            return '';
        }
        $diff = $this->differ->diff($old, $new);
        $diff = $this->clearUnifiedDiffOutputFirstLine($diff);
        $diff = $this->removeTrailingWhitespaces($diff);
        return $this->warpToDiffCode($diff);
    }
    /**
     * Removes UnifiedDiffOutputBuilder generated pre-spaces " \n" => "\n"
     */
    private function removeTrailingWhitespaces(string $diff) : string
    {
        $diff = Strings::replace($diff, self::SPACE_AND_NEWLINE_REGEX, \PHP_EOL);
        return \rtrim($diff);
    }
    private function warpToDiffCode(string $content) : string
    {
        return '```diff' . \PHP_EOL . $content . \PHP_EOL . '```' . \PHP_EOL;
    }
    private function clearUnifiedDiffOutputFirstLine(string $diff) : string
    {
        return Strings::replace($diff, self::METADATA_REGEX, '');
    }
}
