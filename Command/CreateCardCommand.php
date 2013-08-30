<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Command;

use Moo\FlashCardBundle\Entity;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CreateCardCommand is a command line class for creating a new card.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CreateCardCommand extends AbstractCommand
{
    /**
     * An instance of card entity
     *
     * @var Entity\Card
     */
    protected $entity;

    /**
     * Get a card entity
     *
     * @return Entity\Card
     */
    protected function getEntity()
    {
        if (null == $this->entity) {
            $this->entity = new Entity\Card;
        }

        return $this->entity;
    }

    /**
     * Command configuration
     * Create a new card
     *
     * @return void
     */
    protected function configure()
    {
        $this
                ->setName('flashcard:card:create')
                ->setDescription('Create a card')
                ->addArgument('title', InputArgument::REQUIRED, 'The title of the card.')
                ->addArgument('content', InputArgument::REQUIRED, 'The content of the card.')
                ->addArgument('category', InputArgument::REQUIRED, 'The category ID the card is belong to.')
                ->addArgument('keywords', InputArgument::OPTIONAL, 'Comma seperated keywords for the metadata tag.', null)
                ->addArgument('description', InputArgument::OPTIONAL, 'The metadata description.', null)
                ->addArgument('slug', InputArgument::OPTIONAL, 'The url slug of the card.', null)
                ->addOption('active', null, InputOption::VALUE_NONE, 'If set, the card is going to be active.')
        ;
    }

    /**
     * Enable interaction
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->addArgumentsInteract($input, $output);
    }

    /**
     * Validate the card title
     *
     * @param  string     $value
     * @return string
     * @throws \Exception
     */
    public function validateTitle($value)
    {
        $this->getEntity()->setTitle($value);

        $error = $this->validate($this->entity, 'title');
        if ($error !== true) {
            throw new \Exception($error);
        }

        return $value;
    }

    /**
     * Validate the card content
     *
     * @param  string     $value
     * @return string
     * @throws \Exception
     */
    public function validateContent($value)
    {
        $this->getEntity()->setContent($value);

        $error = $this->validate($this->entity, 'content');
        if ($error !== true) {
            throw new \Exception($error);
        }

        return $value;
    }

    /**
     * Validate the card category
     *
     * @param  int             $value
     * @return Entity\Category
     * @throws \Exception
     */
    public function validateCategory($value)
    {
        $value = $this->getRepository('category')->find($value);
        if (!$value) {
            throw new \Exception('The category ID is invalid.');
        }

        $this->getEntity()->setCategory($value);

        $error = $this->validate($this->entity, 'category');
        if ($error !== true) {
            throw new \Exception($error);
        }

        return $value;
    }

    /**
     * Execute the command line to create a new card.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return boolean
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // parameters
        $title = $input->getArgument('title');
        $content = $input->getArgument('content');
        $category = $input->getArgument('category');
        $slug = $input->getArgument('slug');
        $metaKeywords = $input->getArgument('keywords');
        $metaDesc = $input->getArgument('description');
        $active = (boolean) $input->getOption('active');

        if (!$category instanceof Entity\Category) {
            // check if category id is valid
            $category = $this->getRepository('category')->find($category);
            if (!$category) {
                return $this->error($output, "Invalid category Id '%id%'", array('%id%' => $input->getArgument('category')));
            }
        }

        // setup card entity
        $card = $this->getEntity();
        $card->setCreated();
        $card->setTitle($title);
        $card->setContent($content);
        $card->setCategory($category);
        $card->setIsActive($active);
        $card->setMetaKeywords($metaKeywords);
        $card->setMetaDescription($metaDesc);
        if ($slug !== null) {
            $card->setSlug($slug);
        }
        $card->setViews(0);

        $errors = $this->getValidator()->validate($card);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->error($output, $error);
            }

            return false;
        }

        // insert card into the database
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($card);
        $em->flush();

        return $this->success($output, "Voila... You have created a new card.");
    }

}
