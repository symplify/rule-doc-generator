<?php

declare (strict_types=1);
namespace Symplify\RuleDocGenerator;

use RuleDocGenerator202403\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\FileSystem\ClassByTypeFinder;
use Symplify\RuleDocGenerator\Printer\RuleDefinitionsPrinter;
use Symplify\RuleDocGenerator\ValueObject\RuleClassWithFilePath;
/**
 * @see \Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\DirectoryToMarkdownPrinterTest
 */
final class DirectoryToMarkdownPrinter
{
    /**
     * @readonly
     * @var \Symplify\RuleDocGenerator\FileSystem\ClassByTypeFinder
     */
    private $classByTypeFinder;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Symplify\RuleDocGenerator\RuleDefinitionsResolver
     */
    private $ruleDefinitionsResolver;
    /**
     * @readonly
     * @var \Symplify\RuleDocGenerator\Printer\RuleDefinitionsPrinter
     */
    private $ruleDefinitionsPrinter;
    public function __construct(ClassByTypeFinder $classByTypeFinder, SymfonyStyle $symfonyStyle, \Symplify\RuleDocGenerator\RuleDefinitionsResolver $ruleDefinitionsResolver, RuleDefinitionsPrinter $ruleDefinitionsPrinter)
    {
        $this->classByTypeFinder = $classByTypeFinder;
        $this->symfonyStyle = $symfonyStyle;
        $this->ruleDefinitionsResolver = $ruleDefinitionsResolver;
        $this->ruleDefinitionsPrinter = $ruleDefinitionsPrinter;
    }
    /**
     * @param string[] $directories
     * @param string[] $skipTypes
     */
    public function print(string $workingDirectory, array $directories, ?int $categorizeLevel, array $skipTypes) : string
    {
        // 1. collect documented rules in provided path
        $documentedRuleClasses = $this->classByTypeFinder->findByType($workingDirectory, $directories, DocumentedRuleInterface::class);
        $documentedRuleClasses = $this->filterOutSkippedTypes($documentedRuleClasses, $skipTypes);
        $message = \sprintf('Found %d documented rule classes', \count($documentedRuleClasses));
        $this->symfonyStyle->note($message);
        $classes = \array_map(static function (RuleClassWithFilePath $ruleClassWithFilePath) : string {
            return $ruleClassWithFilePath->getClass();
        }, $documentedRuleClasses);
        $this->symfonyStyle->listing($classes);
        // 2. create rule definition collection
        $this->symfonyStyle->note('Resolving rule definitions');
        $ruleDefinitions = $this->ruleDefinitionsResolver->resolveFromClassNames($documentedRuleClasses);
        // 3. print rule definitions to markdown lines
        $this->symfonyStyle->note('Printing rule definitions');
        $markdownLines = $this->ruleDefinitionsPrinter->print($ruleDefinitions, $categorizeLevel);
        $fileContent = '';
        foreach ($markdownLines as $markdownLine) {
            $fileContent .= \trim($markdownLine) . \PHP_EOL . \PHP_EOL;
        }
        return \rtrim($fileContent) . \PHP_EOL;
    }
    /**
     * @param RuleClassWithFilePath[] $ruleClassWithFilePaths
     * @param string[] $skipTypes
     * @return RuleClassWithFilePath[]
     */
    private function filterOutSkippedTypes(array $ruleClassWithFilePaths, array $skipTypes) : array
    {
        if ($skipTypes === []) {
            return $ruleClassWithFilePaths;
        }
        return \array_filter($ruleClassWithFilePaths, static function (RuleClassWithFilePath $ruleClassWithFilePath) use($skipTypes) : bool {
            foreach ($skipTypes as $skipType) {
                if (\is_a($ruleClassWithFilePath->getClass(), $skipType, \true)) {
                    return \false;
                }
            }
            // nothing to skip
            return \true;
        });
    }
}
