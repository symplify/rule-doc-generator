<?php

declare (strict_types=1);
namespace Symplify\RuleDocGenerator\Command;

use RuleDocGenerator202408\Nette\Utils\FileSystem;
use RuleDocGenerator202408\Symfony\Component\Console\Command\Command;
use RuleDocGenerator202408\Symfony\Component\Console\Input\InputArgument;
use RuleDocGenerator202408\Symfony\Component\Console\Input\InputInterface;
use RuleDocGenerator202408\Symfony\Component\Console\Input\InputOption;
use RuleDocGenerator202408\Symfony\Component\Console\Output\OutputInterface;
use RuleDocGenerator202408\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\RuleDocGenerator\DirectoryToMarkdownPrinter;
use Symplify\RuleDocGenerator\ValueObject\Option;
use RuleDocGenerator202408\Webmozart\Assert\Assert;
final class GenerateCommand extends Command
{
    /**
     * @readonly
     * @var \Symplify\RuleDocGenerator\DirectoryToMarkdownPrinter
     */
    private $directoryToMarkdownPrinter;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var string
     */
    private const README_PLACEHOLDER_START = '<!-- ruledoc-start -->';
    /**
     * @var string
     */
    private const README_PLACEHOLDER_END = '<!-- ruledoc-end -->';
    public function __construct(DirectoryToMarkdownPrinter $directoryToMarkdownPrinter, SymfonyStyle $symfonyStyle)
    {
        $this->directoryToMarkdownPrinter = $directoryToMarkdownPrinter;
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('generate');
        $this->setDescription('Generated Markdown documentation based on documented rules found in directory');
        $this->addArgument(Option::PATHS, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to directory of your project');
        $this->addOption(Option::OUTPUT_FILE, null, InputOption::VALUE_REQUIRED, 'Path to output generated markdown file', \getcwd() . '/docs/rules_overview.md');
        $this->addOption(Option::CATEGORIZE, null, InputOption::VALUE_REQUIRED, 'Group rules by namespace position');
        $this->addOption(Option::SKIP_TYPE, null, InputOption::VALUE_REQUIRED, 'Skip specific type in filter');
        $this->addOption(Option::README, null, InputOption::VALUE_NONE, 'Render contents to README using placeholders');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $paths = (array) $input->getArgument(Option::PATHS);
        $categorizeLevel = $input->getOption(Option::CATEGORIZE);
        if ($categorizeLevel !== null) {
            $categorizeLevel = (int) $categorizeLevel;
        }
        $skipTypes = (array) $input->getOption(Option::SKIP_TYPE);
        // dump markdown file
        $outputFilePath = (string) $input->getOption(Option::OUTPUT_FILE);
        $markdownFileDirectory = \dirname($outputFilePath);
        // ensure directory exists
        if (!\file_exists($markdownFileDirectory)) {
            FileSystem::createDir($markdownFileDirectory);
        }
        $markdownFileContent = $this->directoryToMarkdownPrinter->print($markdownFileDirectory, $paths, $categorizeLevel, $skipTypes);
        $isReadme = (bool) $input->getOption(Option::README);
        if ($isReadme) {
            $this->renderToReadme($markdownFileContent);
        } else {
            FileSystem::write($outputFilePath, $markdownFileContent);
            $this->symfonyStyle->success(\sprintf('File "%s" was created', $outputFilePath));
        }
        return self::SUCCESS;
    }
    private function renderToReadme(string $markdownFileContent) : void
    {
        $readmeFilepath = \getcwd() . '/README.md';
        Assert::fileExists($readmeFilepath);
        $readmeContents = FileSystem::read($readmeFilepath);
        Assert::contains($readmeContents, self::README_PLACEHOLDER_START);
        Assert::contains($readmeContents, self::README_PLACEHOLDER_END);
        /** @var string $readmeContents */
        $readmeContents = \preg_replace('#' . \preg_quote(self::README_PLACEHOLDER_START, '#') . '(.*?)' . \preg_quote(self::README_PLACEHOLDER_END, '#') . '#s', self::README_PLACEHOLDER_START . \PHP_EOL . $markdownFileContent . \PHP_EOL . self::README_PLACEHOLDER_END, $readmeContents);
        FileSystem::write($readmeFilepath, $readmeContents);
        $this->symfonyStyle->success('README.md was updated');
        $this->symfonyStyle->newLine();
    }
}
