<?php

declare (strict_types=1);
namespace Symplify\RuleDocGenerator\DependencyInjection;

use RuleDocGenerator202408\Illuminate\Container\Container;
use ReflectionProperty;
use RuleDocGenerator202408\SebastianBergmann\Diff\Differ;
use RuleDocGenerator202408\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use RuleDocGenerator202408\Symfony\Component\Console\Application;
use RuleDocGenerator202408\Symfony\Component\Console\Input\ArrayInput;
use RuleDocGenerator202408\Symfony\Component\Console\Output\ConsoleOutput;
use RuleDocGenerator202408\Symfony\Component\Console\Output\NullOutput;
use RuleDocGenerator202408\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\RuleDocGenerator\Command\GenerateCommand;
use Symplify\RuleDocGenerator\Command\ValidateCommand;
/**
 * @api tests and bin file DI factory
 */
final class ContainerFactory
{
    public function create() : Container
    {
        $container = new Container();
        $container->singleton(Differ::class, static function () : Differ {
            $unifiedDiffOutputBuilder = new UnifiedDiffOutputBuilder('');
            // this is required to show full diffs from start to end
            $contextLinesReflectionProperty = new ReflectionProperty($unifiedDiffOutputBuilder, 'contextLines');
            $contextLinesReflectionProperty->setAccessible(\true);
            $contextLinesReflectionProperty->setValue($unifiedDiffOutputBuilder, 10000);
            return new Differ($unifiedDiffOutputBuilder);
        });
        $this->registerConsole($container);
        return $container;
    }
    private function registerConsole(Container $container) : void
    {
        $container->singleton(SymfonyStyle::class, static function () : SymfonyStyle {
            $input = new ArrayInput([]);
            $output = \defined('PHPUNIT_COMPOSER_INSTALL') ? new NullOutput() : new ConsoleOutput();
            return new SymfonyStyle($input, $output);
        });
        $container->singleton(Application::class, function (Container $container) : Application {
            $application = new Application();
            $generateCommand = $container->make(GenerateCommand::class);
            $application->add($generateCommand);
            $validateCommand = $container->make(ValidateCommand::class);
            $application->add($validateCommand);
            $this->propertyCallable($application, 'commands', static function (array $defaultCommands) {
                unset($defaultCommands['completion']);
                unset($defaultCommands['help']);
                return $defaultCommands;
            });
            return $application;
        });
    }
    private function propertyCallable(object $object, string $propertyName, callable $callable) : void
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);
        $reflectionProperty->setAccessible(\true);
        $value = $reflectionProperty->getValue($object);
        $modifiedValue = $callable($value);
        $reflectionProperty->setValue($object, $modifiedValue);
    }
}
