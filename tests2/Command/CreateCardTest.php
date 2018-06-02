<?php

/*
 * This file is part of the Moo\FlashCard package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCard\Tests\Command;

use Mockery;
use Moo\FlashCard\Command\CreateCard;
use Moo\FlashCard\Entity\Card;
use Moo\FlashCard\Tests\BaseTestCase;

/**
 * CreateCardTest contains test cases for the command line CreateCard class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CreateCardTest extends BaseTestCase
{
    public function testCreateCard()
    {
        // Test creating card.
        $title    = 'Test Card 1';
        $slug     = 'test-card-1';
        $category = $this->category();

        // Mock the ask & choice method
        $command = Mockery::mock(CreateCard::class . '[ask, choice]');
        $command->shouldReceive('ask')
            ->times(3)
            ->andReturn($title, $title, $title)
            ->shouldReceive('choice')
            ->once()
            ->andReturn($category->title);

        // Register the command
        $this->app['Illuminate\Contracts\Console\Kernel']->registerCommand($command);

        // Call the command
        $this->artisan('flashcard:card', [
            '--active'         => true,
            '--no-interaction' => true,
        ]);

        // Query the created card
        $card = Card::where('slug', $slug)->first();

        // Check card values as expected
        $this->assertEquals($title, $card->title);
        $this->assertEquals($category->id, $card->category_id);
        $this->assertEquals($slug, $card->slug);
        $this->assertTrue((bool) $card->active);
    }
}
