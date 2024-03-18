<?php

declare (strict_types=1);
namespace RuleDocGenerator202403;

// 1. autoload
use Symplify\RuleDocGenerator\DependencyInjection\ContainerFactory;
$possibleAutoloadPaths = [
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // dependency
    __DIR__ . '/../../../autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];
foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (\file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;
        break;
    }
}
$containerFactory = new ContainerFactory();
$container = $containerFactory->create();
$application = $container->make(\RuleDocGenerator202403\Symfony\Component\Console\Application::class);
$exitCode = $application->run();
exit($exitCode);
