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
        if (null === $this->entity) {
            $this->entity = new Entity\Card();
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
            ->addOption('active', null, InputOption::VALUE_NONE, 'If set, the card is going to be active.');
    }

    /**
     * Enable interaction
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('title')) {
            $value = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter (Title): ',
                [$this, 'validateTitle'],
                1
            );
            $input->setArgument('title', $value);
        }

        if (!$input->getArgument('content')) {
            $value = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter (Content): ',
                [$this, 'validateContent'],
                1
            );
            $input->setArgument('content', $value);
        }

        if (!$input->getArgument('category')) {
            $value = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter (Category ID): ',
                [$this, 'validateCategory'],
                1
            );
            $input->setArgument('category', $value);
        }
    }

    /**
     * Validate the card title
     *
     * @param string $value
     *
     * @return string
     *
     * @throws \Exception
     */
    public function validateTitle($value)
    {
        $this->getEntity()->setTitle($value);

        $error = $this->validate($this->entity, 'title');
        if ($error !== true) {
            throw new \InvalidArgumentException($error);
        }

        return $value;
    }

    /**
     * Validate the card content
     *
     * @param string $value
     *
     * @return string
     *
     * @throws \Exception
     */
    public function validateContent($value)
    {
        $this->getEntity()->setContent($value);

        $error = $this->validate($this->entity, 'content');
        if ($error !== true) {
            throw new \InvalidArgumentException($error);
        }

        return $value;
    }

    /**
     * Validate the card category
     *
     * @param int $value
     *
     * @return Entity\Category
     *
     * @throws \Exception
     */
    public function validateCategory($value)
    {
        $category = $this->getRepository('category')->find($value);
        if (!$category) {
            throw new \InvalidArgumentException('The category ID is invalid.');
        }

        $this->getEntity()->setCategory($category);

        $error = $this->validate($this->entity, 'category');
        if ($error !== true) {
            throw new \InvalidArgumentException($error);
        }

        return $value;
    }

    /**
     * Execute the command line to create a new card.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Setup card entity
        $card = $this->getEntity();
        $card->setCreated();
        $card->setTitle($input->getArgument('title'));
        $card->setContent($input->getArgument('content'));
        $card->setActive((boolean) $input->getOption('active'));
        $card->setMetaKeywords($input->getArgument('keywords'));
        $card->setMetaDescription($input->getArgument('description'));
        $card->setViews(0);
        if (!$input->isInteractive()) {
            $card->setCategory(
                $this->getRepository('category')->find($input->getArgument('category'))
            );
        }
        if (($slug = $input->getArgument('slug')) !== null) {
            $card->setSlug($slug);
        }

        // Valid category
        $errors = $this->getValidator()->validate($card);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->error($output, $error);
            }

            return 1;
        }

        // Insert category into the database
        $em = $this->getDoctrine()->getManager();
        $em->persist($card);
        $em->flush();

        return $this->success($output, 'Voila... You have created a new card.');
    }
}
