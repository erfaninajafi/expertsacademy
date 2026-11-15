<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Tests;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\DesignTokens;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Repo\DesignSystemRepository;
use PHPUnit\Framework\TestCase;

final class DesignSystemTest extends TestCase
{
    public function testContrastRatioMatchesWcagReferenceValues(): void
    {
        self::assertSame(21.0, DesignTokens::contrast('#000000', '#ffffff'));
        self::assertSame(1.0, DesignTokens::contrast('#824822', '#824822'));
        self::assertGreaterThan(4.5, DesignTokens::contrast('#172b26', '#ffffff'));
        self::assertLessThan(4.5, DesignTokens::contrast('#e8c58b', '#ffffff'));
    }

    public function testCssExposesPhaseTwoVariables(): void
    {
        $css = DesignTokens::css(DesignTokens::defaults());
        foreach ([
            '--cms-font-weight-semibold',
            '--cms-font-fluid-max',
            '--cms-container-wide',
            '--cms-grid-gutter',
            '--cms-radius-pill',
            '--cms-shadow-lg',
            '--cms-gradient-dawn',
            '--cms-motion-slide-duration',
            '--cms-space-8',
            '--cms-rtl-font-scale',
            '--cms-info',
        ] as $variable) {
            self::assertStringContainsString($variable . ':', $css, 'Missing ' . $variable);
        }
    }

    public function testInvalidTokensAreRejected(): void
    {
        $tokens = DesignTokens::defaults();
        $tokens['colors']['light']['brand'] = 'red; background:url(javascript:alert(1))';
        $errors = DesignTokens::errors($tokens);
        self::assertArrayHasKey('tokens.colors.light.brand', $errors);

        $tokens = DesignTokens::defaults();
        $tokens['motion']['presets']['rise']['duration'] = 99999;
        self::assertArrayHasKey('tokens.motion.presets.rise.duration', DesignTokens::errors($tokens));
    }

    public function testDraftSavePublishLifecycle(): void
    {
        if (getenv('EA_STUDIO_DB_TESTS') !== '1') {
            self::markTestSkipped('Set EA_STUDIO_DB_TESTS=1 to run database integration tests.');
        }
        $repository = new DesignSystemRepository();
        $actor = 'phpunit-design';
        $previous = Db::one(
            "SELECT id FROM tbl_studio_themes WHERE locale = 'en' AND is_default = 1 ORDER BY version DESC LIMIT 1"
        );
        $draftId = null;
        $publication = null;
        try {
            $tokens = DesignTokens::defaults();
            $tokens['name'] = 'PHPUnit theme ' . bin2hex(random_bytes(3));
            $draft = $repository->create($tokens['name'], 'en', $tokens, $actor);
            $draftId = (string) $draft['id'];
            self::assertSame(1, (int) $draft['version']);
            self::assertSame('draft', $draft['status']);

            $tokens['colors']['light']['brand'] = '#123456';
            $saved = $repository->save($draftId, $tokens, $actor, 1);
            self::assertSame(2, (int) $saved['version']);
            self::assertSame('#123456', $saved['tokens']['colors']['light']['brand']);

            try {
                $repository->save($draftId, $tokens, $actor, 1);
                self::fail('A stale version must not overwrite a newer draft.');
            } catch (Problem $conflict) {
                self::assertSame(409, $conflict->status);
            }

            $publication = $repository->publish($draftId, $actor, 2);
            $theme = Db::one('SELECT is_default, tokens_id FROM tbl_studio_themes WHERE id = ?', [$publication['themeId']]);
            self::assertNotNull($theme);
            self::assertSame(1, (int) $theme['is_default']);

            $history = $repository->history('en');
            self::assertNotEmpty(array_filter($history, static fn (array $row): bool => $row['theme_id'] === $publication['themeId']));
        } finally {
            if ($publication !== null) {
                Db::run('DELETE FROM tbl_studio_theme_publications WHERE id = ?', [$publication['publicationId']]);
                $theme = Db::one('SELECT tokens_id FROM tbl_studio_themes WHERE id = ?', [$publication['themeId']]);
                Db::run('DELETE FROM tbl_studio_themes WHERE id = ?', [$publication['themeId']]);
                if ($theme !== null) {
                    Db::run('DELETE FROM tbl_studio_design_tokens WHERE id = ?', [$theme['tokens_id']]);
                }
            }
            if ($draftId !== null) {
                Db::run('DELETE FROM tbl_studio_theme_publications WHERE draft_id = ?', [$draftId]);
                Db::run('DELETE FROM tbl_studio_theme_drafts WHERE id = ?', [$draftId]);
            }
            if ($previous !== null) {
                Db::run('UPDATE tbl_studio_themes SET is_default = 1 WHERE id = ?', [$previous['id']]);
            }
        }
    }
}
