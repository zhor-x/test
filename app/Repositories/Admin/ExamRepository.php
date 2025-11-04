<?php

namespace App\Repositories\Admin;

use App\Models\Category;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Collection;

class ExamRepository
{
    public function getList(): Collection
    {
        return Exam::query()->get();
    }

    public function store($payload): void
    {
        $category = new  Category;
        $category->title = $payload['title'];
        $category->save();
    }

    public function getById(int $id): Category
    {
        return Category::query()->findOrFail($id);
    }


    public function update(int $categoryId, $payload): void
    {
        $category = $this->getById($categoryId);
        $category->title = $payload['title'];
        $category->save();
    }


    public function delete(int $categoryId): void
    {
        $category = $this->getById($categoryId);
        $category->delete();
    }
}
