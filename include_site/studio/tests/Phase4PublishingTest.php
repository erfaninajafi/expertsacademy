<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Tests;

use ExpertsAcademy\Studio\Auth\Roles;
use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Publish\PublishScheduler;
use ExpertsAcademy\Studio\Quality\QualityChecker;
use ExpertsAcademy\Studio\Repo\PageRepository;
use ExpertsAcademy\Studio\Repo\PreviewTokenRepository;
use ExpertsAcademy\Studio\Repo\RedirectRepository;
use PHPUnit\Framework\TestCase;

/** Phase 4 publishing, scheduler, SEO, quality and media integration tests. */
final class Phase4PublishingTest extends TestCase
{
    private PageRepository $pages;
    private string $actor;
    private ?string $pageId = null;
    private string $binding;

    protected function setUp(): void
    {
        if (getenv('EA_STUDIO_DB_TESTS') !== '1') {
            self::markTestSkipped('Set EA_STUDIO_DB_TESTS=1 to run database integration tests.');
        }
        $user = Db::one('SELECT tbl_user_id FROM tbl_user WHERE tbl_user_status = 0 LIMIT 1');
        if ($user === null) {
            self::markTestSkipped('No administrator account available.');
        }
        $this->actor = (string) $user['tbl_user_id'];
        $this->pages = new PageRepository();
        $this->binding = 'zz_p4_' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        if ($this->pageId === null) {
            return;
        }
        $id = $this->pageId;
        Db::run('DELETE FROM tbl_studio_schedules WHERE page_id = ?', [$id]);
        Db::run('DELETE FROM tbl_studio_preview_tokens WHERE page_id = ?', [$id]);
        Db::run('DELETE FROM tbl_studio_media_usages WHERE revision_id IN (SELECT id FROM tbl_studio_revisions WHERE page_id = ?)', [$id]);
        Db::run('DELETE FROM tbl_studio_publications WHERE page_id = ?', [$id]);
        Db::run('DELETE FROM tbl_studio_events WHERE page_id = ?', [$id]);
        Db::run('UPDATE tbl_studio_pages SET draft_revision_id = NULL, published_revision_id = NULL WHERE id = ?', [$id]);
        Db::run('DELETE FROM tbl_studio_revisions WHERE page_id = ?', [$id]);
        Db::run('DELETE FROM tbl_studio_pages WHERE id = ?', [$id]);
        $this->pageId = null;
    }

    public function testScheduleCancelAndServerSidePublication(): void
    {
        $page = $this->pages->create(Samples::page('en'), $this->actor, $this->binding);
        $this->pageId = (string) $page['id'];
        $scheduled = $this->pages->schedule(
            $page['id'],
            gmdate('c', time() + 3600),
            'UTC',
            1,
            $this->actor
        );
        self::assertSame('scheduled', $scheduled['status']);
        self::assertNotNull($scheduled['scheduledAt']);
        self::assertNull($this->pages->publishedForLegacyCode($this->binding, 'en'));

        $cancelled = $this->pages->cancelSchedule($page['id'], (int) $scheduled['version'], $this->actor);
        self::assertSame('draft', $cancelled['status']);
        self::assertNull($cancelled['scheduledAt']);

        $again = $this->pages->schedule($page['id'], gmdate('c', time() + 3600), 'Europe/London', (int) $cancelled['version'], $this->actor);
        Db::run('UPDATE tbl_studio_schedules SET publish_at = DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 MINUTE) WHERE id = ?', [$again['scheduleId']]);
        $result = (new PublishScheduler())->runDue();
        self::assertSame(1, $result['published']);
        $published = $this->pages->get($page['id']);
        self::assertSame('published', $published['status']);
        self::assertSame('A page built with the new CMS', $this->pages->publishedForLegacyCode($this->binding, 'en')['title']);
    }

    public function testUnpublishArchiveAndRollback(): void
    {
        $page = $this->pages->create(Samples::page('en', 'Rollback original'), $this->actor, $this->binding);
        $this->pageId = (string) $page['id'];
        $first = $this->pages->publish($page['id'], 1, $this->actor);
        self::assertSame('published', $first['status']);
        $revisionOne = (string) $first['publishedRevision'];

        $document = $first['document'];
        $document['title'] = 'Rollback updated';
        $saved = $this->pages->save($page['id'], $document, (int) $first['version'], $this->actor);
        $published = $this->pages->publish($page['id'], (int) $saved['version'], $this->actor);
        self::assertSame('Rollback updated', $this->pages->publishedForLegacyCode($this->binding, 'en')['title']);

        $rollback = $this->pages->publish($page['id'], (int) $published['version'], $this->actor, $revisionOne);
        self::assertSame($revisionOne, $rollback['publishedRevision']);
        self::assertSame('Rollback original', $this->pages->publishedForLegacyCode($this->binding, 'en')['title']);

        $unpublished = $this->pages->unpublish($page['id'], (int) $rollback['version'], $this->actor);
        self::assertSame('unpublished', $unpublished['status']);
        self::assertNull($this->pages->publishedForLegacyCode($this->binding, 'en'));

        $archived = $this->pages->archive($page['id'], (int) $unpublished['version'], $this->actor);
        self::assertSame('archived', $archived['status']);
        self::assertNotNull($archived['archivedAt']);
    }

