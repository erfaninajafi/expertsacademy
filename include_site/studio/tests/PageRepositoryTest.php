<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Tests;

use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Repo\PageRepository;
use PHPUnit\Framework\TestCase;

/**
 * Integration test against the configured development database.
 * Run with EA_STUDIO_DB_TESTS=1. All rows created here are removed again.
 */
final class PageRepositoryTest extends TestCase
{
    private PageRepository $repository;
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
            self::markTestSkipped('No administrator account available for the integration test.');
        }
        $this->actor = (string) $user['tbl_user_id'];
        $this->repository = new PageRepository();
        $this->binding = 'zz_test_' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        if ($this->pageId === null) {
            return;
        }
        $id = $this->pageId;
        Db::run('DELETE FROM tbl_studio_media_usages WHERE revision_id IN (SELECT id FROM tbl_studio_revisions WHERE page_id = ?)', [$id]);
        Db::run('DELETE FROM tbl_studio_publications WHERE page_id = ?', [$id]);
        Db::run('DELETE FROM tbl_studio_events WHERE page_id = ?', [$id]);
        Db::run('UPDATE tbl_studio_pages SET draft_revision_id = NULL, published_revision_id = NULL WHERE id = ?', [$id]);
        Db::run('DELETE FROM tbl_studio_revisions WHERE page_id = ?', [$id]);
        Db::run('DELETE FROM tbl_studio_pages WHERE id = ?', [$id]);
        $this->pageId = null;
    }

    public function testFullLifecycleCreateSavePublish(): void
    {
        $page = $this->repository->create(Samples::page('en'), $this->actor, $this->binding);
        $this->pageId = $page['id'];

        self::assertSame('draft', $page['status']);
        self::assertSame(1, $page['version']);
        self::assertSame('en', $page['document']['locale']);
        self::assertCount(1, $page['revisions']);
        self::assertNull($this->repository->publishedForLegacyCode($this->binding, 'en'));

        $document = $page['document'];
        $document['title'] = 'Updated title';
        $saved = $this->repository->save($page['id'], $document, 1, $this->actor);
        self::assertSame(2, $saved['version']);
        self::assertSame('Updated title', $saved['document']['title']);
        self::assertCount(2, $saved['revisions']);

        $published = $this->repository->publish($saved['id'], 2, $this->actor);
        self::assertSame('published', $published['status']);
        self::assertSame('Updated title', $this->repository->publishedForLegacyCode($this->binding, 'en')['title']);
        self::assertNull($this->repository->publishedForLegacyCode($this->binding, 'fa'));
    }

    public function testOptimisticLockingRejectsStaleWrite(): void
    {
        $page = $this->repository->create(Samples::page('en'), $this->actor);
        $this->pageId = $page['id'];
        $this->repository->save($page['id'], $page['document'], 1, $this->actor);

        try {
            $this->repository->save($page['id'], $page['document'], 1, $this->actor);
            self::fail('A stale write must be rejected.');
        } catch (Problem $problem) {
            self::assertSame(409, $problem->status);
        }
        self::assertSame(2, $this->repository->get($page['id'])['version']);
    }

    public function testInvalidDocumentIsNotPersisted(): void
    {
        $document = Samples::page();
        unset($document['title']);
        try {
            $this->repository->create($document, $this->actor);
            self::fail('Expected validation to fail.');
        } catch (Problem $problem) {
            self::assertSame(422, $problem->status);
        }
        self::assertSame(0, (int) (Db::one(
            'SELECT COUNT(*) AS c FROM tbl_studio_pages WHERE title = ?',
            ['']
        )['c'] ?? 0));
    }
}
