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

// Contest
// List of Contest
// '/epreuves/page/{page}',
$routes->add(
    'contest_list',
    new Route(
        '/epreuves/{page}',
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

// Category
// List of Categories
$routes->add(
    'category_list',
    new Route(
        '/categories/{page}',
        [
            '_controller' => 'App\Controller\CategoryController::viewList',
            'page' => 1
        ],
        [
            'page' => '\d+'
        ]
    )
);

// Profile
// List of Profiles
$routes->add(
    'profile_list',
    new Route(
        '/profils/{page}',
        [
            '_controller' => 'App\Controller\ProfileController::viewList',
            'page' => 1
        ],
        [
            'page' => '\d+'
        ]
    )
);

// Stopwatch
// List of Stopwatchs
$routes->add(
    'stopwatch_list',
    new Route(
        '/classements/{page}',
        [
            '_controller' => 'App\Controller\StopwatchController::viewList',
            'page' => 1
        ],
        [
            'page' => '\d+'
        ]
    )
);

$routes->add(
    'test_export',
    new Route(
        '/epreuve/export',
        [
            '_controller' => 'App\Controller\StopwatchController::export',
        ]
    )
);

return $routes;
