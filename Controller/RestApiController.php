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

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Moo\FlashCardBundle\Response\RestResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * RestApiController is the default REST API controller.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class RestApiController extends FOSRestController
{

    /**
     * Returns a paginated list of cards.
     *
     * @ApiDoc(
     *  section="Public API (RESTful)",
     *  resource=true,
     *   statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the cards are not found"
     *   }
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page number.")
     * @QueryParam(name="limit", requirements="\d+", default="20", description="Max number of cards to return.")
     * @QueryParam(name="query", requirements="", default="", description="Limit result by query")
     *
     * @param int    $page
     * @param int    $limit
     * @param string $query
     */
    public function getCardsAction($page = 1, $limit = 20, $query = '')
    {
        $cardService = $this->get('moo_flashcard.card.repository');

        if (!empty($query)) {
            $data = $cardService->search($query, $page, $limit);
        } else {
            $data = $cardService->fetchCards($page, $limit);
        }
        $total = $data->getTotalItemCount();

        $response = $this->createResponse($data->getItems(), 'No flash cards found.');
        $response->extra = array('total' => $total);

        $view = $this->view($response, $response->code)
                ->setTemplate('MooFlashCardBundle:Card:list.html.twig');

        if ('html' === $this->getRequestFormat($view)) {
            $view->setData(array(
                'cards' => $response->content,
            ));
        }

        $return = $this->handleView($view);

        // for html request, return the error message
        if (!$total && 'html' === $this->getRequestFormat($view)) {
            return $return->setContent($response->errorMessage);
        }

        return $return;
    }

    /**
     * Search for a card.
     *
     * @ApiDoc(
     *  section="Public API (RESTful)",
     *  resource=true,
     *   statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the card is not found"
     *   }
     * )
     *
     * @QueryParam(name="query", requirements="", default="", description="Search query")
     * @QueryParam(name="pageLink", requirements="boolean", default=1, description="To indicate whether to include a link to the card page (html format only).")
     * @QueryParam(name="shareLink", requirements="boolean", default=1, description="To indicate whether to include the share buttons (twitter & google+) (html format only).")
     * @QueryParam(name="popup", requirements="boolean", default=1, description="To indicate whether to the html is to be displayed as a popup box. This will include 'popup class name and close button' (html format only).")
     *
     * @param string  $query
     * @param boolean $pageLink
     * @param boolean $shareLink
     * @param boolean $popup
     */
    public function getCardAction($query = '', $pageLink = true, $shareLink = true, $popup = true)
    {
        $cardService = $this->get('moo_flashcard.card.repository');
        $data = $cardService->searchForOne($query);

        $response = $this->createResponse($data, 'The flash card you are looking for does not exist.');

        $view = $this->view($response, $response->code)
                ->setTemplate('MooFlashCardBundle:Card:details.html.twig');

        // FOSRestBundle, see https://github.com/FriendsOfSymfony/FOSRestBundle/issues/299
        if ('html' === $this->getRequestFormat($view)) {
            $view->setData(array(
                'card'       => $data,
                'page_link'  => (boolean) $pageLink,
                'share_link' => (boolean) $shareLink,
                'popup'      => (boolean) $popup
            ));
        }

        $return = $this->handleView($view);

        // for html request, return the error message
        if (!$data && 'html' === $this->getRequestFormat($view)) {
            return $return->setContent($response->errorMessage);
        }

        return $return;
    }

    /**
     * Returns a random selection of cards.
     *
     * @ApiDoc(
     *  section="Public API (RESTful)",
     *  resource=true,
     *   statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the cards are not found"
     *   }
     * )
     *
     * @QueryParam(name="limit", requirements="\d+", default="30", description="Max number of cards to return.")
     *
     * @param int $limit
     */
    public function getCardsRandomAction($limit = 30)
    {
        $cardService = $this->get('moo_flashcard.card.repository');
        $data = $cardService->fetchRadomCards($limit);

        $response = $this->createResponse($data, 'No flash cards found.');

        $view = $this->view($response, $response->code)
                ->setTemplate("MooFlashCardBundle:Card:list.html.twig");

        // FOSRestBundle, see https://github.com/FriendsOfSymfony/FOSRestBundle/issues/299
        if ('html' === $this->getRequestFormat($view)) {
            $view->setData(array(
                'cards' => $response->content,
            ));
        }

        $return = $this->handleView($view);

        // for html request, return the error message
        if (!$data && 'html' === $this->getRequestFormat($view)) {
            return $return->setContent($response->errorMessage);
        }

        return $return;
    }

    /**
     * Return request format 'e.g. html, json)
     *
     * @param  \FOS\RestBundle\View\View $view
     * @return string
     */
    protected function getRequestFormat(\FOS\RestBundle\View\View $view)
    {
        $request = $this->getRequest();

        return ($view->getFormat() ?: $request->getRequestFormat());
    }

    /**
     * Create the web service response
     *
     * @param  array|null|object                          $content
     * @param  string                                     $errorMessage
     * @param  int                                        $code
     * @return \Moo\FlashCardBundle\Response\RestResponse
     */
    protected function createResponse($content, $errorMessage = '', $code = null)
    {
        $response = new RestResponse();
        $response->content = $content;
        if (!$content) {
            $response->code = 404;
            $response->errorMessage = $errorMessage;
        }

        if (null !== $code) {
            $response->code = $code;
        }

        return $response;
    }

}
