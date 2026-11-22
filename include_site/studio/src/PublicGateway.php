<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio;

use ExpertsAcademy\Studio\Render\MediaResolver;
use ExpertsAcademy\Studio\Repo\MediaRepository;
use ExpertsAcademy\Studio\Repo\PageRepository;
use ExpertsAcademy\Studio\Routing\PageRoute;

/** A published page resolved for the public request path. */
final class PublishedPage
{
    /** @param array<string,mixed> $document */
    public function __construct(
        private readonly array $document,
        private readonly MediaResolver $media
    ) {}

    /** @return array<string,mixed> */
    public function document(): array
    {
        return $this->document;
    }

    public function resolver(): MediaResolver
    {
        return $this->media;
    }

    public function title(): string
    {
        return (string) ($this->document['seo']['title'] ?: $this->document['title']);
    }

    public function description(): string
    {
        return (string) ($this->document['seo']['description'] ?? '');
    }

    /** @return list<string> */
    public function keywords(): array
    {
        return array_values(array_map('strval', $this->document['seo']['keywords'] ?? []));
    }

    public function locale(): string
    {
        return (string) $this->document['locale'];
    }
}

/**
 * Bridge between the legacy public router and the new renderer.
 *
 * Returns null unless a page is explicitly bound to the legacy category code
 * AND a revision has been published. Legacy rendering therefore remains the
 * default for every existing URL until an editor publishes a replacement.
 */
final class PublicGateway
{
    /**
     * Resolve a published Studio page by its public URL path.
     *
     * Draft, unpublished and archived pages are never returned.
     */
    public static function findByPath(
        string $fullPath,
        string $locale
    ): ?PublishedPage {
        if (!in_array($locale, ['fa', 'en'], true)) {
            return null;
        }

        try {
            // Only canonical, valid paths are accepted.
            if (PageRoute::normalize($fullPath, $locale) !== $fullPath) {
                return null;
            }
        } catch (Problem $error) {
            return null;
        }

        try {
            $document = (new PageRepository())->publishedForPath(
                $fullPath,
                $locale
            );

            if ($document === null) {
                return null;
            }

            $media = (new MediaRepository())->resolverFor($document);

            return new PublishedPage($document, $media);

        } catch (\Throwable $error) {
            Db::log(
                'public studio path lookup failed: ' .
                $error->getMessage()
            );

            return null;
        }
    }

    public static function find(string $legacyCode, string $locale): ?PublishedPage
    {
        if (preg_match('/\A[A-Za-z0-9_-]{1,64}\z/D', $legacyCode) !== 1 || !in_array($locale, ['fa', 'en'], true)) {
            return null;
        }
        try {
            $document = (new PageRepository())->publishedForLegacyCode($legacyCode, $locale);
            if ($document === null) {
                return null;
            }
            $media = (new MediaRepository())->resolverFor($document);
            return new PublishedPage($document, $media);
        } catch (\Throwable $error) {
            // A missing table or database fault must never take the public site down.
            Db::log('public studio lookup failed: ' . $error->getMessage());
            return null;
        }
    }
}
