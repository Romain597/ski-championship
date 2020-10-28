<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

// Redirection of index
$routes->add(
    'index',
    new Route(
        '/'
    )
);
// List of contest
$routes->add(
    'contest_list',
    new Route(
        '/epreuves/page/{page}',
        [
            '_controller' => 'App\Controller\ContestController::viewList',
            'page' => 1
        ],
        [
            'page' => '\d+'
        ]
    )
);
// View a contest
$routes->add(
    'contest_view',
    new Route(
        '/epreuve/{contest}/{slug}',
        [
            '_controller' => 'App\Controller\ContestController::viewOne',
            'slug' => ''
        ],
        [
            'contest' => '\d+',
            'slug' => '[\w\-]*'
        ]
    )
);
// Add a contest
$routes->add(
    'contest_add',
    new Route(
        '/epreuve/ajout',
        [
            '_controller' => 'App\Controller\ContestController::viewAdd',
            'page' => 1
        ],
        [
            'page' => '\d+'
        ]
    )
);
// Modify a contest
$routes->add(
    'contest_modify',
    new Route(
        '/epreuve/{contest}/modification/{slug}',
        [
            '_controller' => 'App\Controller\ContestController::viewModify',
            'slug' => ''
        ],
        [
            'contest' => '\d+',
            'slug' => '[\w\-]*'
        ]
    )
);

return $routes;
