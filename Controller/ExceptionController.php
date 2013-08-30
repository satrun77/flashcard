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

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseExceptionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;

/**
 * ExceptionController is the exception controller to provides specific error page for the bundle pages.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class ExceptionController extends BaseExceptionController
{

    /**
     * Override to provide a custom 404 error page for the current bundle
     *
     * @param Request $request
     * @param string  $format
     * @param integer $code    An HTTP response status code
     * @param Boolean $debug
     *
     * @return \Symfony\Bundle\FrameworkBundle\Templating\TemplateReference
     */
    protected function findTemplate(Request $request, $format, $code, $debug)
    {
        // bundle specific error page for production env.
        if (!$debug) {
            $template = new TemplateReference('MooFlashCardBundle', 'Exception', 'error', $format, 'twig');
            if ($this->templateExists($template)) {
                return $template;
            }
        }

        return parent::findTemplate($request, $format, $code, $debug);
    }

}
