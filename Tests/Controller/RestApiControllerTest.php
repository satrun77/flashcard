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
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadApiData',
        ));

        $this->baseUrl = $this->getUrl('moo_flashcard_index') . 'api/';
    }

    public function testGetAnyCard()
    {
        $client = static::createClient();

        $count = 1;
        $client->request('GET', $this->baseUrl . 'cards.json?limit=' . $count);

        $data = json_decode($client->getResponse()->getContent());

        $this->assertCount($count, $data->content);
        $this->assertEquals(200, $data->code);
        $this->assertSuccessfulResponse($client->getResponse(), 'json');
    }

    public function testSearchValidCards()
    {
        $client = static::createClient();

        $count = 10;
        $query = 'card';
        $client->request('GET', $this->baseUrl . 'cards.json?limit=' . $count . '&query=' . $query);

        $data = json_decode($client->getResponse()->getContent());

        $this->assertGreaterThan(0, count($data->content));
        $this->assertEquals(200, $data->code);
        $this->assertSuccessfulResponse($client->getResponse(), 'json');
    }

    public function testSearchValidCard()
    {
        $client = static::createClient();

        $query = 'card';
        $client->request('GET', $this->baseUrl . 'card.json?&query=' . $query);

        $data = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('content', $data);
        $this->assertInstanceOf('stdClass', $data->content);
        $this->assertEquals(200, $data->code);
        $this->assertSuccessfulResponse($client->getResponse(), 'json');
    }

    public function testSearchValidCardAsPopup()
    {
        $client = static::createClient();

        $query = 'card';
        $crawler = $client->request('GET', $this->baseUrl . 'card.html?query=' . $query . '&popup=1');

        $this->assertSuccessfulResponse($client->getResponse(), 'html');
        $this->assertTrue($crawler->filter('.fc-card.popup .close')->count() > 0);
    }

    public function testSearchInvalidCards()
    {
        $client = static::createClient();

        $count = 10;
        $query = 'card' . uniqid();

        // JSON request
        $client->request('GET', $this->baseUrl . 'cards.json?limit=' . $count . '&query=' . $query);
        $data = json_decode($client->getResponse()->getContent());

        $this->assertNotFoundResponse($client->getResponse(), 'json');
        $this->assertEquals(0, count($data->content));
        $this->assertEquals(404, $data->code);

        // HTML request
        $client->request('GET', $this->baseUrl . 'cards.html?limit=' . $count . '&query=' . $query);
        $this->assertNotFoundResponse($client->getResponse(), 'html');
    }

    public function testSearchInvalidCard()
    {
        $client = static::createClient();

        $query = 'card' . uniqid();
        $client->request('GET', $this->baseUrl . 'card.json?&query=' . $query);

        $data = json_decode($client->getResponse()->getContent());

        $this->assertNotFoundResponse($client->getResponse(), 'json');
        $this->assertFalse(isset($data->content));
        $this->assertEquals(404, $data->code);

        // HTML request
        $client->request('GET', $this->baseUrl . 'card.html?query=' . $query);
        $this->assertNotFoundResponse($client->getResponse(), 'html');
    }

    public function testRandomCards()
    {
        $client = static::createClient();

        $client->request('GET', $this->baseUrl . 'cards/random.json?&limit=1');

        $data = json_decode($client->getResponse()->getContent());

        $this->assertSuccessfulResponse($client->getResponse(), 'json');
        $this->assertEquals(200, $data->code);
        $this->assertCount(1, $data->content);
    }

    protected function getContentType($type)
    {
        if ($type == 'json') {
            return 'application/json';
        }

        return 'text/html; charset=UTF-8';
    }

    protected function assertSuccessfulResponse($response, $type = 'json')
    {
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($this->getContentType($type), $response->headers->get('Content-Type'));
    }

    protected function assertNotFoundResponse($response, $type = 'json')
    {
        $this->assertTrue($response->isNotFound());
        $this->assertEquals($this->getContentType($type), $response->headers->get('Content-Type'));
    }

}
