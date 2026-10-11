<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * إنشاء تصنيف جديد.
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * تحديث تصنيف موجود.
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    /**
     * حذف تصنيف إذا لم يكن مرتبطاً بوظائف.
     * يُرجع true إذا تم الحذف، false إذا كان مرتبطاً بوظائف ولا يمكن الحذف.
     */
    public function deleteIfNotUsed(Category $category): bool
    {
        if ($category->jobs()->count() > 0) {
            return false;
        }
        $category->delete();
        return true;
    }
}
