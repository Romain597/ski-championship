<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use App\Gateway\SqlGateway;
use App\Model\ContestModel;
use App\Repository\ContestRepository;

class ContestController extends AbstractController
{
    private Environment $twig;
    private const LIMIT_BY_PAGE = 15;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function viewList(Request $request): Response
    {
        //$page = $request->attributes->get('page', 0);
        //extract($parameters);
        require __DIR__ . '/../Gateway/Database/mysqlMainDatabase.php';
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        $model = new ContestModel($mysqlGateway);
        $repository = new ContestRepository($model);
        //$repository = $this->getRepository(__CLASS__, $mysqlGateway);
        dump($repository);
        /*$dataList = $repository->findAll();*/
        $dataToRender = ['contests' => []];
        /*if (is_null($dataList) === false) {
            $dataToRender['contests'] = $dataList;
        }*/
        dump($dataToRender);
        return new Response(
            $this->twig->render('contest/index.html.twig', $dataToRender),
            Response::HTTP_OK
        );
    }

    public function viewOne(Request $request): Response
    {
        return new Response(
            $this->twig->render('contest/single.html.twig'),
            Response::HTTP_OK
        );
    }

    public function viewAdd(Request $request): Response
    {
        return new Response(
            $this->twig->render('contest/creation.html.twig'),
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
