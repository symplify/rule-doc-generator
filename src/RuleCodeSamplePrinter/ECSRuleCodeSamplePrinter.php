<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\BadGoodCodeSamplePrinter;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\DiffCodeSamplePrinter;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ECSRuleCodeSamplePrinter implements RuleCodeSamplePrinterInterface
{
    public function __construct(
        private readonly BadGoodCodeSamplePrinter $badGoodCodeSamplePrinter,
        private readonly DiffCodeSamplePrinter $diffCodeSamplePrinter
    ) {
    }

    public function isMatch(string $class): bool
    {
        if (str_ends_with($class, 'Fixer')) {
            return true;
        }

        return str_ends_with($class, 'Sniff');
    }

    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample, RuleDefinition $ruleDefinition): array
    {
        if (is_a($ruleDefinition->getRuleClass(), 'PHP_CodeSniffer\Sniffs\Sniff', true)) {
            return $this->badGoodCodeSamplePrinter->print($codeSample);
        }

        return $this->diffCodeSamplePrinter->print($codeSample);
    }
}
