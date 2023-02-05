<?php

declare(strict_types=1);


return static function (\Symplify\EasyCI\Config\EasyCIConfig $easyCIConfig): void {
    $easyCIConfig->paths([
        __DIR__ . '/src',
    ]);

    $easyCIConfig->typesToSkip([
        \Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface::class,
    ]);
};
