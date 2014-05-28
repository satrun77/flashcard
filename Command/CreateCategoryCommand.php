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
 * CreateCardCommand is a command line class for creating a new card category.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CreateCategoryCommand extends AbstractCommand
{
    /**
     * An instance of category entity
     *
     * @var Entity\Category
     */
    protected $entity;

    /**
     * Get a category entity
     *
     * @return Entity\Category
     */
    protected function getEntity()
    {
        if (null == $this->entity) {
            $this->entity = new Entity\Category;
        }

        return $this->entity;
    }

    /**
     * Command configuration
     * Create a new category
     *
     * @return void
     */
    protected function configure()
    {
        $this
                ->setName('flashcard:category:create')
                ->setDescription('Create a category')
                ->addArgument('title', InputArgument::REQUIRED, 'The category title.')
                ->addArgument('desc', InputArgument::OPTIONAL, 'The category description.')
                ->addArgument('parent', InputArgument::OPTIONAL, 'The parent category ID.')
                ->addOption('active', null, InputOption::VALUE_NONE, 'If set, the category is going to be active.')
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
     * Validate the category title
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

        // make sure the category title is new
        $repository = $this->getRepository('category');
        $exists = $repository->findOneByTitle($value);
        if ($exists) {
            throw new \Exception(sprintf("There is an existing category with the title '%s'.", $exists->getTitle()));
        }

        return $value;
    }

    /**
     * Validate the category parent
     *
     * @param  string          $value
     * @return Entity\Category
     * @throws \Exception
     */
    public function validateParent($value)
    {
        if ($value < 0) {
            throw new \Exception('The parent category ID must be a positve integer.');
        }

        if ($value > 0) {
            $repository = $this->getRepository('category');
            $value = $repository->find($value);
            if (!$value) {
                throw new \Exception('The parent category ID is invalid.');
            }
        }

        return $value;
    }

    /**
     * Execute the command line to create a new category.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return boolean
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getRepository('category');

        // parameters
        $title = $input->getArgument('title');
        $desc = $input->getArgument('desc');
        $parent = $input->getArgument('parent');
        if (!$parent instanceof Entity\Category && null !== $parent) {
            $parent = $repository->find($parent);
        }
        $active = (boolean) $input->getOption('active');

        // setup category entity
        $category = $this->getEntity();
        $category->setCreated();
        $category->setDescription($desc);
        $category->setActive($active);
        $category->setTitle($title);
        if ($parent instanceof Entity\Category) {
            $category->setParent($parent);
        }

        $errors = $this->getValidator()->validate($category);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->error($output, $error);
            }

            return false;
        }

        // insert category into the database
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        return $this->success($output, "Voila... You have created a new category.");
    }

}
