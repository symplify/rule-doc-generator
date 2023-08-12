<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\DependencyInjection;

use Illuminate\Container\Container;
use ReflectionProperty;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\RuleDocGenerator\Command\GenerateCommand;

/**
 * @api tests and bin file DI factory
 */
final class ContainerFactory
{
    public function create(): Container
    {
        $container = new Container();

        $container->singleton(Differ::class, static function (): Differ {
            $unifiedDiffOutputBuilder = new UnifiedDiffOutputBuilder('');

            // this is required to show full diffs from start to end
            $contextLinesReflectionProperty = new ReflectionProperty($unifiedDiffOutputBuilder, 'contextLines');
            $contextLinesReflectionProperty->setValue($unifiedDiffOutputBuilder, 10000);

            return new Differ($unifiedDiffOutputBuilder);
        });

        $this->registerConsole($container);

        return $container;
    }

    private function registerConsole(Container $container): void
    {
        $container->singleton(SymfonyStyle::class, static function (): SymfonyStyle {
            $input = new ArrayInput([]);
            $output = defined('PHPUNIT_COMPOSER_INSTALL') ? new NullOutput() : new ConsoleOutput();

            return new SymfonyStyle($input, $output);
        });

        $container->singleton(Application::class, static function (Container $container): Application {
            $application = new Application();

            $generateCommand = $container->make(GenerateCommand::class);
            $application->add($generateCommand);

            return $application;
        });
    }
}
