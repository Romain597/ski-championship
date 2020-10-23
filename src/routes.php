<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection;

// Redirection of index
$routes->add(
    'index', 
    new Route(
        '/',
        [
            '_route' => 'event_list'
        ]
    )
);
// List of event
$routes->add(
    'event_list', 
    new Route(
        '/epreuves/page/{page}',
        [
            '_controller' => 'App\Controller\EventController::viewList',
            'page' => 1
        ],
        [
            'page' => '\d+'
        ]
    )
);
// View a event
$routes->add(
    'event_view', 
    new Route(
        '/epreuve/{event}/{slug}',
        [
            '_controller' => 'App\Controller\EventController::viewOne',
            'slug' => ''
        ],
        [
            'event' => '\d+',
            'slug' => '[\w\-]*'
        ]
    )
);
// Add a event
$routes->add(
    'event_add', 
    new Route(
        '/epreuve/ajout',
        [
            '_controller' => 'App\Controller\EventController::viewAdd',
            'page' => 1
        ],
        [
            'page' => '\d+'
        ]
    )
);
// event list
$routes->add(
    'event_modify', 
    new Route(
        '/epreuve/{event}/modification/{slug}',
        [
            '_controller' => 'App\Controller\EventController::viewModify',
            'slug' => ''
        ],
        [
            'event' => '\d+',
            'slug' => '[\w\-]*'
        ]
    )
);

return $routes;
