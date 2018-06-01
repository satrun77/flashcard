<?php

namespace Moo\FlashCard\Entity;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Moo\FlashCard\Traits\Validatable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Card is the entity class that represents a record from the database.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Card extends Model
{
    use HasSlug, Validatable;

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
     * Max size of slug
     *
     * @var int
     */
    const SLUG_SIZE = 100;

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
    protected $table = 'card';
    /**
     * List of allowed columns to be used in $this->fill().
     *
     * @var array
     */
    protected $fillable = ['title', 'slug', 'active', 'content', 'meta_description', 'category_id'];

    protected $appends = ['short_modified'];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the category this card belong to
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get collection of card views
     */
    public function cardViews()
    {
        return $this->hasMany(CardView::class);
    }

    /**
     * Get formatted version of updated_at attribute
     *
     * @return string
     */
    public function getShortModifiedAttribute(): string
    {
        return (string)$this->updated_at->format('F d, Y');
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(static::SLUG_SIZE);
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
            'category_id' => 'required',
            'slug' => [
                'required',
                // Ensure slug unique except when editing
                Rule::unique($this->table, 'slug')->ignore($this),
                'max:' . static::SLUG_SIZE,
            ]
        ];
    }

    /**
     * Score to filter by active cards
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
        $query->orWhere('content', 'LIKE', '%' . $keyword . '%');

        return $query;
    }

    /**
     * Update card view counter by 1 and insert record in card view table
     *
     * @param string $ip
     */
    public function incrementViews(string $ip): void
    {
        try {
            // Insert record in card view table
            $cardView = new CardView([
                'card_id' => $this->id,
                'ip' => $ip,
            ]);
            $cardView->save();

            // Increase the view counter by 1
            $this->views = $this->views + 1;
            $this->save();
        } catch (\Exception $exception) {
            // We ignore any error as this is just logging of views!
        }
    }
}
