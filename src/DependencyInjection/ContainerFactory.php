<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\DependencyInjection;

use Illuminate\Container\Container;

final class ContainerFactory
{
    public static function create(): Container
    {
        $container = new Container();

        // ...

        return $container;
    }
}
