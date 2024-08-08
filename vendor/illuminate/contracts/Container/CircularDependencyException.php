<?php

namespace RuleDocGenerator202408\Illuminate\Contracts\Container;

use Exception;
use RuleDocGenerator202408\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
