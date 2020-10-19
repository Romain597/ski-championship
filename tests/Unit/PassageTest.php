<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Passage;
use App\Exception\NegativeNumberException;
use App\Exception\BoundaryNumberException;
use App\Exception\AlreadySetException;

function createPassageObject(array $passageDataParameter = []) : Passage
{
    $passageData = [
        'identifier' => null,
        'passageNumber' => 1,
        'time' => 10.5
    ];
    if (!empty($passageDataParameter) === true && 
        count(array_diff_key($passageDataParameter,$passageData)) === 0) {
        $passageData = $passageDataParameter;
    }
    return new Passage($passageData['passageNumber'], $passageData['time'], 
        $passageData['identifier']);
}

it('should create a passage with right parameters', function () {

    $passageData1 = [
        'identifier' => null,
        'passageNumber' => 1,
        'time' => 10.5
    ];
    $newPassage1 = new Passage($passageData1['passageNumber'], $passageData1['time']);
    $this->assertInstanceOf(Passage::class, $newPassage1);
    $this->assertSame($passageData1, $newPassage1->toArray());

    $passageData2 = [
        'identifier' => 12,
        'passageNumber' => 1,
        'time' => 10.5
    ];
    $newPassage2 = new Passage($passageData2['passageNumber'], $passageData2['time'], 
        $passageData2['identifier']);
    $this->assertInstanceOf(Passage::class, $newPassage2);
    $this->assertSame($passageData2, $newPassage2->toArray());

});

it('should throw a negative passage exception for instantiate passages with bad parameters', 
    function () {
    
    $newPassage1 = createPassageObject([
            'identifier' => null,
            'passageNumber' => -1,
            'time' => 10.5
        ]);

    $newPassage2 = createPassageObject([
            'identifier' => null,
            'passageNumber' => 1,
            'time' => -10.5
        ]);

})->throws(NegativeNumberException::class);

it('should throw a boundary passage exception for instantiate passages with bad parameters', 
    function () {
    
    $newPassage1 = createPassageObject([
            'identifier' => null,
            'passageNumber' => 0,
            'time' => 10.5
        ]);

    $newPassage2 = createPassageObject([
            'identifier' => null,
            'passageNumber' => 3,
            'time' => 10.5
        ]);

    $newPassage3 = createPassageObject([
            'identifier' => null,
            'passageNumber' => 2,
            'time' => 100000
        ]);

})->throws(BoundaryNumberException::class);

it('should throw a type error for instantiate a passage with bad types parameters', 
    function () {
    
    $newPassage1 = createPassageObject([
            'identifier' => null,
            'passageNumber' => 1,
            'time' => '10.5'
        ]);

    $newPassage2 = createPassageObject([
            'identifier' => null,
            'passageNumber' => null,
            'time' => 10.5
        ]);

    $newPassage3 = createPassageObject([
            'identifier' => null,
            'passageNumber' => 2,
            'time' => true
        ]);

    $newPassage4 = createPassageObject([
            'identifier' => null,
            'passageNumber' => [],
            'time' => 10.5
        ]);

    $newPassage5 = createPassageObject([
            'identifier' => '',
            'passageNumber' => 1,
            'time' => 100.5
        ]);

    $newPassage6 = new Passage(1);

    $newPassage7 = new Passage(1,'');

})->throws(\TypeError::class);

it('should return a array of a passage object properties for the method "toArray()"', 
    function () {

    $newPassage = createPassageObject();
    $passageData = $newPassage->toArray();
    $this->assertIsArray($passageData);
    $this->assertArrayHasKey('identifier', $passageData);
    $this->assertArrayHasKey('passageNumber', $passageData);
    $this->assertArrayHasKey('time', $passageData);

});

it('should throw number exceptions when setting a passage number with a not conform integer', 
    function () {

    $newPassage = createPassageObject([
            'identifier' => 1,
            'passageNumber' => 2,
            'time' => 100.5
        ]);

    $this->expectException(BoundaryNumberException::class);
    $newPassage->setPassageNumber(0);

    $this->expectException(NegativeNumberException::class);
    $newPassage->setPassageNumber(-1);

});

it('should throw number exceptions when setting a passage time with a not conform float', 
    function () {

    $newPassage = createPassageObject([
            'identifier' => 1,
            'passageNumber' => 2,
            'time' => 100.5
        ]);

    $this->expectException(BoundaryNumberException::class);
    $newPassage->setTime(95000);

    $this->expectException(NegativeNumberException::class);
    $newPassage->setTime(-200.1);

});

it('should throw a identifier already set exception', function () {

    $newPassage = createPassageObject([
            'identifier' => 1,
            'passageNumber' => 2,
            'time' => 100.5
        ]);

    $newPassage->setIdentifier(2);

})->throws(AlreadySetException::class);
