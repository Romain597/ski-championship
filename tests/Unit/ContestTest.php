<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Contest;
use App\Entity\Exception\EmptyStringException;
use App\Entity\Exception\BoundaryDateException;
use App\Entity\Exception\AlreadySetException;
use App\Entity\Exception\PastDateException;

function createContestObject(array $contestDataParameter = []): Contest
{
    $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    $dateObject1 = $dateObject->modify('+1 hour');
    $dateObject2 = $dateObject->modify('+2 hours');
    $contestData = [
        'identifier' => 1,
        'name' => 'test',
        'location' => 'Alpes',
        'beginAt' => $dateObject1,
        'endAt' => $dateObject2
    ];
    if (
        !empty($contestDataParameter) === true &&
        count(array_diff_key($contestDataParameter, $contestData)) === 0
    ) {
        $contestData = $contestDataParameter;
    }
    return new Contest(
        $contestData['name'],
        $contestData['location'],
        $contestData['beginAt'],
        $contestData['endAt'],
        $contestData['identifier']
    );
}

it('should create a Contest with right parameters', function () {

    $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    $dateObject1 = $dateObject->modify('+1 hour');
    $dateObject2 = $dateObject->modify('+2 hours');

    $contestData1 = [
        'identifier' => null,
        'name' => 'test',
        'location' => 'Alpes',
        'beginAt' => $dateObject1,
        'endAt' => $dateObject2
    ];
    $newContest1 = new Contest(
        $contestData1['name'],
        $contestData1['location'],
        $contestData1['beginAt'],
        $contestData1['endAt']
    );
    $this->assertInstanceOf(Contest::class, $newContest1);
    $this->assertSame($contestData1, $newContest1->toArray());

    $contestData2 = [
        'identifier' => 1,
        'name' => 'test',
        'location' => 'Alpes',
        'beginAt' => $dateObject1,
        'endAt' => $dateObject2
    ];
    $newContest2 = new Contest(
        $contestData2['name'],
        $contestData2['location'],
        $contestData2['beginAt'],
        $contestData2['endAt'],
        $contestData2['identifier']
    );
    $this->assertInstanceOf(Contest::class, $newContest2);
    $this->assertSame($contestData2, $newContest2->toArray());
});

it(
    'should throw a empty exception for instantiate a Contest with empty strings parameters',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('+1 hour');
        $dateObject2 = $dateObject->modify('+2 hours');

        $newContest1 = new Contest('', 'alpes', $dateObject1, $dateObject2);

        $newContest2 = new Contest('contest2', '', $dateObject1, $dateObject2);
    }
)->throws(EmptyStringException::class);

it(
    'should throw a boundary date exception for instantiate a Contest with bad dates parameters',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('+2 hours');
        $dateObject2 = $dateObject->modify('+1 hour');

        $newContest1 = new Contest('contest1', 'alpes', $dateObject1, $dateObject2);
    }
)->throws(BoundaryDateException::class);

it(
    'should throw a past date exception for instantiate a Contest with bad dates parameters',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('-2 hours');
        $dateObject2 = $dateObject->modify('-1 hour');

        $newContest1 = new Contest('contest1', 'alpes', $dateObject1, $dateObject2);
    }
)->throws(PastDateException::class);

it(
    'should throw a type error for instantiate a Contest with bad types parameters',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('+1 hour');
        $dateObject2 = $dateObject->modify('+2 hours');
        $defaultObject = new \stdClass();

        $newContest1 = new Contest(1, 'alpes', $dateObject1, $dateObject2);

        $newContest2 = new Contest('contest2', null, $dateObject1, $dateObject2);

        $newContest3 = new Contest('contest3', 'alpes', $defaultObject1, $dateObject2);

        $newContest4 = new Contest('contest4', 'alpes', $dateObject1, $defaultObject2);

        $newContest5 = new Contest('contest5', 'alpes', $dateObject1, $dateObject2, '');

        $newContest6 = new Contest('contest6');
    }
)->throws(\TypeError::class);

it(
    'should return a array of a Contest object properties for the method "toArray()"',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('+1 hour');
        $dateObject2 = $dateObject->modify('+2 hours');
        $newContest = new Contest('contest1', 'alpes', $dateObject1, $dateObject2);
        $contestData = $newContest->toArray();
        $this->assertIsArray($contestData);
        $this->assertArrayHasKey('identifier', $contestData);
        $this->assertArrayHasKey('name', $contestData);
        $this->assertArrayHasKey('location', $contestData);
        $this->assertArrayHasKey('beginAt', $contestData);
        $this->assertArrayHasKey('endAt', $contestData);
    }
);

it(
    'should throw a empty exception for setting a property with empty strings parameter',
    function () {

        $newContest = createContestObject();

        $newContest->setName('');

        $newContest->setLocation('');
    }
)->throws(EmptyStringException::class);

it(
    'should throw a past date exception for setting a property with a past date parameter',
    function () {

        $newContest = createContestObject();

        $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $dateObject->modify('-1 hour');

        $newContest->setBeginDate($dateObject);

        $newContest->setEndDate($dateObject);
    }
)->throws(PastDateException::class);

it(
    'should throw a boundary date exception for setting a property with a past date parameter',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('+1 hour');
        $dateObject2 = $dateObject->modify('+2 hours');
        $newContest = createContestObject([
            'identifier' => 1,
            'name' => 'test',
            'location' => 'Alpes',
            'beginAt' => $dateObject1,
            'endAt' => $dateObject2
        ]);

        $newContest->setEndDate($dateObject1);
    }
)->throws(BoundaryDateException::class);

it('should throw a identifier already set exception', function () {

    $newContest = createContestObject();

    $newContest->setIdentifier(2);
})->throws(AlreadySetException::class);
