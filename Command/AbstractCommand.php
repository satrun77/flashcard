<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AbstractCommand contains abstracted/helper methods that are needed for a command line object.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
abstract class AbstractCommand extends ContainerAwareCommand
{
    /**
     * An instance of Validator
     *
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     */
    protected function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * Shortcut to return an entity repository class.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository($name)
    {
        return $this->getContainer()->get('moo_flashcard.' . $name . '.repository');
    }

    /**
     * Shortcut to the translator method
     *
     * @param string $message
     * @param array  $params
     */
    protected function trans($message, $params = array())
    {
        return $this->getContainer()->get('translator')->trans($message, $params);
    }

    /**
     * Writes an error message to the output and adds a newline at the end.
     *
     * @param string $message
     * @param array  $params
     */
    protected function error(OutputInterface $output, $message, $params = array())
    {
        return $output->writeln('<error>' . $this->trans($message, $params) . '</error>');
    }

    /**
     * Writes an success message to the output and adds a newline at the end.
     *
     * @param string $message
     */
    protected function success(OutputInterface $output, $message, $params = array())
    {
        return $output->writeln('<info>' . $this->trans($message, $params) . '</info>');
    }

    /**
     * Get instance of Validator
     *
     * @return \Symfony\Component\Validator\Validator
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = $this->getContainer()->get('validator');
        }

        return $this->validator;
    }

    /**
     * Validate a property of an entity
     *
     * @param  object  $entity
     * @return boolean
     */
    protected function validate($entity, $propertyName, $groups = null)
    {
        $violations = $this->getValidator()->validateProperty($entity, $propertyName, $groups);
        if (count($violations) > 0) {
            return $violations[0];
        }

        return true;
    }

}
