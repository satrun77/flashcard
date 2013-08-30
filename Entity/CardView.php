<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CardView is the entity class that represents a record from the database.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 * @ORM\Table(name="card_view")
 * @ORM\Entity(repositoryClass="Moo\FlashCardBundle\Repository\CardView")
 */
class CardView
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="ip", type="integer", nullable=false)
     */
    private $ip;

    /**
     * @var \Moo\FlashCardBundle\Entity\Card
     *
     * @ORM\OneToOne(targetEntity="Card", inversedBy="cardViews")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="card_id", referencedColumnName="id")
     * })
     */
    private $card;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set timestamp
     *
     * @param  \DateTime $timestamp
     * @return CardView
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set ip
     *
     * @param  integer  $ip
     * @return CardView
     */
    public function setIp($ip)
    {
        $this->ip = ip2long($ip);

        return $this;
    }

    /**
     * Get ip
     *
     * @return integer
     */
    public function getIp()
    {
        return long2ip($this->ip);
    }

    /**
     * Set card
     *
     * @param  \Moo\FlashCardBundle\Entity\Card $card
     * @return CardView
     */
    public function setCard(\Moo\FlashCardBundle\Entity\Card $card = null)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Get card
     *
     * @return \Moo\FlashCardBundle\Entity\Card
     */
    public function getCard()
    {
        return $this->card;
    }

}
