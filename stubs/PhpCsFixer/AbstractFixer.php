<?php

namespace PhpCsFixer;

if (class_exists('PhpCsFixer\AbstractFixer')) {
    return;
}

use PhpCsFixer\Fixer\FixerInterface;

abstract class AbstractFixer implements FixerInterface
{
}
