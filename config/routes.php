<?php

declare(strict_types=1);

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
// List of contest
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
        '/epreuve/modification/{contest}',
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
// Delete a contest
$routes->add(
    'contest_delete',
    new Route(
        '/epreuve/suppression/{contest}',
        [
            '_controller' => 'App\Controller\ContestController::viewDelete',
            'slug' => ''
        ],
        [
            'contest' => '\d+',
            'slug' => '[\w\-]*'
        ]
    )
);

// Competitor
// List of competitor
$routes->add(
    'competitor_list',
    new Route(
        '/epreuve/participants/{contest}',
        [
            '_controller' => 'App\Controller\CompetitorController::viewList',
            'page' => 1
        ],
        [
            'page' => '\d+'
        ]
    )
);
// View a competitor
$routes->add(
    'competitor_view',
    new Route(
        '/epreuve/participant/{contest}/{competitor}',
        [
            '_controller' => 'App\Controller\CompetitorController::viewOne',
            'slug' => ''
        ],
        [
            'contest' => '\d+',
            'slug' => '[\w\-]*'
        ]
    )
);
// Add a competitor
$routes->add(
    'competitor_add',
    new Route(
        '/epreuve/participant/ajout/{contest}/{competitor}',
        [
            '_controller' => 'App\Controller\CompetitorController::viewAdd',
            'page' => 1
        ],
        [
            'page' => '\d+'
        ]
    )
);
// Modify a competitor
$routes->add(
    'competitor_modify',
    new Route(
        '/epreuve/participant/modification/{contest}/{competitor}',
        [
            '_controller' => 'App\Controller\CompetitorController::viewModify',
            'slug' => ''
        ],
        [
            'contest' => '\d+',
            'slug' => '[\w\-]*'
        ]
    )
);
// Delete a competitor
$routes->add(
    'competitor_delete',
    new Route(
        '/epreuve/participant/suppression/{contest}/{competitor}',
        [
            '_controller' => 'App\Controller\CompetitorController::viewDelete',
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
    'export_competitors',
    new Route(
        '/epreuve/export/participants/{contest}',
        [
            '_controller' => 'App\Controller\StopwatchController::exportDataSheetForContest'
        ],
        [
            'contest' => '\d+'
        ]
    )
);

$routes->add(
    'import_times',
    new Route(
        '/epreuve/import/temps-participants/{contest}',
        [
            '_controller' => 'App\Controller\StopwatchController::importTimeFromDataSheet'
        ],
        [
            'contest' => '\d+'
        ]
    )
);

$routes->add(
    'test',
    new Route(
        '/test',
        [
            '_controller' => 'App\Controller\ContestController::test'
        ]
    )
);

return $routes;
