<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ErrorController
{

    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function notFound(Request $request, \Throwable $throwable): Response
    {
        $dataToRender = ['error' => ['message' => $throwable->getMessage()]];
        return new Response($this->twig->render('http_error/404.html.twig', $dataToRender), Response::HTTP_NOT_FOUND);
    }

    public function notAllowed(Request $request, \Throwable $throwable): Response
    {
        $dataToRender = ['error' => ['message' => $throwable->getMessage()]];
        return new Response($this->twig->render('http_error/405.html.twig', $dataToRender), Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function byDefault(Request $request, \Throwable $throwable): Response
    {
        $dataToRender = ['error' => ['message' => $throwable->getMessage()]];
        return new Response($this->twig->render('http_error/index.html.twig', $dataToRender), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function internServer(Request $request, \Throwable $throwable): Response
    {
        $dataToRender = ['error' => ['message' => $throwable->getMessage()]];
        //dump($dataToRender);
        return new Response($this->twig->render('http_error/500.html.twig', $dataToRender), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
