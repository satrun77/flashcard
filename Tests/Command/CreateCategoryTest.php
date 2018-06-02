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
use Moo\FlashCard\Command\CreateCategory;
use Moo\FlashCard\Entity\Category;
use Moo\FlashCard\Tests\BaseTestCase;

/**
 * CreateCategoryTest contains test cases for the command line CreateCategory class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CreateCategoryTest extends BaseTestCase
{
    public function testCreateCategory()
    {
        // Test creating category.
        $categoryTile = 'Test Category 1';

        // Mock the ask method
        $command = Mockery::mock(CreateCategory::class . '[ask]');
        $command->shouldReceive('ask')
            ->times(3)
            ->andReturn($categoryTile);

        // Register the command
        $this->app['Illuminate\Contracts\Console\Kernel']->registerCommand($command);

        // Call the command
        $this->artisan('flashcard:category', [
            '--active'         => true,
            '--no-interaction' => true,
        ]);

        // Query the created category
        $category = Category::where('title', $categoryTile)->first();

        // Check category values as expected
        $this->assertEquals($categoryTile, $category->title);
        $this->assertTrue((bool) $category->active);
    }

    public function testCreateSubCategoryInteractive()
    {
        $this->category();
        $category = $this->category([
            'title' => 'Category 2',
        ]);

        // Test creating category.
        $title   = 'Child Category 1';
        $content = 'Content';
        $color   = 'red';

        // Mock the ask & choice method
        $ask = Mockery::mock(CreateCategory::class . '[ask, choice]');
        $ask->shouldReceive('ask')
            ->times(3)
            ->andReturn($title, $content, $color)
            ->shouldReceive('choice')
            ->once()
            ->andReturn($category->title);

        // Register the command
        $this->app['Illuminate\Contracts\Console\Kernel']->registerCommand($ask);

        // Call the command
        $this->artisan('flashcard:category', [
            '--active'         => true,
            '--no-interaction' => true,
        ]);

        // Query the created category
        $childCategory = Category::where('title', $title)->first();

        // Check category values as expected
        $this->assertEquals($title, $childCategory->title);
        $this->assertEquals($category->id, $childCategory->parent);
        $this->assertTrue((bool) $childCategory->active);
    }
}
