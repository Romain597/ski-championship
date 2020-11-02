<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Competitor;
use App\Entity\Stopwatch;
use App\FileHandler\Parser\ImportFileParserInterface;
use App\FileHandler\Parser\RankingFileParser;
use App\FileHandler\Parser\StopwatchFileParser;
use App\Gateway\GatewayInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use App\Gateway\SqlGateway;
use App\Model\AbstractModel;
use App\Model\CompetitorModel;
use App\Model\StopwatchModel;
use App\Repository\CompetitorRepository;

class StopwatchController extends AbstractController
{
    private int $limitByPage = self::LIMIT_BY_PAGE;
    private string $csvCharset = self::CSV_CHARSET;
    private string $csvDelimiter = self::CSV_DELIMITER;
    private const RANGE_FOR_AGE = 10;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function viewList(Request $request): Response
    {
        //$page = $request->attributes->get('page', 0);
        // age => inner join plus age=>TIMESTAMPDIFF(YEAR,date de naissance,date debut epreuves) https://openclassrooms.com/fr/courses/1959476-administrez-vos-bases-de-donnees-avec-mysql/1969404-calculs-sur-les-donnees-temporelles
        $contestIdentifier = $request->attributes->get('contest', null);
        $exportTypeValue = $request->attributes->get('exportType', null);
        if (is_null($contestIdentifier) === true) {
            throw new \Exception("L'identifiant de l'épreuve n'a pas été récupéré.");
        }
        extract($this->getGatewayConfiguration(self::MAIN_DATABASE_CONF_FILE));
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        //$repository = $this->getRepository(__CLASS__, $mysqlGateway);
        //$model = $repository->getModel();
        $model = new StopwatchModel($mysqlGateway);
        $exportType = $this->getRankingExportType($exportTypeValue);
        $dataList = $this->getRankingData($exportType, $model, $contestIdentifier);
        $dataToRender = ['rankings' => []];
        if (is_null($dataList) === false) {
            $dataToRender['rankings'] = $dataList;
        }
        $dataToRender['title'] = $this->getRankingViewTitle($exportType);
        //dump($dataToRender);
        return new Response(
            $this->twig->render('stopwatch/index.html.twig', $dataToRender),
            Response::HTTP_OK
        );
    }

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

    public function exportDataSheetForRanking(Request $request): Response
    {
        /*require __DIR__ . '/../../config/database/mysqlMainDatabase.php';
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }*/
        $contestIdentifier = $request->attributes->get('contest', null);
        $exportTypeValue = $request->attributes->get('exportType', null);
        if (is_null($contestIdentifier) === true) {
            throw new \Exception("L'identifiant de l'épreuve n'a pas été récupéré.");
        }
        extract($this->getGatewayConfiguration(self::MAIN_DATABASE_CONF_FILE));
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        $model = new StopwatchModel($mysqlGateway);
        $exportType = $this->getRankingExportType($exportTypeValue);
        $dataList = $this->getRankingData($exportType, $model, $contestIdentifier);
        $contestName = $this->getContestName($model, $contestIdentifier);
        extract($this->getRankingInfoFile($exportType, $contestName));
        $dateId = str_replace('.', '', uniqid('', true));
        $fileName = 'export_' . $contestFileName . $fileName . $dateId;
        $fileTitle = trim($fileTitle) . ' - ' . $contestName;
        $this->exportDataInDataSheet($dataList, trim($fileName), trim($fileTitle));
        return new Response('ok', 200);
    }

    private function getContestName(StopwatchModel $model, int $contestIdentifier): string
    {
        $result = $model->request('SELECT name, DATE_FORMAT(begin_date, "%Y-%m-%d") AS date FROM contest WHERE identifier = ' . $contestIdentifier);
        if (empty($result) === true || count($result) !== 1) {
            return '';
        }
        $title = $result[0]['name'];
        $date = $result[0]['date'];
        $year = substr($date, 0, 4);
        if (strstr($title, $year) === false) {
            $title .= ' (' . $date . ')';
        }
        return $title;
    }

