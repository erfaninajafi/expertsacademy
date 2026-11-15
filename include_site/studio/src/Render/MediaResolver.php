<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Render;

/** Resolves a media UUID to a public asset. Implementations may hit the DB or a map. */
interface MediaResolver
{
    /** @return array{url:string,alt:string,width:int|null,height:int|null}|null */
    public function resolve(?string $id): ?array;
}
