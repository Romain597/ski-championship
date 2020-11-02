<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use App\Gateway\SqlGateway;

class CompetitorController extends AbstractController
{
    private int $limitByPage = self::LIMIT_BY_PAGE;
    private string $csvCharset = self::CSV_CHARSET;
    private string $csvDelimiter = self::CSV_DELIMITER;
    private const DATE_FORMAT = 'd/m/Y';

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function viewList(Request $request): Response
    {
        $idContest = $request->attributes->get('contest', null);
        if (is_null($idContest) === true) {
            throw new \Exception("Pas d'épreuve sélectioné.");
        }
        extract($this->getGatewayConfiguration(self::MAIN_DATABASE_CONF_FILE));
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        $repository = $this->getRepository(__CLASS__, $mysqlGateway);
        $dataList = $repository->findBy(["contest_identifier = $idContest"], ['race_number ASC']);
        $dataToRender = ['competitors' => [], 'contestIdentifier' => $idContest];
        if (is_null($dataList) === false) {
            //$dataToRender['competitors'] = $dataList;
            foreach ($dataList as $competitor) {
                $competitorArray = $competitor->toArray();
                $competitorArray['birthDate'] = $competitorArray['birthDate']->format(self::DATE_FORMAT);
                $dataToRender['competitors'][] = $competitorArray;
            }
        }
        //dump($dataToRender);
        return new Response(
            $this->twig->render('competitor/index.html.twig', $dataToRender),
            Response::HTTP_OK
        );
    }

    public function viewOne(Request $request): Response
    {
        return new Response(
            $this->twig->render('competitor/single.html.twig'),
            Response::HTTP_OK
        );
    }

    public function viewAdd(Request $request): Response
    {
        return new Response(
            $this->twig->render('competitor/creation.html.twig'),
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
