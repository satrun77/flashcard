<?php
/*
 * This file is part of the flash.dev package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCard\Api;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * Class SearchableCardFilter provide searchable custom filter for spatie query builder module
 *
 * @package Moo\FlashCard\Api
 */
class SearchableCardFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Filter a query by search for LIKE title or content
        return $query->where(function ($query) use ($value) {
            $query->search($value);
        });
    }
}
