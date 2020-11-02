<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use App\Gateway\SqlGateway;
use App\Repository\ContestRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ContestController extends AbstractController
{
    private int $limitByPage = self::LIMIT_BY_PAGE;
    private string $csvCharset = self::CSV_CHARSET;
    private string $csvDelimiter = self::CSV_DELIMITER;
    private const DATE_FORMAT = 'd/m/Y H:i';

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function viewList(Request $request): Response
    {
        //$page = $request->attributes->get('page', 0);
        //extract($parameters);
        /*require __DIR__ . '/../../config/database/mysqlMainDatabase.php';
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }*/
        extract($this->getGatewayConfiguration(self::MAIN_DATABASE_CONF_FILE));
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        $repository = $this->getRepository(__CLASS__, $mysqlGateway);
        $dataList = $repository->findAll();
        $dataToRender = ['contests' => []];
        if (is_null($dataList) === false) {
            //$dataToRender['contests'] = $dataList;
            foreach ($dataList as $contest) {
                $contestArray = $contest->toArray();
                $contestArray['beginAt'] = $contestArray['beginAt']->format(self::DATE_FORMAT);
                $contestArray['endAt'] = $contestArray['endAt']->format(self::DATE_FORMAT);
                $dataToRender['contests'][] = $contestArray;
            }
        }
        //dump($dataToRender);
        return new Response(
            $this->twig->render('contest/index.html.twig', $dataToRender),
            Response::HTTP_OK
        );
    }

    public function viewOne(Request $request): Response
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
        $contest = $repository->findById((int) $idContest);
        $dataToRender = ['contest' => []];
        if (is_null($contest) === false) {
            //$dataToRender['contest'] = $contest;
            $contestArray = $contest->toArray();
            $contestArray['beginAt'] = $contestArray['beginAt']->format(self::DATE_FORMAT);
            $contestArray['endAt'] = $contestArray['endAt']->format(self::DATE_FORMAT);
            $dataToRender['contest'] = $contestArray;
        }
        return new Response(
            $this->twig->render('contest/single.html.twig', $dataToRender),
            Response::HTTP_OK
        );
    }

    public function viewAdd(Request $request): Response
    {
        //dump($request);
        $postParameters = $request->request->all();
        if (!empty($postParameters)) {
            extract($this->getGatewayConfiguration(self::MAIN_DATABASE_CONF_FILE));
            if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
                throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
            }
            $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
            $repository = $this->getRepository(__CLASS__, $mysqlGateway);
            $redirect = $this->addContest($repository, $postParameters);
            return $redirect;
        }
        return new Response(
            $this->twig->render('contest/creation.html.twig'),
            Response::HTTP_OK
        );
    }

    private function addContest(ContestRepository $repository, array $parameters): RedirectResponse
    {
        $name = !empty(trim($parameters['name'])) ? trim($parameters['name']) : null;
        $location = !empty(trim($parameters['location'])) ? trim($parameters['location']) : null;
        $beginDate = !empty(trim($parameters['begin'])) ? \DateTime::createFromFormat(self::DATE_FORMAT, trim($parameters['begin']), new \DateTimeZone(Contest::TIME_ZONE)) : null;
        $endDate = !empty(trim($parameters['end'])) ? \DateTime::createFromFormat(self::DATE_FORMAT, trim($parameters['end']), new \DateTimeZone(Contest::TIME_ZONE)) : null;
        $newContest = new Contest($name, $location, $beginDate, $endDate);
        $id = $repository->add($newContest);
        $url = '/epreuves';
        if (isset($parameters['next'])) {
            $url = "/epreuve/participants/$id?validation=ok";
        }
        return new RedirectResponse($url);
    }

    public function viewModify(Request $request): Response
    {
        //dump($request);
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
        $postParameters = $request->request->all();
        if (!empty($postParameters)) {
            $redirect = $this->updateContest($repository, (int) $idContest, $postParameters);
            return $redirect;
        }
        $contest = $repository->findById((int) $idContest);
        $dataToRender = ['contest' => []];
        if (is_null($contest) === false) {
            //$dataToRender['contest'] = $contest;
            $contestArray = $contest->toArray();
            $contestArray['beginAt'] = $contestArray['beginAt']->format(self::DATE_FORMAT);
            $contestArray['endAt'] = $contestArray['endAt']->format(self::DATE_FORMAT);
            $dataToRender['contest'] = $contestArray;
        }
        return new Response(
            $this->twig->render('contest/update.html.twig', $dataToRender),
            Response::HTTP_OK
        );
    }

    private function updateContest(ContestRepository $repository, int $contestId, array $parameters): RedirectResponse
    {
        $name = !empty(trim($parameters['name'])) ? trim($parameters['name']) : null;
        $location = !empty(trim($parameters['location'])) ? trim($parameters['location']) : null;
        $beginDate = !empty(trim($parameters['begin'])) ? \DateTime::createFromFormat(self::DATE_FORMAT, trim($parameters['begin']), new \DateTimeZone(Contest::TIME_ZONE)) : null;
        $endDate = !empty(trim($parameters['end'])) ? \DateTime::createFromFormat(self::DATE_FORMAT, trim($parameters['end']), new \DateTimeZone(Contest::TIME_ZONE)) : null;
        $newContest = new Contest($name, $location, $beginDate, $endDate, $contestId);
        $repository->modify($newContest);
        $url = '/epreuves';
        if (isset($parameters['next'])) {
            $url = "/epreuve/participants/$contestId?modification=ok";
        }
        return new RedirectResponse($url);
    }

    public function deleteContest(Request $request): Response
    {
        $idContest = $request->attributes->get('contest', null);
        if (is_null($idContest) === true) {
            throw new \Exception("Pas d'épreuve à supprimer.");
        }
        extract($this->getGatewayConfiguration(self::MAIN_DATABASE_CONF_FILE));
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        $repository = $this->getRepository(__CLASS__, $mysqlGateway);
        $repository->remove($idContest);
        return new RedirectResponse('/epreuves');
    }
}
