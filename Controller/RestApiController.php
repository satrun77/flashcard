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

use FOS\RestBundle;
use FOS\RestBundle\Controller\FOSRestController;
use Moo\FlashCardBundle\Response\RestResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

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
     *   },
     *   filters={
     *      {"name"="limit",    "dataType"="integer", "required"=false, "description"="Max number of cards to return."},
     *      {"name"="page",     "dataType"="integer", "required"=false, "description"="Page number."},
     *      {"name"="query",    "dataType"="string",  "required"=false, "description"="Limit result by query."},
     *      {"name"="category", "dataType"="integer", "required"=false, "description"="Limit result by a category ID."}
     *   }
     * )
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCardsAction(Request $request)
    {
        $cardService = $this->get('moo_flashcard.card.repository');

        $data = $cardService->fetchCardsBy(
            [
                'query'    => $request->get('query', ''),
                'category' => $request->query->getInt('category', 0),
            ],
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );
        $total = $data->getTotalItemCount();

        $response = $this->createResponse($data->getItems(), 'No flash cards found.');
        $response->extra = ['total' => $total];

        $view = $this
            ->view($response, $response->code)
            ->setTemplate('MooFlashCardBundle:Card:list.html.twig');

        if ('html' === $this->getRequestFormat($view, $request)) {
            $view->setData([
                'cards' => $response->content,
            ]);
        }

        $return = $this->handleView($view);

        // for html request, return the error message
        if (!$total && 'html' === $this->getRequestFormat($view, $request)) {
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
     *   },
     *   filters={
     *      {"name"="query",     "dataType"="string",  "required"=false, "description"="Search query"},
     *      {"name"="pageLink",  "dataType"="boolean", "required"=false, "description"="To indicate whether to include a link to the card page (html format only)."},
     *      {"name"="shareLink", "dataType"="boolean", "required"=false, "description"="To indicate whether to include the share buttons (twitter & google+) (html format only)."},
     *      {"name"="popup",     "dataType"="boolean", "required"=false, "description"="To indicate whether to the html is to be displayed as a popup box. This will include 'popup class name and close button' (html format only)."}
     *   }
     * )
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCardAction(Request $request)
    {
        $query = $request->get('query', '');
        $pageLink = $request->query->getBoolean('pageLink', true);
        $shareLink = $request->query->getBoolean('shareLink', true);
        $popup = $request->query->getBoolean('popup', true);

        $cardService = $this->get('moo_flashcard.card.repository');
        $data = $cardService->searchForOne($query);

        $response = $this->createResponse($data, 'The flash card you are looking for does not exist.');

        $view = $this
            ->view($response, $response->code)
            ->setTemplate('MooFlashCardBundle:Card:details.html.twig');

        // FOSRestBundle, see https://github.com/FriendsOfSymfony/FOSRestBundle/issues/299
        if ('html' === $this->getRequestFormat($view, $request)) {
            $view->setData([
                'card'       => $data,
                'page_link'  => (boolean) $pageLink,
                'share_link' => (boolean) $shareLink,
                'popup'      => (boolean) $popup,
            ]);
        }

        $return = $this->handleView($view);

        // for html request, return the error message
        if (!$data && 'html' === $this->getRequestFormat($view, $request)) {
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
     *   },
     *   filters={
     *      {"name"="limit",    "dataType"="integer", "required"=false, "description"="Max number of cards to return."}
     *   }
     * )
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCardsRandomAction(Request $request)
    {
        $limit = $request->get('limit', 30);
        $cardService = $this->get('moo_flashcard.card.repository');
        $data = $cardService->fetchRadomCards($limit);

        $response = $this->createResponse($data, 'No flash cards found.');

        $view = $this
            ->view($response, $response->code)
            ->setTemplate('MooFlashCardBundle:Card:list.html.twig');

        // FOSRestBundle, see https://github.com/FriendsOfSymfony/FOSRestBundle/issues/299
        if ('html' === $this->getRequestFormat($view, $request)) {
            $view->setData([
                'cards' => $response->content,
            ]);
        }

        $return = $this->handleView($view);

        // for html request, return the error message
        if (!$data && 'html' === $this->getRequestFormat($view, $request)) {
            return $return->setContent($response->errorMessage);
        }

        return $return;
    }

    /**
     * Return request format 'e.g. html, json)
     *
     * @param RestBundle\View\View $view
     * @param Request              $request
     *
     * @return string
     */
    protected function getRequestFormat(RestBundle\View\View $view, Request $request)
    {
        return ($view->getFormat() ?: $request->getRequestFormat());
    }

    /**
     * Create the web service response
     *
     * @param array|null|object $content
     * @param string            $errorMessage
     * @param int               $code
     *
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
