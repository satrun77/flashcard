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
use Symfony\Component\Console\Input\InputInterface;

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
     * Add dialog interaction for each argument in the command with optional validation
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @param  type                                              $value
     * @return type
     */
    protected function addArgumentsInteract(InputInterface $input, OutputInterface $output)
    {
        $arguments = $this->getDefinition()->getArguments();
        foreach ($arguments as $argument) {
            $name = $argument->getName();
            $method = 'validate' . ucfirst($name);
            if (!$input->getArgument($name)) {
                $calback = function ($value) {
                    return $value;
                };
                if (method_exists($this, $method)) {
                    $calback = array($this, $method);
                }
                $value = $this->getHelper('dialog')->askAndValidate(
                        $output, 'Please enter (' . $argument->getDescription() . '): ', $calback, false, $argument->getDefault()
                );
                $input->setArgument($name, $value);
            }
        }
    }

    /**
     * Get instance of Validator
     *
     * @return \Symfony\Component\Validator\Validator
     */
    protected function getValidator()
    {
        if (null == $this->validator) {
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
    protected function validate($entity, $propertyName)
    {
        $violations = $this->getValidator()->validateProperty($entity, $propertyName);
        if (count($violations) > 0) {
            return $violations[0];
        }

        return true;
    }

}
