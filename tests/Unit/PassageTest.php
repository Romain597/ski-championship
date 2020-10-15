<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Passage;
use App\Exception\NegativePassageException;
use App\Exception\BoundaryPassageException;

it('should create a passage with right parameters', function () {

    $passageData1 = [
        'identifier' => null,
        'passageNumber' => 1,
        'time' => 10.5
    ];
    $newPassage1 = new Passage($passageData1['passageNumber'], $passageData1['time']);
    $this->assertInstanceOf(Passage::class, $newPassage1);
    $this->assertEquals($passageData1, $newPassage1->toArray());

    $passageData2 = [
        'identifier' => 12,
        'passageNumber' => 1,
        'time' => 10.5
    ];
    $newPassage2 = new Passage($passageData2['passageNumber'], $passageData2['time'], $passageData2['identifier']);
    $this->assertInstanceOf(Passage::class, $newPassage2);
    $this->assertEquals($passageData2, $newPassage2->toArray());

});

it('should throw a negative passage exception for instantiate passages with bad parameters', function () {
    
    $newPassage = new Passage(-1, 10.5);

})->throws(NegativePassageException::class);

it('should throw a boundary passage exception for instantiate passages with bad parameters', function () {
    
    $newPassage1 = new Passage(0, 10.5);

    $newPassage2 = new Passage(3, 10.5);

    $newPassage3 = new Passage(2, 100000);

})->throws(BoundaryPassageException::class);

it('should throw a type exception for instantiate passages with bad parameters', function () {
    
    $newPassage1 = new Passage(1, '10.5');

    $newPassage2 = new Passage(null, 10.5);

    $newPassage3 = new Passage(2, true);

    $newPassage4 = new Passage([], 10.5);

    $newPassage4 = new Passage(1, 100.5, '');

})->throws(\TypeError::class);

it('should be a array of properties in return of the "toArray()" method', function () {

    $newPassage = new Passage(1, 10.5);
    $passageData = $newPassage->toArray();
    $this->assertIsArray($passageData);
    $this->assertArrayHasKey('identifier', $passageData);
    $this->assertArrayHasKey('passageNumber', $passageData);
    $this->assertArrayHasKey('time', $passageData);

});
