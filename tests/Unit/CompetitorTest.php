<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Competitor;
use App\Exception\EmptyStringException;
use App\Exception\BoundaryDateException;
use App\Exception\NegativeNumberException;
use App\Exception\EmailAddressSyntaxException;
use App\Exception\AlreadySetException;
use App\Exception\BoundaryNumberException;
use App\Exception\ImageExtensionException;

it('should create a competitor with right parameters', function () {

    $competitorData1 = [
        'identifier' => null,
        'name' => 'test',
        'firstName' => 'prénom',
        'raceNumber' => 50,
        'birthDate' => new \DateTime(),
        'email' => 'truc@test.fr',
        'photo' => 'dossier/photo_name.jpg'
    ];
    $newCompetitor1 = new Competitor($competitorData1['name'], $competitorData1['firstName'], 
        $competitorData1['raceNumber'], $competitorData1['birthDate'], 
        $competitorData1['email'], $competitorData1['photo']);
    $this->assertInstanceOf(Competitor::class, $newCompetitor1);
    $this->assertSame($competitorData1, $newCompetitor1->toArray());

    $competitorData2 = [
        'identifier' => 1,
        'name' => 'test',
        'firstName' => 'prénom',
        'raceNumber' => 50,
        'birthDate' => new \DateTime(),
        'email' => 'truc@test.fr',
        'photo' => 'dossier/photo_name.jpg'
    ];
    $newCompetitor2 = new Competitor($competitorData2['name'], $competitorData2['firstName'], 
        $competitorData2['raceNumber'], $competitorData2['birthDate'], 
        $competitorData2['email'], $competitorData2['photo'], $competitorData2['identifier']);
    $this->assertInstanceOf(Competitor::class, $newCompetitor2);
    $this->assertSame($competitorData2, $newCompetitor2->toArray());

    $competitorData3 = [
        'identifier' => null,
        'name' => 'test',
        'firstName' => 'prénom',
        'raceNumber' => 50,
        'birthDate' => new \DateTime(),
        'email' => 'truc@test.fr',
        'photo' => null
    ];
    $newCompetitor3 = new Competitor($competitorData3['name'], $competitorData3['firstName'], 
        $competitorData3['raceNumber'], $competitorData3['birthDate'], 
        $competitorData3['email']);
    $this->assertInstanceOf(Competitor::class, $newCompetitor3);
    $this->assertSame($competitorData3, $newCompetitor3->toArray());

});

it('should throw a empty exception for instantiate a competitor with empty strings parameters', 
    function () {
    
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    
    $newCompetitor1 = new Competitor('', 'firstName', 1, $dateObject, 
        'email@test.fr');

    $newCompetitor2 = new Competitor('name', '', 1, $dateObject, 
        'email@test.fr');
    
    $newCompetitor3 = new Competitor('name', 'firstName', 1, $dateObject, 
        '', 'dossier/photo.jpg');

    $newCompetitor4 = new Competitor('name', 'firstName', 1, $dateObject, 
        'email@test.fr', '');

})->throws(EmptyStringException::class);

it('should throw a date exception for instantiate a competitor with bad date parameter', 
    function () {
    
    $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    $dateObject1 = $dateObject->modify('-150 years');
    
    $newCompetitor1 = new Competitor('name', 'firstName', 1, $dateObject1, 
        'email@test.fr');

})->throws(BoundaryDateException::class);

it('should throw a number exception for instantiate a competitor with bad number parameter', 
    function () {
    
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    
    $newCompetitor1 = new Competitor('name', 'firstName', 0, $dateObject, 
        'email@test.fr');

    $newCompetitor1 = new Competitor('name', 'firstName', -1, $dateObject, 
        'email@test.fr');

})->throws(NegativeNumberException::class);

it('should throw a data format exception for instantiate a competitor with bad format parameters', 
    function () {
    
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    
    $newCompetitor1 = new Competitor('name', 'firstName', 1, $dateObject, 
        'email');

    $newCompetitor2 = new Competitor('name', 'firstName', 1, $dateObject, 
        'email@test.fr', 'dossier/photo.pdf');

    $newCompetitor3 = new Competitor('name', 'firstName', 1, $dateObject, 
        'email@test.fr', 'dossier/photo.truc');

})->throws(EmailAddressSyntaxException::class);

it('should throw a type error for instantiate a event with bad types parameters', 
    function () {
    
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    $defaultObject = new \stdClass();
    
    $newCompetitor1 = new Competitor(5, 'firstName', 1, $dateObject, 
        'email@test.fr');

    $newCompetitor2 = new Competitor('name', null, 4, $dateObject, 
        'email@test.fr');

    $newCompetitor3 = new Competitor('name', 'firstName', '', $dateObject, 
        'email@test.fr', 'dossier/photo.jpg');

    $newCompetitor4 = new Competitor('name', 'firstName', 3, $defaultObject, 
        'email@test.fr', 'dossier/photo.jpg');

    $newCompetitor5 = new Competitor('name', 'firstName', 3, $dateObject, 
        true, 'dossier/photo.jpg');

    $newCompetitor6 = new Competitor('name', 'firstName', 2, $dateObject, 
        'email@test.fr', [], 1);

    $newCompetitor7 = new Competitor('name', 'firstName', 2, $dateObject, 
        'email@test.fr', 'dossier/photo.jpg', '');

    $newCompetitor8 = new Competitor('name', 'firstName', 2, $dateObject);

})->throws(\TypeError::class);

it('should return a array of a competitor object properties for the method "toArray()"', 
    function () {

    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    $newCompetitor = new Competitor('name', 'firstName', 1, $dateObject, 
        'email@test.fr', 'dossier/photo.jpg');
    $competitorData = $newCompetitor->toArray();
    $this->assertIsArray($competitorData);
    $this->assertArrayHasKey('identifier', $competitorData);
    $this->assertArrayHasKey('name', $competitorData);
    $this->assertArrayHasKey('firstName', $competitorData);
    $this->assertArrayHasKey('raceNumber', $competitorData);
    $this->assertArrayHasKey('birthDate', $competitorData);
    $this->assertArrayHasKey('email', $competitorData);
    $this->assertArrayHasKey('photo', $competitorData);

});
