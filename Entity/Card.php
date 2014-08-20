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
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Card is the entity class that represents a record from the database.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 * @ORM\Table(name="card")
 * @ORM\Entity(repositoryClass="Moo\FlashCardBundle\Repository\Card")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"title"},
 *     message="There is an existing card with the same title."
 * )
 */
class Card
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
     * @var string
     *
     * @Gedmo\Slug(fields={"title"}, separator="-")
     * @ORM\Column(name="slug", type="string", length=100, nullable=false, unique=true)
     */
    private $slug;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = false;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "5",
     *      max = "255",
     *      minMessage = "Card title must be at least {{ limit }} characters length",
     *      maxMessage = "Card title cannot be longer than {{ limit }} characters length"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keywords", type="string", length=255, nullable=true)
     */
    private $metaKeywords;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="string", length=255, nullable=true)
     */
    private $metaDescription;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer", nullable=false)
     */
    private $views = "0";

    /**
     * @var \Moo\FlashCardBundle\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="cards")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="CardView", mappedBy="card")
     */
    private $cardViews;

    public function __construct()
    {
        $this->cardViews = new ArrayCollection();
    }

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
     * Set slug
     *
     * @param  string $slug
     * @return Card
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set isActive
     *
     * @param  boolean $isActive
     * @return Card
     */
    public function setActive($isActive)
    {
        $this->active = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Card
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param  string $content
     * @return Card
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set created
     *
     * @param  \DateTime $created
     * @return Card
     */
    public function setCreated($created = null)
    {
        if ($created === null) {
            $created = new \DateTime();
            $this->setUpdated($created);
        }
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param  \DateTime $updated
     * @return Card
     */
    public function setUpdated($updated = null)
    {
        if ($updated === null) {
            $updated = new \DateTime();
        }
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set meta keywords
     *
     * @param  string $keywords
     * @return Card
     */
    public function setMetaKeywords($keywords)
    {
        $this->metaKeywords = $keywords;

        return $this;
    }

    /**
     * Get Meta keywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set meta description
     *
     * @param  string $metadata
     * @return Card
     */
    public function setMetaDescription($description)
    {
        $this->metaDescription = $description;

        return $this;
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set views
     *
     * @param  integer $views
     * @return Card
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set category
     *
     * @param  \Moo\FlashCardBundle\Entity\Category $category
     * @return Card
     */
    public function setCategory(\Moo\FlashCardBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Moo\FlashCardBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreated();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdated();
    }

    public function __toString()
    {
        return $this->getTitle() ? : 'New Card';
    }

}
