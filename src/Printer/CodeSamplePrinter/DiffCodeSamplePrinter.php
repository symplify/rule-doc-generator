<?php

declare (strict_types=1);
namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Printer\Markdown\MarkdownDiffer;
final class DiffCodeSamplePrinter
{
    /**
     * @readonly
     * @var \Symplify\RuleDocGenerator\Printer\Markdown\MarkdownDiffer
     */
    private $markdownDiffer;
    public function __construct(MarkdownDiffer $markdownDiffer)
    {
        $this->markdownDiffer = $markdownDiffer;
    }
    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample) : array
    {
        $diffCode = $this->markdownDiffer->diff($codeSample->getBadCode(), $codeSample->getGoodCode());
        return [$diffCode];
    }
}
