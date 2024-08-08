<?php

declare (strict_types=1);
namespace Symplify\RuleDocGenerator\Printer;

use RuleDocGenerator202408\Nette\Utils\Strings;
use RuleDocGenerator202408\Symfony\Component\Yaml\Yaml;
final class NeonPrinter
{
    /**
     * @see https://regex101.com/r/r8DGyV/1
     * @var string
     */
    private const TAGS_REGEX = '#tags:\\s+\\-\\s+(?<tag>.*?)$#ms';
    /**
     * @param mixed[] $phpStanNeon
     */
    public function printNeon(array $phpStanNeon) : string
    {
        $printedContent = Yaml::dump($phpStanNeon, 1000);
        // inline single tags, dummy
        $printedContent = Strings::replace($printedContent, self::TAGS_REGEX, 'tags: [$1]');
        return \rtrim($printedContent) . \PHP_EOL;
    }
}
