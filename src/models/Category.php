<?php

namespace Nosennij\LaravelCategory\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    /**
     * Название таблицы в базе данных для работы модели.
     */
    protected $table = 'categories';

    /**
     * Возвращает условия для Eloquent построителя запросов
     * Условие - коревой уровень категорий.
     *
     * @param $query
     * @return mixed
     */
    public function scopeMain($query): Builder
    {
        return $query->where('parent_id', 0);
    }

    /**
     * Возвращает детей запрошенной категории.
     *
     * @return HasMany
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Возвращает родителя запрошенной категории.
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
