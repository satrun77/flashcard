<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Tests\Command;

use Moo\FlashCardBundle\Tests\AbstractWebTestCase;
use \Moo\FlashCardBundle\Command\CreateCardCommand;

/**
 * CreateCardTest contains test cases for the command line CreateCard class.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CreateCardTest extends AbstractWebTestCase
{

    public function testCreateCard()
    {
        $this->loadFixtures(array(
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCreateCategory',
        ));

        $cardTile = 'Test Card 1';
        $cardSlug = 'test-card-1';
        $category = $this->get('moo_flashcard.category.repository')->findOneById(1);

        $output = $this->runCommand('flashcard:card:create', array(
            'title'            => $cardTile,
            'content'          => '<p>This is the card description.</p>',
            'slug'             => $cardSlug,
            'category'         => $category->getId(),
            '--active'         => true,
            '--no-interaction' => true
        ));

        $this->assertRegExp('/Voila/', $output);

        $card = $this->get('moo_flashcard.card.repository')->findOneBySlug($cardSlug);
        $this->assertInstanceOf('\\Moo\\FlashCardBundle\\Entity\\Card', $card);
        $this->assertTrue($card->isActive());

        return $card;
    }

    public function testCreateCardWithInvalidCategoryExecute()
    {
        $this->loadFixtures(array());

        $output = $this->runCommand('flashcard:card:create', array(
            'title'            => 'Test Card 1',
            'content'          => 'Card Desc',
            'category'         => 999999999, // Invalid category Id!
            'keywords'         => '',
            '--no-interaction' => true
        ));

        $this->assertFalse(strpos($output, 'Voila') !== false);
    }

    /**
     *
     * @expectedException        InvalidArgumentException
     */
    public function testEmptyCardTitle()
    {
        $this->loadFixtures(array());

        $command = new CreateCardCommand;

        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askAndValidate'));
        $dialog->expects($this->once())
                ->method('askAndValidate')
                ->will($this->onConsecutiveCalls(
                                $this->returnCallback(function () use ($command) {
                                    return $command->validateTitle('');
                                })
        ));
        $this->runInteractiveCommand($command, $dialog);
    }

    /**
     *
     * @expectedException        InvalidArgumentException
     */
    public function testEmptyCardContent()
    {
        $this->loadFixtures(array());

        $command = new CreateCardCommand;

        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askAndValidate'));
        $dialog->expects($this->exactly(2))
                ->method('askAndValidate')
                ->will($this->onConsecutiveCalls(
                                $this->returnCallback(function () use ($command) {
                                    return $command->validateTitle('Card 1 Title');
                                }), $this->returnCallback(function () use ($command) {
                                    return $command->validateContent('');
                                })
        ));
        $this->runInteractiveCommand($command, $dialog);
    }

    /**
     *
     * @expectedException        InvalidArgumentException
     */
    public function testInvalidCategory()
    {
        $this->loadFixtures(array(
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCreateCategory',
        ));

        $category = $this->get('moo_flashcard.category.repository')->findOneById(1);

        $command = new CreateCardCommand;

        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askAndValidate'));
        $dialog->expects($this->exactly(3))
                ->method('askAndValidate')
                ->will($this->onConsecutiveCalls(
                                $this->returnCallback(function () use ($command) {
                                    return $command->validateTitle('Card 2 Title');
                                }), $this->returnCallback(function () use ($command) {
                                    return $command->validateContent('Card 2 content...');
                                }), $this->returnCallback(function () use ($command, $category) {
                                    return $command->validateCategory($category->getId() + 100);
                                })
        ));
        $this->runInteractiveCommand($command, $dialog);
    }

    public function testCreateCardInteractive()
    {
        $this->loadFixtures(array(
            'Moo\FlashCardBundle\DataFixtures\ORM\LoadCreateCategory',
        ));

        $category = $this->get('moo_flashcard.category.repository')->findOneById(1);

        $command = new CreateCardCommand;

        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askAndValidate'));
        $dialog->expects($this->exactly(3))
                ->method('askAndValidate')
                ->will($this->onConsecutiveCalls(
                                $this->returnCallback(function () use ($command) {
                                    return $command->validateTitle('Card 2 Title');
                                }), $this->returnCallback(function () use ($command) {
                                    return $command->validateContent('Card 2 content...');
                                }), $this->returnCallback(function () use ($command, $category) {
                                    return $command->validateCategory($category->getId());
                                })
        ));
        $output = $this->runInteractiveCommand($command, $dialog);

        $this->assertRegExp('/Voila/', $output->getDisplay());
    }

}
