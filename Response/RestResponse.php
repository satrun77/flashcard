<?php

/*
 * This file is part of the Moo\FlashCardBundle package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moo\FlashCardBundle\Response;

/**
 * RestResponse is the response object returned by the REST API.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class RestResponse
{
    public $code = 200;
    public $errorMessage = '';
    public $content = '';
    public $extra;

}