    public function testRouteValidationPreventsDuplicateSlug(): void
    {
        $page = $this->pages->create(Samples::page('en'), $this->actor, $this->binding);
        $this->pageId = (string) $page['id'];
        $slug = 'p4-test-' . bin2hex(random_bytes(4));
        $routed = $this->pages->updateRoute($page['id'], $slug, $this->binding, 1, $this->actor);
        self::assertSame($slug, $routed['slug']);

        $other = $this->pages->create(Samples::page('en'), $this->actor, $this->binding . 'b');
        try {
            $this->pages->updateRoute($other['id'], $slug, null, 1, $this->actor);
            self::fail('A duplicate slug must be rejected.');
        } catch (Problem $error) {
            self::assertSame(422, $error->status);
        } finally {
            Db::run('DELETE FROM tbl_studio_schedules WHERE page_id = ?', [$other['id']]);
            Db::run('DELETE FROM tbl_studio_media_usages WHERE revision_id IN (SELECT id FROM tbl_studio_revisions WHERE page_id = ?)', [$other['id']]);
            Db::run('DELETE FROM tbl_studio_publications WHERE page_id = ?', [$other['id']]);
            Db::run('DELETE FROM tbl_studio_events WHERE page_id = ?', [$other['id']]);
            Db::run('UPDATE tbl_studio_pages SET draft_revision_id = NULL, published_revision_id = NULL WHERE id = ?', [$other['id']]);
            Db::run('DELETE FROM tbl_studio_revisions WHERE page_id = ?', [$other['id']]);
            Db::run('DELETE FROM tbl_studio_pages WHERE id = ?', [$other['id']]);
        }
        try {
            $this->pages->updateRoute($page['id'], 'Invalid Slug!', null, (int) $routed['version'], $this->actor);
            self::fail('An invalid slug must be rejected.');
        } catch (Problem $error) {
            self::assertSame(422, $error->status);
        }
    }

    public function testQualityCheckerBlocksEmptyDocumentAndWarnsMissingRoute(): void
    {
        $page = $this->pages->create(Samples::page('en'), $this->actor);
        $this->pageId = (string) $page['id'];
        $checker = new QualityChecker();
        $checks = $checker->check($page, $page['document']);
        self::assertTrue($checks['ok']);
        self::assertContains('route.missing', array_column($checks['warnings'], 'code'));

        $empty = $page['document'];
        $empty['sections'] = [];
        $blocked = $checker->check($page, $empty);
        self::assertFalse($blocked['ok']);
        self::assertContains('content.empty', array_column($blocked['errors'], 'code'));
    }

    public function testRedirectValidationAndMatch(): void
    {
        $redirects = new RedirectRepository();
        $from = '/old-' . bin2hex(random_bytes(4));
        $created = $redirects->create($from, '/new-destination', 'en', 301, $this->actor);
        self::assertSame($from, $redirects->match($from, 'en')['from_path']);
        self::assertNull($redirects->match($from, 'fa'));
        $redirects->delete((string) $created['id'], $this->actor);
        self::assertNull($redirects->match($from, 'en'));
    }

    public function testPreviewTokenIsHashedSingleUseLookup(): void
    {
        $page = $this->pages->create(Samples::page('en'), $this->actor);
        $this->pageId = (string) $page['id'];
        $repo = new PreviewTokenRepository();
        $issued = $repo->issue($page['id'], $this->actor, 300);
        self::assertSame(64, strlen($issued['token']));
        $resolved = $repo->resolve($issued['token']);
        self::assertSame($page['id'], $resolved['pageId']);
        self::assertNull($repo->resolve(str_repeat('a', 64)));
    }

    public function testRoleMatrixEnforcesPublisherBoundary(): void
    {
        self::assertContains('pages.publish', Roles::permissions(Roles::PUBLISHER));
        self::assertNotContains('pages.edit', Roles::permissions(Roles::PUBLISHER));
        self::assertContains('pages.edit', Roles::permissions(Roles::EDITOR));
        self::assertNotContains('pages.publish', Roles::permissions(Roles::EDITOR));
        self::assertContains('users.manage', Roles::permissions(Roles::ADMINISTRATOR));
    }
}
