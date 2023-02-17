<?php

declare(strict_types=1);

namespace App\Article\Http\Admin;

use App\Article\Domain\Article;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Ручка информации статьи
 */
#[IsGranted('ROLE_USER')]
#[Route('/admin/articles/{id}', methods: ['GET'])]
#[AsController]
final class InfoAction
{
    public function __invoke(Article $article): Article
    {
        return $article;
    }
}
