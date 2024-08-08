<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\FileSystem;

use Nette\Loaders\RobotLoader;
use ReflectionClass;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleClassWithFilePath;

final class RuleDefinitionClassesFinder
{
    /**
     * @var string
     */
    private const DOCUMENTED_RULE_INTERFACE = DocumentedRuleInterface::class;

    /**
     * @param string[] $directories
     * @return array<string, string>
     */
    public function findInDirectories(array $directories): array
    {
        $robotLoader = new RobotLoader();
        $robotLoader->setTempDirectory(sys_get_temp_dir() . '/robot_loader_temp');
        $robotLoader->addDirectory(...$directories);
        $robotLoader->ignoreDirs[] = '*tests*';
        $robotLoader->ignoreDirs[] = '*Fixture*';
        $robotLoader->ignoreDirs[] = '*templates*';

        $robotLoader->rebuild();

        $classesByFilePath = [];
        foreach ($robotLoader->getIndexedClasses() as $class => $filePath) {
            if (! is_a($class, self::DOCUMENTED_RULE_INTERFACE, true)) {
                continue;
            }

            // skip abstract classes
            $reflectionClass = new ReflectionClass($class);
            if ($reflectionClass->isAbstract()) {
                continue;
            }

            $classesByFilePath[$filePath] = $class;
        }

        return $classesByFilePath;
    }

    /**
     * @param string[] $directories
     * @return RuleClassWithFilePath[]
     */
    public function findAndCreateRuleWithFilePaths(array $directories, string $workingDirectory): array
    {
        $classesByFilePath = $this->findInDirectories($directories);

        $desiredClasses = [];
        foreach ($classesByFilePath as $filePath => $class) {
            $isClassDeprecated = $this->isClassDeprecated($class);

            $relativeFilePath = PathsHelper::relativeFromDirectory($filePath, $workingDirectory);
            $desiredClasses[] = new RuleClassWithFilePath($class, $relativeFilePath, $isClassDeprecated);
        }

        usort(
            $desiredClasses,
            static fn (RuleClassWithFilePath $left, RuleClassWithFilePath $right): int => $left->getClass() <=> $right->getClass()
        );

        return $desiredClasses;
    }

    private function isClassDeprecated(string $class): bool
    {
        $reflectionClass = new ReflectionClass($class);

        return str_contains((string) $reflectionClass->getDocComment(), '@deprecated');
    }
}
