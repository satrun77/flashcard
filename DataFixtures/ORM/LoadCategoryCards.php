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
 * LoadCategoryCards used to load fixtures with one category & n cards for any test case that requires these data to
 * exists in the database.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class LoadCategoryCards extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $category = $this->createCategory('Category 1', '');
        $manager->persist($category);

        $card1 = $this->createCard('Card 1', $category, 'Card 1...', '', '');
        $card2 = $this->createCard('Card 2', $category, 'Card 2...', '', '');
        $manager->persist($card1);
        $manager->persist($card2);

        $manager->flush();
    }
}
