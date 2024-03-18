<?php

declare (strict_types=1);
namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\BadGoodCodeSamplePrinter;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\DiffCodeSamplePrinter;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
final class ECSRuleCodeSamplePrinter implements RuleCodeSamplePrinterInterface
{
    /**
     * @readonly
     * @var \Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\BadGoodCodeSamplePrinter
     */
    private $badGoodCodeSamplePrinter;
    /**
     * @readonly
     * @var \Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\DiffCodeSamplePrinter
     */
    private $diffCodeSamplePrinter;
    public function __construct(BadGoodCodeSamplePrinter $badGoodCodeSamplePrinter, DiffCodeSamplePrinter $diffCodeSamplePrinter)
    {
        $this->badGoodCodeSamplePrinter = $badGoodCodeSamplePrinter;
        $this->diffCodeSamplePrinter = $diffCodeSamplePrinter;
    }
    public function isMatch(string $class) : bool
    {
        if (\substr_compare($class, 'Fixer', -\strlen('Fixer')) === 0) {
            return \true;
        }
        return \substr_compare($class, 'Sniff', -\strlen('Sniff')) === 0;
    }
    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample, RuleDefinition $ruleDefinition) : array
    {
        if (\is_a($ruleDefinition->getRuleClass(), 'RuleDocGenerator202403\\PHP_CodeSniffer\\Sniffs\\Sniff', \true)) {
            return $this->badGoodCodeSamplePrinter->print($codeSample);
        }
        return $this->diffCodeSamplePrinter->print($codeSample);
    }
}
