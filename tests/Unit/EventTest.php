<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Event;
use App\Entity\Exception\EmptyStringException;
use App\Entity\Exception\BoundaryDateException;
use App\Entity\Exception\AlreadySetException;
use App\Entity\Exception\PastDateException;

function createEventObject(array $eventDataParameter = []): Event
{
    $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    $dateObject1 = $dateObject->modify('+1 hour');
    $dateObject2 = $dateObject->modify('+2 hours');
    $eventData = [
        'identifier' => 1,
        'name' => 'test',
        'location' => 'Alpes',
        'beginAt' => $dateObject1,
        'endAt' => $dateObject2
    ];
    if (
        !empty($eventDataParameter) === true &&
        count(array_diff_key($eventDataParameter, $eventData)) === 0
    ) {
        $eventData = $eventDataParameter;
    }
    return new Event(
        $eventData['name'],
        $eventData['location'],
        $eventData['beginAt'],
        $eventData['endAt'],
        $eventData['identifier']
    );
}

it('should create a event with right parameters', function () {

    $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    $dateObject1 = $dateObject->modify('+1 hour');
    $dateObject2 = $dateObject->modify('+2 hours');

    $eventData1 = [
        'identifier' => null,
        'name' => 'test',
        'location' => 'Alpes',
        'beginAt' => $dateObject1,
        'endAt' => $dateObject2
    ];
    $newEvent1 = new Event(
        $eventData1['name'],
        $eventData1['location'],
        $eventData1['beginAt'],
        $eventData1['endAt']
    );
    $this->assertInstanceOf(Event::class, $newEvent1);
    $this->assertSame($eventData1, $newEvent1->toArray());

    $eventData2 = [
        'identifier' => 1,
        'name' => 'test',
        'location' => 'Alpes',
        'beginAt' => $dateObject1,
        'endAt' => $dateObject2
    ];
    $newEvent2 = new Event(
        $eventData2['name'],
        $eventData2['location'],
        $eventData2['beginAt'],
        $eventData2['endAt'],
        $eventData2['identifier']
    );
    $this->assertInstanceOf(Event::class, $newEvent2);
    $this->assertSame($eventData2, $newEvent2->toArray());
});

it(
    'should throw a empty exception for instantiate a event with empty strings parameters',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('+1 hour');
        $dateObject2 = $dateObject->modify('+2 hours');

        $newEvent1 = new Event('', 'alpes', $dateObject1, $dateObject2);

        $newEvent2 = new Event('event2', '', $dateObject1, $dateObject2);
    }
)->throws(EmptyStringException::class);

it(
    'should throw a boundary date exception for instantiate a event with bad dates parameters',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('+2 hours');
        $dateObject2 = $dateObject->modify('+1 hour');

        $newEvent1 = new Event('event1', 'alpes', $dateObject1, $dateObject2);
    }
)->throws(BoundaryDateException::class);

it(
    'should throw a past date exception for instantiate a event with bad dates parameters',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('-2 hours');
        $dateObject2 = $dateObject->modify('-1 hour');

        $newEvent1 = new Event('event1', 'alpes', $dateObject1, $dateObject2);
    }
)->throws(PastDateException::class);

it(
    'should throw a type error for instantiate a event with bad types parameters',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('+1 hour');
        $dateObject2 = $dateObject->modify('+2 hours');
        $defaultObject = new \stdClass();

        $newEvent1 = new Event(1, 'alpes', $dateObject1, $dateObject2);

        $newEvent2 = new Event('event2', null, $dateObject1, $dateObject2);

        $newEvent3 = new Event('event3', 'alpes', $defaultObject1, $dateObject2);

        $newEvent4 = new Event('event4', 'alpes', $dateObject1, $defaultObject2);

        $newEvent5 = new Event('event5', 'alpes', $dateObject1, $dateObject2, '');

        $newEvent6 = new Event('event6');
    }
)->throws(\TypeError::class);

it(
    'should return a array of a event object properties for the method "toArray()"',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('+1 hour');
        $dateObject2 = $dateObject->modify('+2 hours');
        $newEvent = new Event('event1', 'alpes', $dateObject1, $dateObject2);
        $eventData = $newEvent->toArray();
        $this->assertIsArray($eventData);
        $this->assertArrayHasKey('identifier', $eventData);
        $this->assertArrayHasKey('name', $eventData);
        $this->assertArrayHasKey('location', $eventData);
        $this->assertArrayHasKey('beginAt', $eventData);
        $this->assertArrayHasKey('endAt', $eventData);
    }
);

it(
    'should throw a empty exception for setting a property with empty strings parameter',
    function () {

        $newEvent = createEventObject();

        $newEvent->setName('');

        $newEvent->setLocation('');
    }
)->throws(EmptyStringException::class);

it(
    'should throw a past date exception for setting a property with a past date parameter',
    function () {

        $newEvent = createEventObject();

        $dateObject = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $dateObject->modify('-1 hour');

        $newEvent->setBeginDate($dateObject);

        $newEvent->setEndDate($dateObject);
    }
)->throws(PastDateException::class);

it(
    'should throw a boundary date exception for setting a property with a past date parameter',
    function () {

        $dateObject = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $dateObject1 = $dateObject->modify('+1 hour');
        $dateObject2 = $dateObject->modify('+2 hours');
        $newEvent = createEventObject([
            'identifier' => 1,
            'name' => 'test',
            'location' => 'Alpes',
            'beginAt' => $dateObject1,
            'endAt' => $dateObject2
        ]);

        $newEvent->setEndDate($dateObject1);
    }
)->throws(BoundaryDateException::class);

it('should throw a identifier already set exception', function () {

    $newEvent = createEventObject();

    $newEvent->setIdentifier(2);
})->throws(AlreadySetException::class);
