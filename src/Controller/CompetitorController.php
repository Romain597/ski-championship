<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class CompetitorController
{
    private Environment $_twig;

    public function __construct(Environment $twig)
    {
        $this->_twig = $twig;
    }

    public function viewList(Request $request): Response
    {
        return new Response(
            $this->_twig->render('competitor/index.html.twig'),
            Response::HTTP_OK
        );
    }

    public function viewOne(Request $request): Response
    {
        return new Response(
            $this->_twig->render('competitor/single.html.twig'), 
            Response::HTTP_OK
        );
    }

    public function viewAdd(Request $request): Response
    {
        return new Response(
            $this->_twig->render('competitor/creation.html.twig'), 
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
