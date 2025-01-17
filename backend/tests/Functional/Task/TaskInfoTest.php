<?php

declare(strict_types=1);

namespace App\Tests\Functional\Task;

use App\Tests\Functional\SDK\ApiWebTestCase;
use App\Tests\Functional\SDK\Task;
use App\Tests\Functional\SDK\User;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 *
 * @testdox Получение информации по задаче
 */
final class TaskInfoTest extends ApiWebTestCase
{
    /**
     * @testdox Создана задача, получена по ней информация
     */
    public function testSuccess(): void
    {
        $token = User::auth();

        $taskId = Task::createAndReturnId($taskName = 'Тестовая задача 1', $token);

        $response = Task::list($token);
        $tasks = $response['data'];

        self::assertCount(1, $tasks);

        $response = self::request('GET', "/api/tasks/{$taskId}", token: $token);
        self::assertSuccessResponse($response);

        $task = self::jsonDecode($response->getContent());

        self::assertNotNull($task['id']);
        self::assertSame($taskName, $task['taskName']);
        self::assertFalse($task['isCompleted']);
        self::assertNotNull($task['createdAt']);
        self::assertNull($task['completedAt']);
        self::assertNull($task['updatedAt']);
    }

    /**
     * @testdox Задача не найдена
     */
    public function testNotFound(): void
    {
        $token = User::auth();

        Task::create('Тестовая задача 1', $token);

        $taskId = (string) Uuid::v4();
        $response = self::request('GET', "/api/tasks/{$taskId}", token: $token);
        self::assertNotFound($response);
    }

    /**
     * @testdox Получение информации по задаче доступно только автору
     */
    public function testNoAccessAnotherUser(): void
    {
        $token = User::auth();
        Task::create('Тестовая задача №1', $token);

        $this->tearDown();
        $tokenSecond = User::auth('second@example.com');
        $taskId = Task::createAndReturnId('Тестовая задача №2 ', $tokenSecond);

        $response = self::request('GET', "/api/tasks/{$taskId}", token: $token);
        self::assertNotFound($response);
    }

    /**
     * @dataProvider notValidTokenDataProvider
     *
     * @testdox Доступ запрещен
     */
    public function testAccessDenied(string $notValidToken): void
    {
        $token = User::auth();
        $taskId = Task::createAndReturnId('Тестовая задача 1', $token);

        $response = self::request('GET', "/api/tasks/{$taskId}", token: $notValidToken);

        self::assertAccessDenied($response);
    }
}
