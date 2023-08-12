<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\RuleDocGenerator\DirectoryToMarkdownPrinter;
use Symplify\RuleDocGenerator\ValueObject\Option;

final class GenerateCommand extends Command
{
    public function __construct(
        private readonly DirectoryToMarkdownPrinter $directoryToMarkdownPrinter,
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('generate');

        $this->setDescription('Generated Markdown documentation based on documented rules found in directory');

        $this->addArgument(
            Option::PATHS,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directory of your project'
        );

        $this->addOption(
            Option::OUTPUT_FILE,
            null,
            InputOption::VALUE_REQUIRED,
            'Path to output generated markdown file',
            getcwd() . '/docs/rules_overview.md'
        );

        $this->addOption(Option::CATEGORIZE, null, InputOption::VALUE_NONE, 'Group in categories');
        $this->addOption('skip-type', null, InputOption::VALUE_REQUIRED, 'Skip specific type in filter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = (array) $input->getArgument(Option::PATHS);
        $shouldCategorize = (bool) $input->getOption(Option::CATEGORIZE);
        $skipTypes = (array) $input->getOption('skip-type');

        // dump markdown file
        $outputFilePath = (string) $input->getOption(Option::OUTPUT_FILE);

        $markdownFileDirectory = dirname($outputFilePath);

        // ensure directory exists
        if (! file_exists($markdownFileDirectory)) {
            FileSystem::createDir($markdownFileDirectory);
        }

        $markdownFileContent = $this->directoryToMarkdownPrinter->print(
            $markdownFileDirectory,
            $paths,
            $shouldCategorize,
            $skipTypes
        );

        FileSystem::write($outputFilePath, $markdownFileContent);

        $this->symfonyStyle->success(sprintf('File "%s" was created', $outputFilePath));

        return self::SUCCESS;
    }
}
