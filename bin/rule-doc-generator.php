<?php

declare (strict_types=1);
namespace RuleDocGenerator202408;

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
// load scoped classes
$scoperAutoloadFilepath = __DIR__ . '/../vendor/scoper-autoload.php';
if (\file_exists($scoperAutoloadFilepath)) {
    require_once $scoperAutoloadFilepath;
    // tap into project autoload
    if (\file_exists(__DIR__ . '/../../../autoload.php')) {
        require_once __DIR__ . '/../../../autoload.php';
    }
}
$containerFactory = new ContainerFactory();
$container = $containerFactory->create();
$application = $container->make(\RuleDocGenerator202408\Symfony\Component\Console\Application::class);
$exitCode = $application->run();
exit($exitCode);
