<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\DependencyInjection;

use Illuminate\Container\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PhpConfigPrinter\CaseConverter\AliasCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ClassServiceCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ConfiguredServiceCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ExtensionConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ImportCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\NameOnlyServiceCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ParameterCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ResourceCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ServicesDefaultsCaseConverter;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\Converter\ServiceOptionsKeyYamlToPhpFactory\TagsServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\NodeFactory\Service\ServiceOptionNodeFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\AbstractServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\ArgumentsServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\AutowiringTypesOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\BindAutowireAutoconfigureServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\CallsServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\DecoratesServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\DeprecatedServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\FactoryConfiguratorServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\ParentLazyServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\PropertiesServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\SharedPublicServiceOptionKeyYamlToPhpFactory;
use Symplify\RuleDocGenerator\CaseConverter\ECSRuleCaseConverter;
use Symplify\RuleDocGenerator\CaseConverter\RectorRuleCaseConverter;
use Symplify\RuleDocGenerator\Command\GenerateCommand;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\CodeSamplePrinter;
use Symplify\RuleDocGenerator\RuleCodeSamplePrinter\ECSRuleCodeSamplePrinter;
use Symplify\RuleDocGenerator\RuleCodeSamplePrinter\PHPStanRuleCodeSamplePrinter;
use Symplify\RuleDocGenerator\RuleCodeSamplePrinter\RectorRuleCodeSamplePrinter;

final class ContainerFactory
{
    public function create(): Container
    {
        $container = new Container();

        $this->registerConsole($container);
        $this->registerPrinter($container);
        $this->registerCaseConverters($container);

        $container->singleton(TagsServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(TagsServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(AbstractServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(AbstractServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(ArgumentsServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(ArgumentsServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(AutowiringTypesOptionKeyYamlToPhpFactory::class);
        $container->tag(AutowiringTypesOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(BindAutowireAutoconfigureServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(BindAutowireAutoconfigureServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(CallsServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(CallsServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(DecoratesServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(DecoratesServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(DeprecatedServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(DeprecatedServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(FactoryConfiguratorServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(FactoryConfiguratorServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(ParentLazyServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(ParentLazyServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(PropertiesServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(PropertiesServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);
        $container->singleton(SharedPublicServiceOptionKeyYamlToPhpFactory::class);
        $container->tag(SharedPublicServiceOptionKeyYamlToPhpFactory::class, ServiceOptionsKeyYamlToPhpFactoryInterface::class);

        $container->singleton(ServiceOptionNodeFactory::class);
        $container->when(ServiceOptionNodeFactory::class)
            ->needs('$serviceOptionKeyYamlToPhpFactories')
            ->giveTagged(ServiceOptionsKeyYamlToPhpFactoryInterface::class);

        return $container;
    }

    private function registerConsole(Container $container): void
    {
        $container->singleton(SymfonyStyle::class, static function (): SymfonyStyle {
            $input = new ArrayInput([]);
            $output = new ConsoleOutput();
            return new SymfonyStyle($input, $output);
        });

        $container->singleton(Application::class, static function (Container $container): Application {
            $application = new Application();
            $generateCommand = $container->make(GenerateCommand::class);
            $application->add($generateCommand);
            return $application;
        });
    }

    private function registerPrinter(Container $container): void
    {
        $container->singleton(ECSRuleCodeSamplePrinter::class);
        $container->tag(ECSRuleCodeSamplePrinter::class, RuleCodeSamplePrinterInterface::class);

        $container->singleton(PHPStanRuleCodeSamplePrinter::class);
        $container->tag(PHPStanRuleCodeSamplePrinter::class, RuleCodeSamplePrinterInterface::class);

        $container->singleton(RectorRuleCodeSamplePrinter::class);
        $container->tag(RectorRuleCodeSamplePrinter::class, RuleCodeSamplePrinterInterface::class);

        // printer
        $container->singleton(CodeSamplePrinter::class);
        $container->when(CodeSamplePrinter::class)
            ->needs('$ruleCodeSamplePrinters')
            ->giveTagged(RuleCodeSamplePrinterInterface::class);
    }

    private function registerCaseConverters(Container $container): void
    {
        $container->singleton(ECSRuleCaseConverter::class);
        $container->tag(ECSRuleCaseConverter::class, CaseConverterInterface::class);

        $container->singleton(RectorRuleCaseConverter::class);
        $container->tag(RectorRuleCaseConverter::class, CaseConverterInterface::class);

        $container->singleton(AliasCaseConverter::class);
        $container->tag(AliasCaseConverter::class, CaseConverterInterface::class);

        $container->singleton(ClassServiceCaseConverter::class);
        $container->tag(ClassServiceCaseConverter::class, CaseConverterInterface::class);

        $container->singleton(ConfiguredServiceCaseConverter::class);
        $container->tag(ConfiguredServiceCaseConverter::class, CaseConverterInterface::class);

        $container->singleton(ExtensionConverter::class);
        $container->tag(ExtensionConverter::class, CaseConverterInterface::class);

        $container->singleton(ImportCaseConverter::class);
        $container->tag(ImportCaseConverter::class, CaseConverterInterface::class);

        $container->singleton(NameOnlyServiceCaseConverter::class);
        $container->tag(NameOnlyServiceCaseConverter::class, CaseConverterInterface::class);

        $container->singleton(ParameterCaseConverter::class);
        $container->tag(ParameterCaseConverter::class, CaseConverterInterface::class);

        $container->singleton(ResourceCaseConverter::class);
        $container->tag(ResourceCaseConverter::class, CaseConverterInterface::class);

        $container->singleton(ServicesDefaultsCaseConverter::class);
        $container->tag(ServicesDefaultsCaseConverter::class, CaseConverterInterface::class);

        $container->singleton(ContainerConfiguratorReturnClosureFactory::class);
        $container->when(ContainerConfiguratorReturnClosureFactory::class)
            ->needs('$caseConverters')
            ->giveTagged(CaseConverterInterface::class);
    }
}
