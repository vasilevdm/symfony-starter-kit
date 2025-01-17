<?php

declare(strict_types=1);

namespace App\Task\Command\CreateTask;

use App\Infrastructure\ApiRequestResolver\ApiRequest;
use Webmozart\Assert\Assert;

/**
 * Команда создания задачи
 */
final class CreateTaskCommand implements ApiRequest
{
    public function __construct(public readonly string $taskName)
    {
        Assert::notEmpty($taskName, 'Укажите наименование задачи');
    }
}
