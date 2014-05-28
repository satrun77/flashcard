<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Moo\FlashCardBundle\Entity;

/**
 * LoadCategoryData used to load card category fixtures. Mostly used for load test data in PHP Unit Test.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $category1 = $this->createCategory('Category 1', '');
        $category2 = $this->createCategory('Category 2', '', null, 0);
        $category3 = $this->createCategory('Category 3', '', $category2);

        $manager->persist($category1);
        $manager->persist($category2);
        $manager->persist($category3);

        $manager->flush();

        $this->addReference('category1', $category1);
    }

    /**
     * Helper method to create a category
     *
     * @param  type                                 $title
     * @param  type                                 $description
     * @param  type                                 $parentId
     * @param  type                                 $isActive
     * @return \Moo\FlashCardBundle\Entity\Category
     */
    protected function createCategory($title, $description, $parent = null, $isActive = 1)
    {
        $category = new Entity\Category;
        $category->setCreated();
        $category->setDescription($description);
        $category->setActive($isActive);
        $category->setTitle($title);
        $category->setParent($parent);

        return $category;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

}
