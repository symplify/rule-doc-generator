<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter;

use Rector\Contract\Rector\RectorInterface;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\DiffCodeSamplePrinter;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class RectorRuleCodeSamplePrinter implements RuleCodeSamplePrinterInterface
{
    public function __construct(
        private readonly DiffCodeSamplePrinter $diffCodeSamplePrinter,
    ) {
    }

    public function isMatch(string $class): bool
    {
        return is_a($class, RectorInterface::class, true);
    }

    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample, RuleDefinition $ruleDefinition): array
    {
        return $this->diffCodeSamplePrinter->print($codeSample);
    }
}
