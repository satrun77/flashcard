<?php

namespace Moo\FlashCard\Tests;

use Moo\FlashCard\Entity\Card;
use Moo\FlashCard\Entity\Category;
use Moo\FlashCard\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;

abstract class BaseTestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class, QueryBuilderServiceProvider::class];
    }
    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        include __DIR__ . '/../routes/api.php';

        $this->artisan('migrate', ['--database' => 'testbench']);
    }

    protected function card(array $args = [])
    {
        $categoryId = '';
        if (!array_key_exists('category_id', $args)) {
            $categoryId = $this->category()->id;
        }
        $card = new Card(array_replace([
            'title'            => 'Cool card',
            'active'           => true,
            'content'          => 'Content',
            'meta_description' => '',
            'category_id'      => $categoryId,
        ], $args));

        $card->save();

        return $card;
    }

    protected function category(array $args = [])
    {
        $category = new Category(array_replace([
            'title'       => 'Category 1',
            'description' => '',
            'active'      => true,
            'color'       => 'red',
            'parent'      => 0,
        ], $args));

        $category->save();

        return $category;
    }
}
