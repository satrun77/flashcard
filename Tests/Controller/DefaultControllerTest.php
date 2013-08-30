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

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array(
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCategoryData',
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCardData',
        ));
    }

    public function testViewACardDetails()
    {
        $client = static::createClient();

        /** @var \Symfony\Component\DomCrawler\Crawler $crawler */
        $crawler = $client->request('GET', $this->getUrl('moo_flashcard_card', array('slug' => 'card-1')));

        $this->assertTrue($crawler->filter('html:contains("Card 1...")')->count() > 0);
    }

    public function testViewIndex()
    {
        $client = static::createClient();

        /** @var \Symfony\Component\DomCrawler\Crawler $crawler */
        $crawler = $client->request('GET', $this->getUrl('moo_flashcard_index'));

        $this->assertTrue($crawler->filter('html:contains("Card 1...")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Card 2...")')->count() > 0);
    }

}
