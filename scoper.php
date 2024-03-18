<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$nowDateTime = new DateTime('now');
$timestamp = $nowDateTime->format('Ym');

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'RuleDocGenerator' . $timestamp,
    'expose-constants' => ['#^SYMFONY\_[\p{L}_]+$#'],
    'exclude-namespaces' => ['#^Symplify\\\\RuleDocGenerator#', '#^Symfony\\\\Polyfill#'],
    'exclude-files' => [
        // do not prefix "trigger_deprecation" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        'stubs/PhpCsFixer/AbstractFixer.php',
        'stubs/Rector/Core/Contract/Rector/RectorInterface.php',

        // check original types
        'src/RuleCodeSamplePrinter/ECSRuleCodeSamplePrinter.php',
        'src/RuleCodeSamplePrinter/PHPStanRuleCodeSamplePrinter.php',
        'src/RuleCodeSamplePrinter/RectorRuleCodeSamplePrinter.php',
    ],
];
