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
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\DialogHelper;

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

    /**
     * Builds up the environment to run an interactive command.
     *
     * @param ContainerAwareCommand $command
     * @param DialogHelper          $dialog
     * @param boolean               $reuseKernel
     *
     * @return \CommandTester
     */
    protected function runInteractiveCommand(ContainerAwareCommand $command, DialogHelper $dialog, $reuseKernel = false)
    {
        if (!$reuseKernel) {
            if (null !== static::$kernel) {
                static::$kernel->shutdown();
            }

            $kernel = static::$kernel = $this->createKernel(array('environment' => $this->environment));
            $kernel->boot();
        } else {
            $kernel = $this->getContainer()->get('kernel');
        }

        $application = new Application($kernel);
        $application->setAutoExit(false);
        $application->add($command);
        $application->getHelperSet()->set($dialog, 'dialog');

        $command = $application->find($command->getName());

        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array('command' => $command->getName())
        );

        return $commandTester;
    }

    /**
     * Returns a mock object for console dialog helper
     *
     * @param  string                                         $method
     * @param  string                                         $value
     * @param  array                                          $validator
     * @return \Symfony\Component\Console\Helper\DialogHelper
     */
    protected function getMockDialogHelper($method, $value, $validator)
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array($method));
        $dialog->expects($this->any())
                ->method($method)
                ->will($this->returnCallback(function () use ($validator, $value) {
                            return $validator[0]->$validator[1]($value);
                        }));
        $dialog->setInputStream($this->getInputStream($value));

        return $dialog;
    }

    /**
     * Get input stream for console interactive input
     *
     * @param  string   $input
     * @return resource of type stream
     */
    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }

}
