<?php

declare (strict_types=1);
namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Printer\Markdown\MarkdownCodeWrapper;
final class BadGoodCodeSamplePrinter
{
    /**
     * @readonly
     * @var \Symplify\RuleDocGenerator\Printer\Markdown\MarkdownCodeWrapper
     */
    private $markdownCodeWrapper;
    public function __construct(MarkdownCodeWrapper $markdownCodeWrapper)
    {
        $this->markdownCodeWrapper = $markdownCodeWrapper;
    }
    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample) : array
    {
        return [$this->markdownCodeWrapper->printPhpCode($codeSample->getBadCode()), ':x:', '<br>', $this->markdownCodeWrapper->printPhpCode($codeSample->getGoodCode()), ':+1:'];
    }
}
