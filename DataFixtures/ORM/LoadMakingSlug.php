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
 * LoadMakingSlug used to load fixtures for making slug test case.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class LoadMakingSlug extends AbstractFixture
{

    public function load(ObjectManager $manager)
    {
        $category = $this->createCategory('Category 1', '');
        $manager->persist($category);

        $card1 = $this->createCard('Card 1', $category, 'Card 1...', '', '');
        $manager->persist($card1);

        $manager->flush();
    }

}
