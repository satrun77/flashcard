<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Tests\Entity;

use Moo\FlashCardBundle\Entity\Category;
use Moo\FlashCardBundle\Tests\AbstractWebTestCase;

/**
 * CategoryTest contains test cases for the Category entity class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CategoryTest extends AbstractWebTestCase
{

    public function testCreateCategory()
    {
        $category = new Category;
        $category->setTitle('Title 1');
        $category->setDescription('Content 2');
        $category->setCreated(new \DateTime);
        $category->setActive(true);

        $subCategory = new Category;
        $subCategory->setTitle('Title 2');
        $subCategory->setDescription('Content 2');
        $subCategory->setCreated(new \DateTime);
        $subCategory->setActive(false);
        $subCategory->setParent($category);

        $this->assertInstanceOf('\Moo\FlashCardBundle\Entity\Category', $subCategory->getParent());
        $this->assertEquals($category->getTitle(), $subCategory->getParent()->getTitle());
        $this->assertEquals($category->getDescription(), $subCategory->getParent()->getDescription());
        $this->assertInstanceOf('\DateTime', $subCategory->getParent()->getCreated());
        $this->assertInstanceOf('\DateTime', $subCategory->getCreated());
    }

    public function testInsertingCategory()
    {
        $this->loadFixtures(array());

        // Category instance
        $category = new Category;
        $category->setCreated();
        $category->setTitle('Title 1');
        $category->setDescription('Content 1');
        $category->setActive(true);

        // insert category into the database
        $this->em->persist($category);
        $this->em->flush();

        $this->assertTrue($category->isActive());
        $this->assertGreaterThan(0, $category->getId());
        $this->assertEquals($category->getUpdated()->getTimestamp(), $category->getCreated()->getTimestamp());
    }

}
