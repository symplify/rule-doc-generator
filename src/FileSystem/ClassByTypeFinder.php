<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\FileSystem;

use Nette\Loaders\RobotLoader;
use ReflectionClass;
use Symplify\RuleDocGenerator\ValueObject\RuleClassWithFilePath;

final class ClassByTypeFinder
{
    /**
     * @param string[] $directories
     * @return RuleClassWithFilePath[]
     */
    public function findByType(string $workingDirectory, array $directories, string $type): array
    {
        $robotLoader = new RobotLoader();
        $robotLoader->setTempDirectory(sys_get_temp_dir() . '/robot_loader_temp');
        $robotLoader->addDirectory(...$directories);
        $robotLoader->ignoreDirs[] = '*tests*';
        $robotLoader->ignoreDirs[] = '*Fixture*';
        $robotLoader->ignoreDirs[] = '*templates*';

        $robotLoader->rebuild();

        $desiredClasses = [];
        foreach ($robotLoader->getIndexedClasses() as $class => $filePath) {
            if (! is_a($class, $type, true)) {
                continue;
            }

            // skip abstract classes
            $reflectionClass = new ReflectionClass($class);
            if ($reflectionClass->isAbstract()) {
                continue;
            }

            $relativeFilePath = PathsHelper::relativeFromDirectory($filePath, $workingDirectory);
            $desiredClasses[] = new RuleClassWithFilePath($class, $relativeFilePath);
        }

        usort(
            $desiredClasses,
            static fn (RuleClassWithFilePath $left, RuleClassWithFilePath $right): int => $left->getClass() <=> $right->getClass()
        );

        return $desiredClasses;
    }
}
