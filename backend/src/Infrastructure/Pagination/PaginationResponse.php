<?php

declare(strict_types=1);

namespace App\Infrastructure\Pagination;

use Webmozart\Assert\Assert;

/**
 * Ответ пагинации. Высчитывает кол-во страниц
 */
final class PaginationResponse
{
    public readonly int $pagesCount;

    public function __construct(
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage,
    ) {
        Assert::positiveInteger($perPage);
        Assert::positiveInteger($currentPage);
        Assert::natural($total);

        // Кол-во страниц не может быть равно 0, так как текущая страница всегда положительна
        $pagesCount = (int) ceil($total / $perPage);
        $this->pagesCount = $pagesCount === 0 ? 1 : $pagesCount;
    }
}
