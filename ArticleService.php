<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Services\Helpers\UserDataSanitizer;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class ArticleService
{
    protected ArticleRepositoryInterface $articleRepository;
    protected UploadImageService $uploadImage;

    /**
     * @param ArticleRepositoryInterface $articleRepository
     */
    public function __construct(ArticleRepositoryInterface $articleRepository, UploadImageService $uploadImage)
    {
        $this->articleRepository = $articleRepository;
        $this->uploadImage = $uploadImage;
    }

    /**
     * @return Paginator
     * Получаем все статьи через пагинацию.
     */
    public function getAllArticles(): Paginator
    {
        return $this->articleRepository->getAll();
    }

    /**
     * @param int $id
     * @return Article
     * Получаем только одну статью по айди.
     */
    public function getArticleById(int $id): Article
    {
        return $this->articleRepository->getById($id);
    }

    /**
     * @param int $id
     * @return Article
     * Получаем статью вместе с тегом.
     */
    public function getByIdWithTag(int $id): Article
    {
        return $this->articleRepository->getByIdWithTag($id);
    }

    /**
     * @param array $data
     * @return Article
     * Создаем статью. Устанавливаем принудительно булево для публикации.
     * Фильтруем все на пустые поля.
     * Привязываем к тегу.
     */
    public function createArticle(array $data): Article
    {
        $data['published'] = filter_var($data['published'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        if (isset($data['poster']) && $data['poster'] instanceof UploadedFile) {
            $poster = $data['poster'];
            $this->uploadImage->upload(['poster' => $poster]);
        }

        $clean = Arr::except($data, ['tag_id']);

        $article = $this->articleRepository->create($clean);

        if (!empty($data['tag_id']) && is_array($data['tag_id'])) {
            $article->tags()->sync($data['tag_id']);
        }

        return $article;
    }

    /**
     * @param int $id
     * @param array $data
     * @return Article
     * Обновляем статью, также, как добавляем.
     */
    public function updateArticle(int $id, array $data): Article
    {
        $data['published'] = filter_var($data['published'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $data = $this->filterData($data);

        $article = $this->articleRepository->update($id, $data);

        if (!empty($data['tag_id']) && is_array($data['tag_id'])) {
            $article->tags()->sync($data['tag_id']);
        } else {
            $article->tags()->sync([]);
        }

        return $article;
    }

    /**
     * @param int $id
     * @return bool
     * Удаляем статью.
     */
    public function deleteArticle(int $id): bool
    {
        return $this->articleRepository->delete($id);
    }

    /**
     * @param array $data
     * @return array
     * Используем хелпер, чтобы убрать пустые данные.
     */
    private function filterData(array $data): array
    {
        return UserDataSanitizer::sanitize($data);
    }
}
