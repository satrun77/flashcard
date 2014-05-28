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
 * LoadCardData used to load card fixtures. Mostly used for load test data in PHP Unit Test.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class LoadCardData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $category = $this->getReference('category1');
        $card1 = $this->createCard('Card 1', $category, 'Card 1...', '', '');
        $card2 = $this->createCard('Card 2', $category, 'Card 2...', '', '');

        $manager->persist($card1);
        $manager->persist($card2);

        $manager->flush();
    }

    /**
     * Helper method to create a card
     *
     * @param  type                             $title
     * @param  type                             $categoryId
     * @param  type                             $content
     * @param  type                             $keywords
     * @param  type                             $description
     * @param  type                             $slug
     * @param  type                             $isActive
     * @param  type                             $views
     * @return \Moo\FlashCardBundle\Entity\Card
     */
    protected function createCard($title, $category, $content, $keywords, $description, $slug = null, $isActive = 1, $views = 0)
    {
        $card = new Entity\Card;
        $card->setCreated();
        $card->setTitle($title);
        $card->setCategory($category);
        $card->setContent($content);
        $card->setActive($isActive);
        $card->setMetaKeywords($keywords);
        $card->setMetaDescription($description);
        if ($slug !== null) {
            $card->setSlug($slug);
        }
        $card->setViews($views);

        return $card;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }

}
