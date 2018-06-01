<?php

namespace Moo\FlashCard\Command;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Moo\FlashCard\Entity\Card;
use Moo\FlashCard\Entity\Category;
use Moo\FlashCard\Traits\AskAndValidate;

/**
 * CreateCardCommand is a command line class for creating a new card.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CreateCard extends Command
{
    use AskAndValidate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flashcard:card {--A|active= : If set, the card is going to be active.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a card';

    /**
     * @var Card
     */
    protected $entity;


    /**
     * Instance of card model
     *
     * @return Model
     */
    protected function getEntity(): Model
    {
        if (is_null($this->entity)) {
            $this->entity = new Card([
                'active' => (boolean)$this->option('active'),
            ]);
        }

        return $this->entity;
    }

    /**
     * Execute the command line to create a new card.
     *
     * @return int
     */
    public function handle()
    {
        // Get collection of categories - can complete process if no categories found
        $choices = Category::all()->pluck('title', 'id')->toArray();
        if (count($choices) === 0) {
            $this->error('Add category first before creating cards.');
            return 1;
        }

        // Ask for card title
        $this->askWithValidation('Please enter card title', 'title');

        // Ask for card content
        $this->askWithValidation('Please enter card content', 'content');

        // Ask for card category & set card category
        $category = $this->choice('Please select card category', $choices, key($choices));
        $this->getEntity()->fill([
            'category_id' => array_search($category, $choices, true),
        ]);

        // Ask for card meta description
        $this->askWithValidation('Please enter card SEO meta description', 'meta_description');

        // Save entity and display message
        $this->getEntity()->save();
        $this->info('Voila... You have created a new card.');

        return 0;
    }
}
