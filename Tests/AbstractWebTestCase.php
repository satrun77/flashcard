<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * AbstractWebTestCase contains abstracted/helper methods that are needed for a test class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
abstract class AbstractWebTestCase extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    protected $doctrine;

    protected function loadFixtures(array $classNames, $omName = null, $registryName = 'doctrine', $purgeMode = null)
    {
        parent::loadFixtures($classNames, $omName, $registryName, $purgeMode);

        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->em = $this->doctrine->getManager();
    }

    protected function tearDown()
    {
        parent::tearDown();

        if (null !== $this->em) {
            $this->em->close();
        }
    }

    /**
     * Shortcut method to get a service by id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

}
