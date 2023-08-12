<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\Finder\ClassByTypeFinder;
use Symplify\RuleDocGenerator\Printer\RuleDefinitionsPrinter;
use Symplify\RuleDocGenerator\ValueObject\RuleClassWithFilePath;

/**
 * @see \Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\DirectoryToMarkdownPrinterTest
 */
final class DirectoryToMarkdownPrinter
{
    public function __construct(
        private readonly ClassByTypeFinder $classByTypeFinder,
        private readonly SymfonyStyle $symfonyStyle,
        private readonly RuleDefinitionsResolver $ruleDefinitionsResolver,
        private readonly RuleDefinitionsPrinter $ruleDefinitionsPrinter,
    ) {
    }

    /**
     * @param string[] $directories
     * @param string[] $skipTypes
     */
    public function print(string $workingDirectory, array $directories, bool $shouldCategorize, array $skipTypes): string
    {
        // 1. collect documented rules in provided path
        $documentedRuleClasses = $this->classByTypeFinder->findByType(
            $workingDirectory,
            $directories,
            DocumentedRuleInterface::class
        );

        $documentedRuleClasses = $this->filterOutSkippedTypes($documentedRuleClasses, $skipTypes);

        $message = sprintf('Found %d documented rule classes', count($documentedRuleClasses));
        $this->symfonyStyle->note($message);

        $classes = array_map(
            static fn (RuleClassWithFilePath $ruleClassWithFilePath): string => $ruleClassWithFilePath->getClass(),
            $documentedRuleClasses
        );

        $this->symfonyStyle->listing($classes);

        // 2. create rule definition collection
        $this->symfonyStyle->note('Resolving rule definitions');

        $ruleDefinitions = $this->ruleDefinitionsResolver->resolveFromClassNames($documentedRuleClasses);

        // 3. print rule definitions to markdown lines
        $this->symfonyStyle->note('Printing rule definitions');
        $markdownLines = $this->ruleDefinitionsPrinter->print($ruleDefinitions, $shouldCategorize);

        $fileContent = '';
        foreach ($markdownLines as $markdownLine) {
            $fileContent .= trim($markdownLine) . PHP_EOL . PHP_EOL;
        }

        return rtrim($fileContent) . PHP_EOL;
    }

    /**
     * @param RuleClassWithFilePath[] $ruleClassWithFilePaths
     * @param string[] $skipTypes
     * @return RuleClassWithFilePath[]
     */
    private function filterOutSkippedTypes(array $ruleClassWithFilePaths, array $skipTypes): array
    {
        if ($skipTypes === []) {
            return $ruleClassWithFilePaths;
        }

        return array_filter(
            $ruleClassWithFilePaths,
            static function (RuleClassWithFilePath $ruleClassWithFilePath) use ($skipTypes): bool {
                foreach ($skipTypes as $skipType) {
                    if ($ruleClassWithFilePath instanceof $skipType) {
                        return false;
                    }
                }

                // nothing to skip
                return true;
            }
        );
    }
}
