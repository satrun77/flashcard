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

use Moo\FlashCardBundle\Entity\Card;
use Moo\FlashCardBundle\Tests\AbstractWebTestCase;

/**
 * CardTest contains test cases for the Card entity class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CardTest extends AbstractWebTestCase
{

    public function testGetTitle()
    {
        $title = "Title";
        $content = 'Content';
        $card = new Card;
        $card->setTitle($title);
        $card->setContent($content);
        $card->setActive(true);

        $this->assertEquals($title, $card->getTitle());
    }

    public function testIsActive()
    {
        $card = new Card;
        $card->setActive(true);

        $this->assertTrue($card->isActive());
    }

    public function testMakingSlug()
    {
        $this->loadFixtures(array(
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCategoryData',
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCardData',
        ));

        $cardService = $this->get('moo_flashcard.card.repository');
        $card = $cardService->findOneBySlugJoinedToCategory('card-1');

        $this->assertInstanceOf('\Moo\FlashCardBundle\Entity\Card', $card);
    }

}
