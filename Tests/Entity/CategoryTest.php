<?php

/*
 * This file is part of the Moo\FlashCard package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCard\Tests\Entity;


use Moo\FlashCard\Tests\BaseTestCase;

/**
 * CategoryTest contains test cases for the Category entity class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CategoryTest extends BaseTestCase
{
    public function testCreateCategory()
    {
        $category1 = $this->category();
        $category2 = $this->category([
            'title' => 'Category 2',
            'parent' => $category1->id,
        ]);

        $this->assertInstanceOf('\Moo\FlashCard\Entity\Category', $category2->parent()->first());
        $this->assertEquals($category1->title, $category2->parent()->first()->title);
    }

    /**
     * @expectedException   InvalidArgumentException
     */
    public function testShortCategoryTitle()
    {
        $this->category([
            'title' => 'Cat',
        ]);
    }
}
