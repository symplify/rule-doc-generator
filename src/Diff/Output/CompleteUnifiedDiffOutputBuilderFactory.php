<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Diff\Output;

use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

/**
 * Creates @see UnifiedDiffOutputBuilder with "$contextLines = 10000;"
 */
final class CompleteUnifiedDiffOutputBuilderFactory
{
    public function create(): UnifiedDiffOutputBuilder
    {
        $unifiedDiffOutputBuilder = new UnifiedDiffOutputBuilder('');

        // this is required to show full diffs from start to end
        $contextLinesReflectionProperty = new \ReflectionProperty($unifiedDiffOutputBuilder, 'contextLines');
        $contextLinesReflectionProperty->setValue($unifiedDiffOutputBuilder, 10000);

        return $unifiedDiffOutputBuilder;
    }
}
