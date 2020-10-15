<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Event;

it('should create a event', function () {

    $eventData = [
        'name' => 'test',
        'location'=> 'Alpes',
        'beginAt' => new \DateTime(),
        'endAt' => new \DateTime()
    ];

    $newEvent = new Event($eventData['name'], $eventData['location'], $eventData['beginAt'], 
        $eventData['endAt']);

    expect($newEvent)->toBeInstanceOf(Event::class);

    /*$resultToArray = $newEvent->toArray();
    if (isset($resultToArray['uuid'])) {
        unset($resultToArray['uuid']);
    }*/

    $this->assertSame($eventData, $newEvent->toArray());

});
