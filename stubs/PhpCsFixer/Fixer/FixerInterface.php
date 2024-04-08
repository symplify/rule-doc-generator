<?php

namespace PhpCsFixer\Fixer;

use PhpCsFixer\FixerDefinitionInterface;
use PhpCsFixer\Tokens;

if (class_exists('PhpCsFixer\FixerInterface')) {
    return;
}

interface FixerInterface
{
}
