<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Render;

/** Used by previews and tests: media references render a labelled placeholder. */
final class NullMediaResolver implements MediaResolver
{
    public function resolve(?string $id): ?array
    {
        return null;
    }
}
