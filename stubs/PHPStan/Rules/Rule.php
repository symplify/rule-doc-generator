<?php

namespace PHPStan\Rules;

// if exists
if (interface_exists(Rule::class)) {
    return;
}

interface Rule
{
}
