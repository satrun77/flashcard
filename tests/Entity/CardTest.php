<?php

/*
 * This file is part of the Moo\FlashCard package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCard\Tests\Entity;

use Moo\FlashCard\Tests\BaseTestCase;

/**
 * CardTest contains test cases for the Card entity class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CardTest extends BaseTestCase
{
    public function testGetTitle()
    {
        // Create a card
        $card = $this->card();

        // Assert that the card is created
        $this->assertEquals('Cool card', $card->title);
        $this->assertEquals('cool-card', $card->slug);
        $this->assertEmpty($card->meta_description);
    }

    public function testIsActive()
    {
        // Create inactive card
        $card = $this->card([
            'active' => false,
        ]);

        // Assert the card is inactive
        $this->assertFalse((bool) $card->active);
    }

    /**
     * @expectedException   \InvalidArgumentException
     */
    public function testShortCardTitle()
    {
        // Attempt to create a card with short title - expect an exception
        $this->card([
            'title' => 'Card',
        ]);
    }

    public function testUniqueCardSlug()
    {
        // Create a card
        $card1 = $this->card([
            'title' => 'Card one',
        ]);

        // Create another card with same title
        $card2 = $this->card([
            'title' => 'Card one',
        ]);

        // Assert that the slug is not equals
        $this->assertNotEquals($card2->slug, $card1->slug);
    }
}
