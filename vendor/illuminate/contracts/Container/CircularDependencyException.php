<?php

namespace RuleDocGenerator202406\Illuminate\Contracts\Container;

use Exception;
use RuleDocGenerator202406\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
