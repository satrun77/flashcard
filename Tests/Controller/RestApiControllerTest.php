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
 * RestApiControllerTest contains test cases for the REST API controller.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class RestApiControllerTest extends AbstractWebTestCase
{
    protected $baseUrl;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array(
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCategoryData',
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCardData',
        ));

        $this->baseUrl = $this->getUrl('moo_flashcard_index') . 'api/';
    }

    public function testGetAnyCardJson()
    {
        $client = static::createClient();

        $count = 1;
        $client->request('GET', $this->baseUrl . 'cards.json?limit=' . $count);

        $data = json_decode($client->getResponse()->getContent());

        $this->assertCount($count, $data->content);
        $this->assertEquals(200, $data->code);
    }

    public function testSearchValidCardsJson()
    {
        $client = static::createClient();

        $count = 10;
        $query = 'card';
        $client->request('GET', $this->baseUrl . 'cards.json?limit=' . $count . '&query=' . $query);

        $data = json_decode($client->getResponse()->getContent());

        $this->assertGreaterThan(0, count($data->content));
        $this->assertEquals(200, $data->code);
    }

    public function testSearchInvalidCardsJson()
    {
        $client = static::createClient();

        $count = 10;
        $query = 'card' . uniqid();
        $client->request('GET', $this->baseUrl . 'cards.json?limit=' . $count . '&query=' . $query);

        $data = json_decode($client->getResponse()->getContent());

        $this->assertEquals(0, count($data->content));
        $this->assertEquals(404, $data->code);
    }

    public function testSearchValidCardJson()
    {
        $client = static::createClient();

        $query = 'card';
        $client->request('GET', $this->baseUrl . 'card.json?&query=' . $query);

        $data = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('content', $data);
        $this->assertInstanceOf('stdClass', $data->content);
        $this->assertEquals(200, $data->code);
    }

    public function testSearchInvalidCardJson()
    {
        $client = static::createClient();

        $query = 'card' . uniqid();
        $client->request('GET', $this->baseUrl . 'card.json?&query=' . $query);

        $data = json_decode($client->getResponse()->getContent());

        $this->assertFalse(isset($data->content));
        $this->assertEquals(404, $data->code);
    }

    public function testSearchValidCardAsPopupJson()
    {
        $client = static::createClient();

        $query = 'card';
        $crawler = $client->request('GET', $this->baseUrl . 'card.html?query=' . $query . '&popup=1');

        $this->assertTrue($crawler->filter('.fc-card.popup .close')->count() > 0);
    }

}
