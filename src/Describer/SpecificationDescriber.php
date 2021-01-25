<?php

declare(strict_types=1);

namespace Speicher210\OpenApiGenerator\Describer;

use Speicher210\OpenApiGenerator\Assert\Assert;

use function explode;
use function implode;
use function nl2br;

use const PHP_EOL;

final class SpecificationDescriber
{
    /**
     * @psalm-pure
     */
    public static function updateDescription(?string $existingText, string $newText): string
    {
        if ($existingText === null) {
            return $newText;
        }

        $existingLines = explode('<br>' . PHP_EOL, $existingText);
        Assert::isArray($existingLines);

        return nl2br(implode(PHP_EOL, [...$existingLines, $newText]), false);
    }
}