    private function getRankingInfoFile(int $exportType, string $contestName): array
    {
        $contestFileName = preg_replace('/[\s\(\)]+/', '_', trim($contestName));
        $contestFileName = rtrim($contestFileName, '_') . '_';
        switch ($exportType) {
            case RankingFileParser::EXPORT_TYPE_CATEGORY:
                return ['contestFileName' => $contestFileName, 'fileName' => 'classement_categorie_','fileTitle' => "Classement par catégories"];
            break;
            case RankingFileParser::EXPORT_TYPE_AGE_RANGE:
                return ['contestFileName' => $contestFileName, 'fileName' => 'classement_age_','fileTitle' => "Classement par tranches d'âge"];
            break;
            case RankingFileParser::EXPORT_TYPE_GLOBAL:
                return ['contestFileName' => $contestFileName, 'fileName' => 'classement_generale_','fileTitle' => "Classement générale"];
            break;
            default:
                return ['contestFileName' => $contestFileName, 'fileName' => '','fileTitle' => ''];
        }
    }

    private function getRankingViewTitle(int $exportType): string
    {
        switch ($exportType) {
            case RankingFileParser::EXPORT_TYPE_CATEGORY:
                return "Classement par catégories";
            break;
            case RankingFileParser::EXPORT_TYPE_AGE_RANGE:
                return "Classement par tranches d'âge";
            break;
            case RankingFileParser::EXPORT_TYPE_GLOBAL:
                return "Classement générale";
            break;
            default:
                return '';
        }
    }

    private function getRankingExportType(string $exportTypeValue): int
    {
        switch ($exportTypeValue) {
            case 'categorie':
                return RankingFileParser::EXPORT_TYPE_CATEGORY;
            break;
            case 'age':
                return RankingFileParser::EXPORT_TYPE_AGE_RANGE;
            break;
            default:
                return RankingFileParser::EXPORT_TYPE_GLOBAL;
        }
    }

    private function getRankingDataByAgeRange(AbstractModel $model, string $sql, array $values): array
    {
        $result = [];
        foreach ($values as $value) {
            $request = str_replace(':range', $value['range'], str_replace(':minrange', $value['minRange'], str_replace(':maxrange', $value['maxRange'], $sql)));
            $dataList = $model->request($request);
            for ($i = 0; $i < count($dataList); $i++) {
                $dataList[$i]['rank'] = $i + 1;
            }
            $result[] = $dataList;
        }
        return $result;
    }

    private function getRankingDataByCategory(AbstractModel $model, string $sql, array $values): array
    {
        $result = [];
        foreach ($values as $value) {
            $request = str_replace(':cat', $value, $sql);
            $dataList = $model->request($request);
            for ($i = 0; $i < count($dataList); $i++) {
                $dataList[$i]['rank'] = $i + 1;
            }
            $result[] = $dataList;
        }
        return $result;
    }

    private function getRankingAgeRange(): array
    {
        $minAge = Competitor::MIN_AGE_FOR_RACING;
        $minAgeStep = intval(floor($minAge * 0.1) * 10);
        $maxAge = Competitor::MAX_AGE_FOR_RACING;
        $maxAgeStep = intval(ceil($maxAge * 0.1) * 10);
        $range = [];
        //$range = [[':range', ':minrange', ':maxrange']];
        //$range[] = ['De ' . $j . ' à ' . ($j + self::RANGE_FOR_AGE) . ' ans', $j, $j + self::RANGE_FOR_AGE];
        for ($j = $minAgeStep; $j < $maxAgeStep; $j + self::RANGE_FOR_AGE) {
            $minValue = $j <= $minAge ? $minAge : $j;
            $maxValue = ($j + self::RANGE_FOR_AGE) >= $maxAge ? $maxAge : ($j + self::RANGE_FOR_AGE - 1);
            $range[] = ['range' => 'De ' . $minValue . ' à ' . $maxValue . ' ans', 'minRange' => $minValue, 'maxRange' => $maxValue];
        }
        return $range;
    }

