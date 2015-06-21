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
        if (null === $this->entity) {
            $this->entity = new Entity\Category();
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
            ->addOption('active', null, InputOption::VALUE_NONE, 'If set, the category is going to be active.');
    }

    /**
     * Enable interaction
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('title')) {
            $value = $this->getHelper('dialog')->askAndValidate($output, 'Please enter (Title): ',
                [$this, 'validateTitle'], 1);
            $input->setArgument('title', $value);
        }

        if (!$input->getArgument('parent')) {
            $value = $this->getHelper('dialog')->askAndValidate($output, 'Please enter (Parent category ID): ',
                [$this, 'validateParent'], 1);
            $input->setArgument('parent', $value);
        }
    }

    /**
     * Validate the category title
     *
     * @param string $value
     *
     * @return string
     *
     * @throws \Exception
     */
    public function validateTitle($value)
    {
        // Set title to entity
        $this->getEntity()->setTitle($value);

        // Validate entity and return the first error found in property 'title'
        $errors = $this->getValidator()->validate($this->entity);
        foreach ($errors as $error) {
            if ($error->getPropertyPath() == 'title') {
                throw new \InvalidArgumentException($error->getMessage());
            }
        }

        return $value;
    }

    /**
     * Validate the category parent
     *
     * @param string $value
     *
     * @return Entity\Category
     *
     * @throws \Exception
     */
    public function validateParent($value)
    {
        if ($value < 0 && ($value !== '' || $value !== null)) {
            throw new \InvalidArgumentException('The parent category ID must be a positve integer.');
        }

        if ($value > 0) {
            if (!($parent = $this->getRepository('category')->find($value))) {
                throw new \InvalidArgumentException('The parent category ID is invalid.');
            }
            $this->getEntity()->setParent($parent);
        }

        return $value;
    }

    /**
     * Execute the command line to create a new category.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getRepository('category');
        $category = $this->getEntity();
        $parent = $input->getArgument('parent');

        // Setup category entity
        $category->setCreated();
        $category->setTitle($input->getArgument('title'));
        $category->setDescription($input->getArgument('desc'));
        $category->setActive((boolean) $input->getOption('active'));
        if (!$input->isInteractive() && $parent > 0) {
            $category->setParent($repository->find($parent));
        }

        // Valid category
        $errors = $this->getValidator()->validate($category);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->error($output, $error);
            }

            return;
        }

        // Insert category into the database
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        $this->success($output, 'Voila... You have created a new category.');
    }
}
