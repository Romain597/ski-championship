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

function createCompetitorObject(array $eventDataParameter = []) : Competitor
{
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    $dateObject->modify('-19 years');
    $competitorData = [
        'identifier' => 1,
        'name' => 'test',
        'firstName' => 'prénom',
        'raceNumber' => 50,
        'birthDate' => $dateObject,
        'emailAddress' => 'truc@test.fr',
        'photo' => 'dossier/photo_name.jpg'
    ];
    if (!empty($competitorDataParameter) === true && 
        count(array_diff_key($competitorDataParameter,$competitorData)) === 0) {
        $eventData = $competitorDataParameter;
    }
    return new Competitor($competitorData['name'], $competitorData['firstName'], 
        $competitorData['raceNumber'], $competitorData['birthDate'], 
        $competitorData['emailAddress'], $competitorData['photo'], $competitorData['identifier']);
}

it('should create a competitor with right parameters', function () {

    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    $dateObject->modify('-19 years');

    $competitorData1 = [
        'identifier' => null,
        'name' => 'test',
        'firstName' => 'prénom',
        'raceNumber' => 50,
        'birthDate' => $dateObject,
        'emailAddress' => 'truc@test.fr',
        'photo' => 'dossier/photo_name.jpg'
    ];
    $newCompetitor1 = new Competitor($competitorData1['name'], $competitorData1['firstName'], 
        $competitorData1['raceNumber'], $competitorData1['birthDate'], 
        $competitorData1['emailAddress'], $competitorData1['photo']);
    $this->assertInstanceOf(Competitor::class, $newCompetitor1);
    $this->assertSame($competitorData1, $newCompetitor1->toArray());

    $competitorData2 = [
        'identifier' => 1,
        'name' => 'test',
        'firstName' => 'prénom',
        'raceNumber' => 50,
        'birthDate' => $dateObject,
        'emailAddress' => 'truc@test.fr',
        'photo' => 'dossier/photo_name.jpg'
    ];
    $newCompetitor2 = new Competitor($competitorData2['name'], $competitorData2['firstName'], 
        $competitorData2['raceNumber'], $competitorData2['birthDate'], 
        $competitorData2['emailAddress'], $competitorData2['photo'], $competitorData2['identifier']);
    $this->assertInstanceOf(Competitor::class, $newCompetitor2);
    $this->assertSame($competitorData2, $newCompetitor2->toArray());

    $competitorData3 = [
        'identifier' => null,
        'name' => 'test',
        'firstName' => 'prénom',
        'raceNumber' => 50,
        'birthDate' => $dateObject,
        'emailAddress' => 'truc@test.fr',
        'photo' => null
    ];
    $newCompetitor3 = new Competitor($competitorData3['name'], $competitorData3['firstName'], 
        $competitorData3['raceNumber'], $competitorData3['birthDate'], 
        $competitorData3['emailAddress']);
    $this->assertInstanceOf(Competitor::class, $newCompetitor3);
    $this->assertSame($competitorData3, $newCompetitor3->toArray());

});

it('should throw a empty exception for instantiate a competitor with empty strings parameters', 
    function () {
    
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    $dateObject->modify('-19 years');
    
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

it('should throw a negative number exception for instantiate a competitor with negative number parameter', 
    function () {
    
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    $dateObject->modify('-19 years');

    $newCompetitor1 = new Competitor('name', 'firstName', -1, $dateObject, 
        'email@test.fr');

})->throws(NegativeNumberException::class);

it('should throw a boundary number exception for instantiate a competitor with a bad number parameter', 
    function () {
    
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    $dateObject->modify('-19 years');
    
    $newCompetitor1 = new Competitor('name', 'firstName', 0, $dateObject, 
        'email@test.fr');

})->throws(BoundaryNumberException::class);

it('should throw a email address symtax exception for instantiate a competitor with bad email parameter', 
    function () {
    
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    $dateObject->modify('-19 years');
    
    $newCompetitor1 = new Competitor('name', 'firstName', 1, $dateObject, 
        'email');

})->throws(EmailAddressSyntaxException::class);

it('should throw a image extension exception for instantiate a competitor with bad image extension parameter', 
    function () {
    
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    $dateObject->modify('-19 years');

    $newCompetitor1 = new Competitor('name', 'firstName', 1, $dateObject, 
        'email@test.fr', 'dossier/photo.pdf');

    $newCompetitor2 = new Competitor('name', 'firstName', 1, $dateObject, 
        'email@test.fr', 'dossier/photo.truc');

})->throws(ImageExtensionException::class);

it('should throw a type error for instantiate a event with bad types parameters', 
    function () {
    
    $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    $dateObject->modify('-19 years');
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
    $dateObject->modify('-19 years');
    $newCompetitor = new Competitor('name', 'firstName', 1, $dateObject, 
        'email@test.fr', 'dossier/photo.jpg');
    $competitorData = $newCompetitor->toArray();
    $this->assertIsArray($competitorData);
    $this->assertArrayHasKey('identifier', $competitorData);
    $this->assertArrayHasKey('name', $competitorData);
    $this->assertArrayHasKey('firstName', $competitorData);
    $this->assertArrayHasKey('raceNumber', $competitorData);
    $this->assertArrayHasKey('birthDate', $competitorData);
    $this->assertArrayHasKey('emailAddress', $competitorData);
    $this->assertArrayHasKey('photo', $competitorData);

});

it('should throw a empty exception for setting a property with empty strings parameter', 
    function () {
    
    $newCompetitor = createCompetitorObject();

    $newCompetitor->setName('');
    $newCompetitor->setFirstName('');
    $newCompetitor->setEmailAddress('');
    $newCompetitor->setPhoto('');

})->throws(EmptyStringException::class);

it('should throw a image extension exception for setting a property with bad image extension parameter', 
    function () {
    
    $newCompetitor = createCompetitorObject();

    $newCompetitor->setPhoto('image.pdf');

    $newCompetitor->setPhoto('image.truc');

})->throws(ImageExtensionException::class);

it('should throw a email address symtax exception for setting a property with bad email parameter', 
    function () {
    
    $newCompetitor = createCompetitorObject();

    $newCompetitor->setEmailAddress('@test.fr');

    $newCompetitor->setEmailAddress('email a@test.fr');

})->throws(EmailAddressSyntaxException::class);

it('should throws number exceptions for setting a property with bad number parameter', 
    function () {
    
    $newCompetitor = createCompetitorObject();

    $this->expectException(NegativeNumberException::class);
    $newCompetitor->setRaceNumber(-1);

    $this->expectException(BoundaryNumberException::class);
    $newCompetitor->setRaceNumber(0);

});

it('should throws boundary date exceptions for setting a property with bad date parameter', 
    function () {
    
    $newCompetitor = createCompetitorObject();

    $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    $dateObject2 = $dateObject->modify('-150 hours');

    $newCompetitor->setBirthDate($dateObject);

    $newCompetitor->setBirthDate($dateObject2);

})->throws(BoundaryDateException::class);

it('should throw a identifier already set exception', function () {

    $newCompetitor = createCompetitorObject();

    $newCompetitor->setIdentifier(2);

})->throws(AlreadySetException::class);
