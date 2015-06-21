<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * DefaultController is the default frontend controller (homepage, & card details view).
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * <p>Shows preview of the first n cards, with the options to load more cards (the next n cards).</p>
     * <p>Each card is displayed as a thumbnail. Clicking the thumbnail, will expand the card to show all of it
     * details.</p>
     * <p>There is also, a search text field at the top right side to search the cards.</p>
     *
     * @ApiDoc(
     *  section="Public pages",
     *  resource=true,
     *  description="The home page."
     * )
     *
     * @return \Symfony\Component\HttpFoundation\Response A Response instance
     */
    public function indexAction()
    {
        $limit = 24;
        $cardService = $this->get('moo_flashcard.card.repository');
        $cards = $cardService->fetchCards(1, $limit);

        return $this->render('MooFlashCardBundle:Default:index.html.twig', [
            'cards' => $cards,
            'limit' => $limit,
        ]);
    }

    /**
     * Shows the details of a card.
     *
     * @ApiDoc(
     *  section="Public pages",
     *  resource=true,
     *  description="Displays a card details.",
     *   statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the card is not found"
     *   }
     * )
     *
     * @param string $slug The slug value of a card.
     *
     * @return \Symfony\Component\HttpFoundation\Response A Response instance
     */
    public function viewAction($slug)
    {
        $cardService = $this->get('moo_flashcard.card.repository');
        $card = $cardService->findOneBySlugJoinedToCategory($slug);
        if (!$card) {
            throw $this->createNotFoundException('The flash card you are looking for does not exist');
        }
        $this->updateViews($card, $cardService);

        return $this->render('MooFlashCardBundle:Default:view.html.twig', [
            'card'  => $card,
            'popup' => null,
        ]);
    }

    /**
     * Update flashcard view counter
     *
     * @param \Moo\FlashCardBundle\Entity\Card     $card
     * @param \Moo\FlashCardBundle\Repository\Card $cardService
     *
     * @return \Moo\FlashCardBundle\Entity\Card
     */
    protected function updateViews($card, $cardService)
    {
        if ($card) {
            $viewsService = $this->get('moo_flashcard.cardview.repository');
            $status = $viewsService->insertView($card, $this->container->get('request')->getClientIp());
            if ($status) {
                $cardService->incrementViews($card);
            }
        }

        return $card;
    }
}
