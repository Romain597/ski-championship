<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\ErrorController;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

// instantiate twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

require_once __DIR__ . '/../src/routes.php';

// Init RequestContext object
$context = new RequestContext();
$context->fromRequest($request = Request::createFromGlobals());

$response = new Response();

// Init UrlMatcher object
$matcher = new UrlMatcher($routes, $context);

try {

    // Find the current route
    $parameters = $matcher->match($context->getPathInfo());
    $request->attributes->add($parameters);

    if (!isset($parameters['_controller']) === true) {
        if (isset($parameters['_route']) === true) {
            $redirect = new RedirectResponse($parameters['_route']);
            $redirect->send();
        }
        $redirect = new RedirectResponse('/epreuves/page');
        $redirect->send();
    }
    
    if (preg_match('/^[\w\\]+\:\:\w+$/', $parameters['_controller']) !== 1) {
        throw new \Exception('Route controller has not a valid symtax.');
    }
    $controller = preg_split('#\:\:#', $parameters['_controller'], -1, PREG_SPLIT_NO_EMPTY);
    $controller[0] = new $controller[0]($twig);
    $response = call_user_func($controller, $request);

} catch (ResourceNotFoundException $e) {

    $errorController = new ErrorController($twig);
    $response = $errorController->notFound($request, $e);

} catch (\Exception $e) {

    $errorController = new ErrorController($twig);
    $response = $errorController->internServer($request, $e);

}

$response->send();
