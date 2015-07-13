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
use Doctrine\ORM\Query\Expr;

/**
 * Category is a repository class for the card category entity.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Category extends EntityRepository
{
    /**
     * Fetch active categories
     *
     * @return array
     */
    public function fetchCategories()
    {
        $builder = $this->getEntityManager()->createQueryBuilder();

        $builder
            ->select('p', 'c')
            ->from('MooFlashCardBundle:Category', 'p')
            ->join('p.cards', 'c', Expr\Join::WITH, 'c.active = 1')
            ->where('p.active = 1')
            ->orderBy('p.title', 'ASC');

        return $builder->getQuery()->getResult();
    }
}
