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

use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadCreateCategory used to load fixtures with one category for any test case that requires a category to exists in
 * the database.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class LoadCreateCategory extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $category = $this->createCategory('Category 1', '');
        $manager->persist($category);

        $manager->flush();
    }
}
