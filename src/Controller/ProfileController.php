<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ProfileController
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function viewList(Request $request): Response
    {
        return new Response(
            $this->twig->render('profile/index.html.twig'),
            Response::HTTP_OK
        );
    }

    public function viewOne(Request $request): Response
    {
        return new Response(
            $this->twig->render('profile/single.html.twig'),
            Response::HTTP_OK
        );
    }

    public function viewAdd(Request $request): Response
    {
        return new Response(
            $this->twig->render('profile/creation.html.twig'),
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
