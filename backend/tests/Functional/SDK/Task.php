<?php

declare(strict_types=1);

namespace App\Tests\Functional\SDK;

use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class Task extends ApiWebTestCase
{
    public static function create(string $taskName, string $token): Response
    {
        $body = [];
        $body['taskName'] = $taskName;
        $body = json_encode($body, JSON_THROW_ON_ERROR);

        return self::request('POST', '/api/tasks/create', $body, token: $token);
    }

    public static function createAndReturnId(string $taskName, string $token): string
    {
        $response = self::create($taskName, $token);
        $taskInfo = self::jsonDecode($response->getContent());

        return $taskInfo['id'];
    }

    public static function list(string $token, int $limit = 10, int $offset = 1): array
    {
        $params = [
            'per-page' => $limit,
            'page' => $offset,
        ];

        $uri = '/api/tasks?'.http_build_query($params);

        $response = self::request('GET', $uri, token: $token);

        self::assertSuccessResponse($response);

        return self::jsonDecode($response->getContent());
    }
}
