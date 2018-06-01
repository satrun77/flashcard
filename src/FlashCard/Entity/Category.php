<?php

namespace Moo\FlashCard\Entity;

use Illuminate\Database\Eloquent\Model;
use Moo\FlashCard\Traits\Validatable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Category is the entity class that represents a record from the database.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Category extends Model
{
    use  Validatable;

    /**
     * Constant for card active status
     *
     * @var bool
     */
    const ACTIVE = true;
    /**
     * Constant for card inactive status
     *
     * @var int
     */
    const INACTIVE = false;

    /**
     * Timestamp enabled.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Name of database table.
     *
     * @var string
     */
    protected $table = 'card_category';

    /**
     * List of allowed columns to be used in $this->fill().
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'parent', 'active', 'color'];

    /**
     * Get collection of cards in the category.
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    /**
     * Get collection of children categories
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent');
    }

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent');
    }

    /**
     * Get number of cards in a category
     *
     * @return int
     */
    public function countCards()
    {
        return $this->cards()->count();
    }

    /**
     * Get collection of validation rules for model attributes
     *
     * @return array
     */
    protected function getRules(): array
    {
        return [
            'title' => 'required|max:255|min:5',
        ];
    }

    /**
     * Scope to filter by active categories
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', '=', static::ACTIVE);
    }

    /**
     * Score to filter by LIKE search
     *
     * @param Builder $query
     * @param string $keyword
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        $query->where('title', 'LIKE', '%' . $keyword . '%');
        $query->orWhere('description', 'LIKE', '%' . $keyword . '%');

        return $query;
    }
}
