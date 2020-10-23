<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class EventController
{
//https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/src/Fixer/ClassNotation/OrderedClassElementsFixer.php
    private Environment $_twig;

    public function __construct(Environment $twig)
    {
        $this->_twig = $twig;
    }

    public function viewList(Request $request): Response
    {
        //$page = $request->attributes->get('page', 0);
        //extract($parameters);
        //return new Response('event_list page ' . $page);
        return new Response(
            $this->_twig->render('event/index.html.twig'),
            Response::HTTP_OK
        );
    }

    public function viewOne(Request $request): Response
    {
        return new Response(
            $this->_twig->render('event/single.html.twig'), 
            Response::HTTP_OK
        );
    }

    public function viewAdd(Request $request): Response
    {
        return new Response(
            $this->_twig->render('event/creation.html.twig'), 
            Response::HTTP_OK
        );
    }

    public function viewDelete(Request $request): Response
    {
        return new Response('test', 200);
    }

    public function viewModify(Request $request): Response
    {
        return new Response('test', 200);
    }
}
