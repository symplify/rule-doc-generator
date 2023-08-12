<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer;

use Nette\Utils\Strings;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\CodeSamplePrinter;
use Symplify\RuleDocGenerator\Text\KeywordHighlighter;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

final class RuleDefinitionsPrinter
{
    public function __construct(
        private readonly CodeSamplePrinter $codeSamplePrinter,
        private readonly KeywordHighlighter $keywordHighlighter,
    ) {
    }

    /**
     * @param RuleDefinition[] $ruleDefinitions
     * @return string[]
     */
    public function print(array $ruleDefinitions, bool $shouldCategorize): array
    {
        $ruleCount = count($ruleDefinitions);

        $lines = [];
        $lines[] = sprintf('# %d Rules Overview', $ruleCount);

        if ($shouldCategorize) {
            $ruleDefinitionsByCategory = $this->groupDefinitionsByCategory($ruleDefinitions);

            $categoryMenuLines = $this->createCategoryMenu($ruleDefinitionsByCategory);
            $lines = array_merge($lines, $categoryMenuLines);

            foreach ($ruleDefinitionsByCategory as $category => $ruleDefinitions) {
                $lines[] = '## ' . $category;
                $lines = $this->printRuleDefinitions($ruleDefinitions, $lines, $shouldCategorize);
            }
        } else {
            $lines = $this->printRuleDefinitions($ruleDefinitions, $lines, false);
        }

        return $lines;
    }

    /**
     * @param RuleDefinition[] $ruleDefinitions
     * @return array<string, RuleDefinition[]>
     */
    private function groupDefinitionsByCategory(array $ruleDefinitions): array
    {
        $ruleDefinitionsByCategory = [];

        // have a convention from namespace :)
        foreach ($ruleDefinitions as $ruleDefinition) {
            $category = $this->resolveCategory($ruleDefinition);
            $ruleDefinitionsByCategory[$category][] = $ruleDefinition;
        }

        ksort($ruleDefinitionsByCategory);

        return $ruleDefinitionsByCategory;
    }

    /**
     * @param RuleDefinition[] $ruleDefinitions
     * @param string[] $lines
     * @return string[]
     */
    private function printRuleDefinitions(array $ruleDefinitions, array $lines, bool $shouldCategorize): array
    {
        foreach ($ruleDefinitions as $ruleDefinition) {
            if ($shouldCategorize) {
                $lines[] = '### ' . $ruleDefinition->getRuleShortClass();
            } else {
                $lines[] = '## ' . $ruleDefinition->getRuleShortClass();
            }

            $lines[] = $this->keywordHighlighter->highlight($ruleDefinition->getDescription());

            if ($ruleDefinition->isConfigurable()) {
                $lines[] = ':wrench: **configure it!**';
            }

            $lines[] = '- class: [`' . $ruleDefinition->getRuleClass() . '`](' . $ruleDefinition->getRuleFilePath() . ')';

            $codeSampleLines = $this->codeSamplePrinter->print($ruleDefinition);
            $lines = array_merge($lines, $codeSampleLines);
        }

        return $lines;
    }

    /**
     * @param array<string, RuleDefinition[]> $ruleDefinitionsByCategory
     * @return string[]
     */
    private function createCategoryMenu(array $ruleDefinitionsByCategory): array
    {
        $lines = [];
        $lines[] = '<br>';
        $lines[] = '## Categories';

        foreach ($ruleDefinitionsByCategory as $category => $ruleDefinitions) {
            $categoryLink = strtolower(Strings::webalize($category));

            $lines[] = sprintf('- [%s](#%s) (%d)', $category, $categoryLink, count($ruleDefinitions));
        }

        $lines[] = '<br>';

        return $lines;
    }

    private function resolveCategory(RuleDefinition $ruleDefinition): string
    {
        $classNameParts = explode('\\', $ruleDefinition->getRuleClass());

        // get one namespace before last by convention
        array_pop($classNameParts);

        // @todo handle for Rector rules, to pop one more... @todo make configurable via CLI?

        $category = array_pop($classNameParts);
        Assert::string($category);

        return $category;
    }
}
