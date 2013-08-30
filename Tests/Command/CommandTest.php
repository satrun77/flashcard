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

/**
 * CommandTest contains test cases for the command line class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CommandTest extends AbstractWebTestCase
{

    public function testCreateCategoryExecute()
    {
        $categoryTile = 'Test Category ' . uniqid();

        $output = $this->runCommand('flashcard:category:create', array(
            'title'            => $categoryTile,
            'desc'             => 'Category Desc',
            '--active'         => true,
            '--no-interaction' => true
        ));

        $this->assertRegExp('/Voila/', $output);

        $category = $this->get('moo_flashcard.category.repository')->findOneByTitle($categoryTile);
        $this->assertInstanceOf('\Moo\FlashCardBundle\Entity\Category', $category);

        $output = $this->runCommand('flashcard:category:create', array(
            'title'            => 'Test Category 2',
            'desc'             => 'Category Desc',
            'parent'           => $category->getId(),
            '--no-interaction' => true
        ));

        $this->assertRegExp('/Voila/', $output);
    }

    public function testCreateCardWithInvalidCategoryExecute()
    {
        $output = $this->runCommand('flashcard:card:create', array(
            'title'            => 'Test Card 1',
            'content'          => 'Card Desc',
            'category'         => 999999999, // Invalid category Id!
            'keywords'         => '',
            '--no-interaction' => true
        ));

        $this->assertRegExp('/Invalid category Id/', $output);
    }

}
