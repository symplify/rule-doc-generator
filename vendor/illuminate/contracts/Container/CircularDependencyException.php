<?php

namespace RuleDocGenerator202403\Illuminate\Contracts\Container;

use Exception;
use RuleDocGenerator202403\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
