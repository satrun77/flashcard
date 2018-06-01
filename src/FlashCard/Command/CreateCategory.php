<?php

namespace Moo\FlashCard\Command;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Moo\FlashCard\Entity\Category;
use Moo\FlashCard\Traits\AskAndValidate;

/**
 * CreateCardCommand is a command line class for creating a new card.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CreateCategory extends Command
{
    use AskAndValidate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flashcard:category {--A|active= : If set, the category is going to be active.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a category';

    /**
     * @var Category
     */
    protected $entity;


    /**
     * Instance of category model
     *
     * @return Model
     */
    protected function getEntity(): Model
    {
        if (is_null($this->entity)) {
            $this->entity = new Category([
                'active' => (boolean)$this->option('active'),
            ]);
        }

        return $this->entity;
    }

    /**
     * Execute the command line to create a new category.
     *
     * @return int
     */
    public function handle()
    {
        // Ask for category title
        $this->askWithValidation('Please enter category title', 'title');

        // Ask for category content
        $this->askWithValidation('Please enter category description', 'description');

        // Ask for category color
        $this->askWithValidation('Please enter category color', 'color');

        // Ask for card category
        $choices = Category::all()->pluck('title', 'id')->prepend('Root Category')->toArray();
        if (count($choices) > 0) {
            $category = $this->choice('Please select category parent or enter "0" for root category', $choices, key($choices));

            // Set parent category id if value not "0"
            if ($category !== '0') {
                $this->getEntity()->fill([
                    'parent' => array_search($category, $choices, true),
                ]);
            }
        }

        // Save entity and display message
        $this->getEntity()->save();
        $this->info('Voila... You have created a new category.');

        return 0;
    }
}
