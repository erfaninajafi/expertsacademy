<?php
    declare(strict_types=1);
    namespace ExpertsAcademy\Studio\Publish;

    /** Retired compatibility shell. There is no scheduled publication worker. */
    final class PublishScheduler
    {
        public const MAX_ATTEMPTS = 0;

        public function runDue(int $limit = 25): array
        {
            return ['claimed' => 0, 'published' => 0, 'failed' => 0, 'results' => []];
        }

        public function publishClaimed(string $scheduleId): array
        {
            throw new \RuntimeException('Scheduled publishing has been retired.');
        }

        public function retry(string $scheduleId, string $actorId): void
        {
            throw new \RuntimeException('Scheduled publishing has been retired.');
        }
    }
