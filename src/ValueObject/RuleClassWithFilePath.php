<?php

declare (strict_types=1);
namespace Symplify\RuleDocGenerator\ValueObject;

final class RuleClassWithFilePath
{
    /**
     * @readonly
     * @var string
     */
    private $class;
    /**
     * @readonly
     * @var string
     */
    private $path;
    /**
     * @readonly
     * @var bool
     */
    private $isDeprecated;
    public function __construct(string $class, string $path, bool $isDeprecated)
    {
        $this->class = $class;
        $this->path = $path;
        $this->isDeprecated = $isDeprecated;
    }
    public function getClass() : string
    {
        return $this->class;
    }
    public function getPath() : string
    {
        return $this->path;
    }
    public function isDeprecated() : bool
    {
        return $this->isDeprecated;
    }
}
