<?php

declare(strict_types=1);

namespace App\Task\Query\Task\FindUncompletedTasksByUserId;

use DateTimeImmutable;

/**
 * DTO задачи
 */
final class TaskData
{
    public function __construct(
        public readonly string $taskName,
        public readonly DateTimeImmutable $createdAt,
    ) {
    }
}
