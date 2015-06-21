<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Tests\Command;

use Moo\FlashCardBundle\Tests\AbstractWebTestCase;
use Moo\FlashCardBundle\Command\CreateCategoryCommand;

/**
 * CreateCategoryTest contains test cases for the command line CreateCategory class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CreateCategoryTest extends AbstractWebTestCase
{
    public function testCreateCategory()
    {
        $this->loadFixtures([]);

        // Test creating category.
        $categoryTile = 'Test Category 1';

        $output = $this->runCommand('flashcard:category:create', [
            'title'            => $categoryTile,
            'desc'             => 'Category Desc',
            '--active'         => true,
            '--no-interaction' => false,
        ]);

        $this->assertRegExp('/Voila/', $output);
    }

    public function testCreateSubCategory()
    {
        $this->loadFixtures([
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCreateCategory',
        ]);

        $category = $this->get('moo_flashcard.category.repository')->findOneById(1);

        $categoryTile = 'Test Category 2';
        $output = $this->runCommand('flashcard:category:create', [
            'title'            => $categoryTile,
            'desc'             => 'Category Desc',
            'parent'           => $category->getId(),
            '--no-interaction' => true,
        ]);

        $this->assertRegExp('/Voila/', $output);
    }

    public function testCreateShortTitleCategory()
    {
        $this->loadFixtures([]);

        $categoryTile = 'Catg';

        $output = $this->runCommand('flashcard:category:create', [
            'title'            => $categoryTile,
            'desc'             => 'Category Desc',
            '--active'         => true,
            '--no-interaction' => false,
        ]);

        $this->assertFalse(strpos($output, 'Voila') !== false);
    }

    /**
     * @expectedException        InvalidArgumentException
     */
    public function testEmptyCategoryTitle()
    {
        $this->loadFixtures([]);

        $command = new CreateCategoryCommand();

        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', ['askAndValidate']);
        $dialog->expects($this->once())
            ->method('askAndValidate')
            ->will($this->onConsecutiveCalls(
                $this->returnCallback(function () use ($command) {
                    return $command->validateTitle('');
                })));
        $this->runInteractiveCommand($command, $dialog);
    }

    /**
     * @expectedException        InvalidArgumentException
     */
    public function testInvalidParnetCategoryTitle()
    {
        $this->loadFixtures([
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCreateCategory',
        ]);

        $parent = $this->get('moo_flashcard.category.repository')->findOneById(1);

        $command = new CreateCategoryCommand();

        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', ['askAndValidate']);
        $dialog->expects($this->exactly(2))
            ->method('askAndValidate')
            ->will($this->onConsecutiveCalls(
                $this->returnCallback(function () use ($command) {
                    return $command->validateTitle('Category 2');
                }), $this->returnCallback(function () use ($command, $parent) {
                return $command->validateParent($parent->getId() + 100);
            })));
        $this->runInteractiveCommand($command, $dialog);
    }

    public function testCreateSubCategoryInteractive()
    {
        $this->loadFixtures([
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCreateCategory',
        ]);

        $parent = $this->get('moo_flashcard.category.repository')->findOneById(1);

        $command = new CreateCategoryCommand();

        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', ['askAndValidate']);
        $dialog->expects($this->exactly(2))
            ->method('askAndValidate')
            ->will($this->onConsecutiveCalls(
                $this->returnCallback(function () use ($command) {
                    return $command->validateTitle('Category 2');
                }), $this->returnCallback(function () use ($command, $parent) {
                return $command->validateParent($parent->getId());
            })));

        $output = $this->runInteractiveCommand($command, $dialog);

        $this->assertRegExp('/Voila/', $output->getDisplay());
    }
}
