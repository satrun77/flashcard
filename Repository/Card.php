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
use Doctrine\ORM\NoResultException;
use Moo\FlashCardBundle\Entity;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\Paginator;

/**
 * Card is a repository class for the card entity.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Card extends EntityRepository implements PaginatorAwareInterface
{
    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * Set the KnpPaginator instance.
     *
     * @param Paginator $paginator
     *
     * @return $this
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Return the KnpPaginator instance.
     *
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * Return query to select all active cards
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryAllCards()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('p, c')->from('MooFlashCardBundle:Card', 'p');
        $query->join('p.category', 'c');
        $query->where('p.active = 1');
        $query->where('c.active = 1');
        $query->orderBy('p.id', 'ASC');

        return $query;
    }

    /**
     * Return query to search for active cards
     *
     * @param string $search
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQuerySearchCards($search)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('p, c')->from('MooFlashCardBundle:Card', 'p');
        $query->join('p.category', 'c');
        $query->where('p.content LIKE :name');
        $query->orWhere('p.title LIKE :name');
        $query->orWhere('p.slug LIKE :name');
        $query->andWhere('p.active = 1');
        $query->andWhere('c.active = 1');
        $query->setParameter('name', '%' . $search . '%');
        $query->orderBy('p.id', 'ASC');

        return $query;
    }

    /**
     * Return all cards
     *
     * @param int $page
     * @param int $limit
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function fetchCards($page = 1, $limit = 20)
    {
        return $this->getPaginator()->paginate($this->getQueryAllCards(), $page, $limit);
    }

    /**
     * Search for cards by keyword
     *
     * @param string $search
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function search($search, $page = 1, $limit = 20)
    {
        return $this->getPaginator()->paginate($this->getQuerySearchCards($search), $page, $limit);
    }

    /**
     * Search for a card by keyword
     *
     * @param string $search
     *
     * @return null|\Moo\FlashCardBundle\Entity\Card
     */
    public function searchForOne($search)
    {
        if (empty($search)) {
            return;
        }

        try {
            return $this->getQuerySearchCards($search)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return;
        }
    }

    /**
     * Search for a card by a slug
     *
     * @param string $slug
     *
     * @return null|\Moo\FlashCardBundle\Entity\Card
     */
    public function findOneBySlugJoinedToCategory($slug)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('p, c')->from('MooFlashCardBundle:Card', 'p');
        $query->join('p.category', 'c');
        $query->where('p.slug = :slug');
        $query->setParameter('slug', $slug);
        $query->setMaxResults(1);

        try {
            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return;
        }
    }

    /**
     * Return random cards
     *
     * @param int $limit
     *
     * @return array
     */
    public function fetchRadomCards($limit = 30)
    {
        $em = $this->getEntityManager();

        // retrieve the highest card id
        $max = $em->createQuery('SELECT MAX(c.id) FROM MooFlashCardBundle:Card c')
            ->getSingleScalarResult();

        // get random cards
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('p, c')->from('MooFlashCardBundle:Card', 'p');
        $query->join('p.category', 'c');
        $query->where('p.id >= :rand');
        $query->andWhere('p.active = 1');
        $query->andWhere('c.active = 1');
        $query->setParameter('rand', mt_rand(0, $max - $limit));
        $query->setMaxResults($limit);
        $query->orderBy('p.id', 'ASC');

        $cards = $query->getQuery()->getResult();
        shuffle($cards);

        return $cards;
    }

    /**
     * Update card view counter by 1
     *
     * @param \Moo\FlashCardBundle\Entity\Card $card
     */
    public function incrementViews(Entity\Card $card)
    {
        $card->setViews($card->getViews() + 1);
        $em = $this->getEntityManager();
        $em->flush();
    }
}
