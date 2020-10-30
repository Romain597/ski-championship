<?php

declare(strict_types=1);

namespace App\Controller;

use App\File\Writer\CsvFileWriter;
use App\FileHandler\Loader\CsvFileLoader;
use App\FileHandler\Parser\StopwatchFileParser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use App\Gateway\SqlGateway;
use App\Model\StopwatchModel;

class StopwatchController extends AbstractController
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
        // global => SELECT (SUM(s.time)*0.5) AS average_time, c.identifier, c.name, c.first_name, c.race_number, c.birth_date, c.email_address, c.photo FROM stopwatch s inner join competitor c on c.identifier= s.competitor_identifier WHERE s.contest_identifier = 2 GROUP BY s.competitor_identifier ORDER BY average_time ASC;
        // cat => inner join
        // age => inner join plus age=>TIMESTAMPDIFF(YEAR,date de naissance,date debut epreuves) https://openclassrooms.com/fr/courses/1959476-administrez-vos-bases-de-donnees-avec-mysql/1969404-calculs-sur-les-donnees-temporelles
        require __DIR__ . '/../Gateway/Database/mysqlMainDatabase.php';
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        //$repository = $this->getRepository(__CLASS__, $mysqlGateway);
        //$model = $repository->getModel();
        $model = new StopwatchModel($mysqlGateway);
        $sql = '';
        $sql = 'SELECT (SUM(s.time)*0.5) AS average_time, c.name, c.first_name, c.photo FROM stopwatch s inner join competitor c on c.identifier= s.competitor_identifier WHERE s.contest_identifier = 2 GROUP BY s.competitor_identifier ORDER BY average_time ASC;';
        $dataList = $model->request($sql);
        for ($i = 0; $i < count($dataList); $i++) {
            $dataList[$i]['rank'] = $i + 1;
        }
        $dataToRender = ['rankings' => []];
        if (is_null($dataList) === false) {
            $dataToRender['rankings'] = $dataList;
        }
        //dump($dataToRender);
        return new Response(
            $this->twig->render('stopwatch/index.html.twig', $dataToRender),
            Response::HTTP_OK
        );
    }

    /*
    public function viewList(Request $request): Response
    {
        //$page = $request->attributes->get('page', 0);
        require __DIR__ . '/../Gateway/Database/mysqlMainDatabase.php';
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        $repository = $this->getRepository(__CLASS__, $mysqlGateway);
        $dataList = $repository->findAll();
        $dataToRender = ['stopwatchs' => []];
        if (is_null($dataList) === false) {
            $dataToRender['stopwatchs'] = $dataList;
        }
        //dump($dataToRender);
        return new Response(
            $this->twig->render('stopwatch/index.html.twig', $dataToRender),
            Response::HTTP_OK
        );
    }*/

    public function viewOne(Request $request): Response
    {
        return new Response(
            $this->twig->render('stopwatch/single.html.twig'),
            Response::HTTP_OK
        );
    }

    public function viewAdd(Request $request): Response
    {
        return new Response(
            $this->twig->render('stopwatch/creation.html.twig'),
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

    public function export(Request $request): Response
    {
        require __DIR__ . '/../Gateway/Database/mysqlMainDatabase.php';
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        $model = new StopwatchModel($mysqlGateway);
        $sql = 'SELECT c.race_number, c.name, c.first_name, (NULL) AS time_1, (NULL) AS time_2 FROM competitor c WHERE c.contest_identifier = 2 ORDER BY c.race_umber ASC;';
        $dataList = $model->request($sql);
        $parser = new StopwatchFileParser($dataList);
        $fileData = $parser->translateToFile('windows-1252', 'Championnat de ski (2020)');
        $loader = new CsvFileLoader();
        $writer = new CsvFileWriter($loader, __DIR__ . '/../tmp/test_export2.csv');
        $writer->write($fileData, ';');
        return new Response('ok', 200);
    }
}
