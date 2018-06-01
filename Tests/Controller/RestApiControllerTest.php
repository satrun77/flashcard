<?php

/*
 * This file is part of the Moo\FlashCard package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCard\Tests\Controller;

use Illuminate\Foundation\Testing\TestResponse;
use Moo\FlashCard\Entity\Card;
use Moo\FlashCard\Entity\Category;
use Moo\FlashCard\Tests\BaseTestCase;

/**
 * RestApiControllerTest contains test cases for the REST API controller.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class RestApiControllerTest extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Create categories
        $category1 = $this->category();
        $category2 = $this->category([
            'title' => 'Category 2'
        ]);
        $category3 = $this->category([
            'title' => 'Category 3'
        ]);
        $this->category([
            'title' => 'Category 4'
        ]);

        // Create cards
        for ($i = 1; $i < 5; $i++) {
            $this->card([
                'title' => 'Card ' . $i,
                'category_id' => $category1->id,
            ]);
        }

        for ($i = 5; $i < 8; $i++) {
            $this->card([
                'title' => 'Card ' . $i,
                'category_id' => $category2->id,
            ]);
        }

        $this->card([
            'title' => 'Card 9',
            'category_id' => $category3->id,
        ]);
    }

    public function testViewACardDetails()
    {
        // Get card by slug
        $card = Card::where('slug', 'card-1')->first();

        /** @var TestResponse $response */
        // Request to get all cards
        $response = $this->getJson('/api/cards');

        // Assert that the above card exists in the response
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $card->title]);
        $response->assertJsonFragment(['category_id' => (string )$card->category_id]);
    }

    public function testGetLimitCard()
    {
        // Count of cards to return
        $count = 1;

        /** @var TestResponse $response */
        // Request to get limit cards
        $response = $this->getJson('/api/cards?limit=' . $count);

        // Assert the response contains same amount of records based on count above
        $response->assertJsonFragment(['current_page' => 1]);
        $response->assertJsonCount($count, 'data');
        $response->assertStatus(200);
    }

    public function testSearchValidCards()
    {
        // Search query
        $keyword = 'card 1';

        /** @var TestResponse $response */
        // Request to get cards by a search query and include category details
        $response = $this->getJson(sprintf('/api/cards?filter[search]=%s&include=category', $keyword));

        // Assert that records found and category included
        $response->assertJsonFragment(['category']);
        $this->assertEquals('Category 1', $response->json('0.category.title'));
        $response->assertJsonFragment(['slug' => 'card-1']);
        $response->assertJsonCount(1);
        $response->assertStatus(200);
    }

    public function testSearchInvalidCards()
    {
        // Search query
        $keyword = 'ca 1';

        /** @var TestResponse $response */
        // Request to get cards by search query
        $response = $this->getJson(sprintf('/api/cards?filter[search]=%s', $keyword));

        // Assert that nothing found based on above search query
        $response->assertJsonCount(0);
        $response->assertStatus(200);
    }

    public function testSearchValidCardsWithoutCategory()
    {
        // Search query
        $keyword = 'card 1';

        /** @var TestResponse $response */
        // Request to get cards by a search query and does not include category details
        $response = $this->getJson(sprintf('/api/cards?filter[search]=%s', $keyword));

        // Assert that records does not include category details
        $response->assertJsonMissing(['category']);
        $this->assertNull($response->json('0.category.title'));
        $response->assertJsonFragment(['slug' => 'card-1']);
        $response->assertJsonCount(1);
        $response->assertStatus(200);
    }

    public function testFilterCardsByCategory()
    {
        // Get a category
        $category = Category::all()->first();

        /** @var TestResponse $response */
        // Request to get cards filtered by a category
        $response = $this->getJson(sprintf('/api/cards?filter[category_id]=%s&include=category', $category->id));

        // Assert that the records are belongs to the category above
        $cards = $response->json();
        foreach ($cards as $card) {
            $this->assertArrayHasKey('category', $card);
            $this->assertEquals($category->id, $card['category']['id']);
        }
        $this->assertTrue(count($cards) > 0);
        $response->assertStatus(200);
    }
}
