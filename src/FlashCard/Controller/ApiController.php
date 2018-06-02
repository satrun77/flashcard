<?php

/*
 * This file is part of the Moo\FlashCard package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCard\Controller;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moo\FlashCard\Api\SearchableCardFilter;
use Moo\FlashCard\Entity\Card;
use Moo\FlashCard\Entity\Category;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * RestApiController is the default REST API controller.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class ApiController extends Controller
{
    /**
     * Get collection of cards
     *
     * @param  Request                       $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCards(Request $request)
    {
        // Parameter for limit by
        $limit = (int) $request->input('limit');

        $cards = QueryBuilder::for(Card::class)
            ->defaultSort('title')
            ->allowedFilters(
                Filter::exact('id'),
                'slug',
                'title',
                'category_id',
                Filter::custom('search', SearchableCardFilter::class)
            )
            ->allowedIncludes('category')
            ->allowedAppends('short_modified')
            ->active();

        if ($limit) {
            $cards = $cards->simplePaginate($limit);
        } else {
            $cards = $cards->get();
        }

        return response()->json($cards);
    }

    /**
     * Get details of a card
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCard()
    {
        $card = QueryBuilder::for(Card::class)
            ->defaultSort('title')
            ->allowedFilters(
                Filter::exact('id'),
                'slug'
            )
            ->allowedIncludes('category')
            ->active()
            ->first();

        return response()->json($card);
    }

    /**
     * Get collection of categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories()
    {
        $categories = QueryBuilder::for(Category::class)
            ->defaultSort('title')
            ->allowedFilters(
                Filter::exact('id'),
                Filter::custom('search', SearchableCardFilter::class)
            )
            ->withCount([
                'cards' => function (Builder $query) {
                    $query->active();
                },
            ])
            ->active()
            ->get();

        return response()->json($categories);
    }
}