    private function getRankingData(int $exportType, AbstractModel $model, int $contestIdentifier): array
    {
        switch ($exportType) {
            case RankingFileParser::EXPORT_TYPE_CATEGORY:
                $categories = $model->request('SELECT identifier FROM category ORDER BY name ASC;', [], \PDO::FETCH_COLUMN);
                $sql = 'SELECT c.name AS category, (SUM(s.time)*0.5) AS average_time, p.race_number, p.name, p.first_name, p.photo FROM stopwatch s INNER JOIN competitor p ON p.identifier = s.competitor_identifier INNER JOIN category c ON c.identifier = p.category_identifier WHERE s.contest_identifier = ' . $contestIdentifier . ' AND c.identifier = :cat GROUP BY s.competitor_identifier ORDER BY average_time ASC;';
                return $this->getRankingDataByCategory($model, $sql, $categories);
                break;
            case RankingFileParser::EXPORT_TYPE_AGE_RANGE:
                $range = $this->getRankingAgeRange();
                $sql = 'SELECT (":range") AS age_range, (SUM(s.time)*0.5) AS average_time, p.race_number, p.name, p.first_name, p.photo FROM stopwatch s INNER JOIN competitor p ON p.identifier = s.competitor_identifier INNER JOIN category c ON c.identifier = p.category_identifier INNER JOIN contest e ON e.identifier = p.contest_identifier WHERE e.identifier = ' . $contestIdentifier . ' AND TIMESTAMPDIFF(YEAR, p.birth_date, e.begin_date) BETWEEN :minrange AND :maxrange GROUP BY s.competitor_identifier ORDER BY average_time ASC;';
                return $this->getRankingDataByAgeRange($model, $sql, $range);
                break;
            case RankingFileParser::EXPORT_TYPE_GLOBAL:
                $sql = 'SELECT (SUM(s.time)*0.5) AS average_time, p.race_number, p.name, p.first_name, p.photo FROM stopwatch s INNER JOIN competitor p ON p.identifier = s.competitor_identifier WHERE s.contest_identifier = ' . $contestIdentifier . ' GROUP BY s.competitor_identifier ORDER BY average_time ASC;';
                $dataList = $model->request($sql);
                for ($i = 0; $i < count($dataList); $i++) {
                    $dataList[$i]['rank'] = $i + 1;
                }
                return $dataList;
                break;
            default:
                return [];
        }
    }

    public function exportDataSheetForContest(Request $request): Response
    { // exportTimeDataSheetForContest // exportEmptyTimeByCompetitor
        /*require __DIR__ . '/../../config/database/mysqlMainDatabase.php';
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }*/
        $contestIdentifier = $request->attributes->get('contest', null);
        if (is_null($contestIdentifier) === true) {
            throw new \Exception("L'identifiant de l'épreuve n'a pas été récupéré.");
        }
        extract($this->getGatewayConfiguration(self::MAIN_DATABASE_CONF_FILE));
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        $model = new StopwatchModel($mysqlGateway);
        $sql = 'SELECT c.race_number, c.name, c.first_name, (NULL) AS time_1, (NULL) AS time_2 FROM competitor c WHERE c.contest_identifier = ' . $contestIdentifier . ' ORDER BY c.race_umber ASC;';
        $dataList = $model->request($sql);
        $fileName = 'test_export2';
        $fileTitle = 'Championnat de ski (2020)';
        $this->exportDataInDataSheet($dataList, $fileName, $fileTitle);
        return new Response('ok', 200);
    }

    private function exportDataInDataSheet(array $dataList, string $fileName, string $fileTitle = ''): void
    { //exportCompetitorTimeInDataSheet
        $parser = new StopwatchFileParser($dataList);
        $this->exportDataToCsvFile($parser, $this->csvCharset, $fileName, $this->csvDelimiter, $fileTitle);
    }

    private function importDataFromCsvFile(GatewayInterface $gateway, int $contestIdentifier, string $fileName): array
    { // importTimeDataFromCsvFile
        $dataToRetrieve = $this->readDataFromCsvFile($fileName, $this->csvDelimiter);
        $parser = new StopwatchFileParser($dataToRetrieve);
        $competitorModel = new CompetitorModel($gateway);
        $competitorRepository = new CompetitorRepository($competitorModel);
        return $parser->retrieveObjects($contestIdentifier, $competitorRepository);
    }

    public function importTimeFromDataSheet(Request $request): Response
    {
        $contestIdentifier = $request->attributes->get('contest', null);
        $fileName = $request->attributes->get('file', '');
        if (is_null($contestIdentifier) === true) {
            throw new \Exception("L'identifiant de l'épreuve n'a pas été récupéré.");
        }
        extract($this->getGatewayConfiguration(self::MAIN_DATABASE_CONF_FILE));
        if (!isset($dsn) === true || !isset($user) === true || !isset($password) === true) {
            throw new \Exception("Les données de connexion à la base de données ne sont pas tous initialisés.");
        }
        $mysqlGateway = new SqlGateway($dsn, $user, $password, $request);
        $stopwatchList = $this->importDataFromCsvFile($mysqlGateway, $contestIdentifier, $fileName);
        $repository = $this->getRepository(__CLASS__, $mysqlGateway);
        foreach ($stopwatchList as $stopwatch) {
            if ($stopwatch instanceof Stopwatch) {
                $repository->add($stopwatch);
            }
        }
        return new Response('ok', 200);
    }
}
