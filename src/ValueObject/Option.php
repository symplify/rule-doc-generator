<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const PATHS = 'paths';

    /**
     * @var string
     */
    public const OUTPUT_FILE = 'output-file';

    /**
     * @var string
     */
    public const CATEGORIZE = 'categorize';

    /**
     * @var string
     */
    public const SKIP_TYPE = 'skip-type';

    /**
     * @var string
     */
    public const README = 'readme';
}
