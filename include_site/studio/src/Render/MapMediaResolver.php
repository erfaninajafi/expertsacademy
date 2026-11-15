<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Render;

/** Immutable id => asset map, loaded once per request. */
final class MapMediaResolver implements MediaResolver
{
    /** @param array<string,array{url:string,alt:string,width:int|null,height:int|null}> $map */
    public function __construct(private readonly array $map) {}

    public function resolve(?string $id): ?array
    {
        if ($id === null) {
            return null;
        }
        return $this->map[$id] ?? null;
    }
}
