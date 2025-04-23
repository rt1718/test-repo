<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;

class ArticleRepository implements ArticleRepositoryInterface
{
    /**
     * Все статьи, по дате, пагинация
     *
     * @return Paginator
     */
    public function getAll(): Paginator
    {
        return Article::orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * Статья по ID (без связей)
     *
     * @param int $id
     * @return Article
     */
    public function getById(int $id): Article
    {
        return Article::findOrFail($id);
    }

    /**
     * Статья по ID с отношениями
     *
     * @param int $id
     * @param array $with - список связей (пример: ['tags'])
     * @return Article
     */
    public function getById(int $id, array $with = []): Article
    {
        return Article::with($with)->findOrFail($id);
    }

    /**
     * Создание статьи
     *
     * @param array $data
     * @return Article
     */
    public function create(array $data): Article
    {
        return Article::create($data);
    }

    /**
     * Обновление статьи по ID
     *
     * @param int $id
     * @param array $data
     * @return Article
     */
    public function update(int $id, array $data): Article
    {
        $article = $this->getById($id);
        $article->update($data);
        return $article;
    }

    /**
     * Удаление статьи по ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->getById($id)->delete();
    }
}
