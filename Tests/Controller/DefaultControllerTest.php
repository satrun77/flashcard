<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Tests\Controller;

use Moo\FlashCardBundle\Tests\AbstractWebTestCase;

/**
 * DefaultControllerTest contains test cases for the default controller class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class DefaultControllerTest extends AbstractWebTestCase
{
    public function testViewACardDetails()
    {
        $this->loadFixtures([
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCreateCard',
        ]);

        $client = static::createClient();

        /** @var \Symfony\Component\DomCrawler\Crawler $crawler */
        $crawler = $client->request('GET', $this->getUrl('moo_flashcard_card', ['slug' => 'card-1']));

        $this->assertTrue($crawler->filter('html:contains("Card 1...")')->count() > 0);
    }

    public function testViewIndex()
    {
        $this->loadFixtures([
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCategoryCards',
        ]);

        $client = static::createClient();

        /** @var \Symfony\Component\DomCrawler\Crawler $crawler */
        $crawler = $client->request('GET', $this->getUrl('moo_flashcard_index'));

        $this->assertTrue($crawler->filter('html:contains("Card 1...")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Card 2...")')->count() > 0);
    }

    public function testViewNotFoundCard()
    {
        $this->loadFixtures([]);

        $client = static::createClient();

        $client->request('GET', $this->getUrl('moo_flashcard_card', ['slug' => 'card-1']));

        $this->assertTrue($client->getResponse()->isNotFound());
    }
}
