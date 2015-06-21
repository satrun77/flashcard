<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Moo\FlashCardBundle\Entity;

/**
 * CardView is a repository class for the card view entity.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CardView extends EntityRepository
{
    /**
     * Insert a view count
     *
     * @param Entity\Card $card
     * @param string      $ip
     *
     * @return bool|Entity\CardView
     */
    public function insertView(Entity\Card $card, $ip)
    {
        $view = false;
        if ($card->getId() == 0) {
            return $view;
        }

        $isExists = $this->findOneBy(['card' => $card, 'ip' => ip2long($ip)]);
        if (!$isExists) {
            $view = new Entity\CardView();
            $view->setCard($card);
            $view->setTimestamp(new \DateTime());
            $view->setIp($ip);
            $em = $this->getEntityManager();
            $em->persist($view);
            $em->flush();
        }

        return $view;
    }
}
