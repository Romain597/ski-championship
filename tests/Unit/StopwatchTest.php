<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Stopwatch;
use App\Entity\Exception\NegativeNumberException;
use App\Entity\Exception\BoundaryNumberException;
use App\Entity\Exception\AlreadySetException;

function createStopwatchObject(array $stopwatchDataParameter = []): Stopwatch
{
    $stopwatchData = [
        'identifier' => null,
        'turn' => 1,
        'time' => 10.5
    ];
    if (
        !empty($stopwatchDataParameter) === true &&
        count(array_diff_key($stopwatchDataParameter, $stopwatchData)) === 0
    ) {
        $stopwatchData = $stopwatchDataParameter;
    }
    return new Stopwatch(
        $stopwatchData['turn'],
        $stopwatchData['time'],
        $stopwatchData['identifier']
    );
}

it('should create a Stopwatch with right parameters', function () {

    $stopwatchData1 = [
        'identifier' => null,
        'turn' => 1,
        'time' => 10.5
    ];
    $newStopwatch1 = new Stopwatch($stopwatchData1['turn'], $stopwatchData1['time']);
    $this->assertInstanceOf(Stopwatch::class, $newStopwatch1);
    $this->assertSame($stopwatchData1, $newStopwatch1->toArray());

    $stopwatchData2 = [
        'identifier' => 12,
        'turn' => 1,
        'time' => 10.5
    ];
    $newStopwatch2 = new Stopwatch(
        $stopwatchData2['turn'],
        $stopwatchData2['time'],
        $stopwatchData2['identifier']
    );
    $this->assertInstanceOf(Stopwatch::class, $newStopwatch2);
    $this->assertSame($stopwatchData2, $newStopwatch2->toArray());
});

it(
    'should throw a negative Stopwatch exception for instantiate Stopwatchs with bad parameters',
    function () {

        $newStopwatch1 = createStopwatchObject([
            'identifier' => null,
            'turn' => -1,
            'time' => 10.5
        ]);

        $newStopwatch2 = createStopwatchObject([
            'identifier' => null,
            'turn' => 1,
            'time' => -10.5
        ]);
    }
)->throws(NegativeNumberException::class);

it(
    'should throw a boundary Stopwatch exception for instantiate Stopwatchs with bad parameters',
    function () {

        $newStopwatch1 = createStopwatchObject([
            'identifier' => null,
            'turn' => 0,
            'time' => 10.5
        ]);

        $newStopwatch2 = createStopwatchObject([
            'identifier' => null,
            'turn' => 3,
            'time' => 10.5
        ]);

        $newStopwatch3 = createStopwatchObject([
            'identifier' => null,
            'turn' => 2,
            'time' => 100000
        ]);
    }
)->throws(BoundaryNumberException::class);

it(
    'should throw a type error for instantiate a Stopwatch with bad types parameters',
    function () {

        $newStopwatch1 = createStopwatchObject([
            'identifier' => null,
            'turn' => 1,
            'time' => '10.5'
        ]);

        $newStopwatch2 = createStopwatchObject([
            'identifier' => null,
            'turn' => null,
            'time' => 10.5
        ]);

        $newStopwatch3 = createStopwatchObject([
            'identifier' => null,
            'turn' => 2,
            'time' => true
        ]);

        $newStopwatch4 = createStopwatchObject([
            'identifier' => null,
            'turn' => [],
            'time' => 10.5
        ]);

        $newStopwatch5 = createStopwatchObject([
            'identifier' => '',
            'turn' => 1,
            'time' => 100.5
        ]);

        $newStopwatch6 = new Stopwatch(1);

        $newStopwatch7 = new Stopwatch(1, '');
    }
)->throws(\TypeError::class);

it(
    'should return a array of a Stopwatch object properties for the method "toArray()"',
    function () {

        $newStopwatch = createStopwatchObject();
        $stopwatchData = $newStopwatch->toArray();
        $this->assertIsArray($stopwatchData);
        $this->assertArrayHasKey('identifier', $stopwatchData);
        $this->assertArrayHasKey('turn', $stopwatchData);
        $this->assertArrayHasKey('time', $stopwatchData);
    }
);

it(
    'should throw number exceptions when setting a Stopwatch number with a not conform integer',
    function () {

        $newStopwatch = createStopwatchObject([
            'identifier' => 1,
            'turn' => 2,
            'time' => 100.5
        ]);

        $this->expectException(BoundaryNumberException::class);
        $newStopwatch->setTurn(0);

        $this->expectException(NegativeNumberException::class);
        $newStopwatch->setTurn(-1);
    }
);

it(
    'should throw number exceptions when setting a Stopwatch time with a not conform float',
    function () {

        $newStopwatch = createStopwatchObject([
            'identifier' => 1,
            'turn' => 2,
            'time' => 100.5
        ]);

        $this->expectException(BoundaryNumberException::class);
        $newStopwatch->setTime(95000);

        $this->expectException(NegativeNumberException::class);
        $newStopwatch->setTime(-200.1);
    }
);

it('should throw a identifier already set exception', function () {

    $newStopwatch = createStopwatchObject([
        'identifier' => 1,
        'turn' => 2,
        'time' => 100.5
    ]);

    $newStopwatch->setIdentifier(2);
})->throws(AlreadySetException::class);
