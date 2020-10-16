<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\EventEmptyException;
use App\Exception\EventDateException;

/**
 * Class Event
 * A event every years   
 */
class Event
{
    private ?int $identifier;
    private string $name;
    private string $location;
    private \DateTimeInterface $beginAt;
    private \DateTimeInterface $endAt;
    
    /**
     * Construct and initialize the instance of the object
     *
     * @param  string $name
     * @param  string $location
     * @param  DateTimeInterface $beginAt
     * @param  DateTimeInterface $endAt
     * @param  int|null $identifier By default null
     * @return void
     */
    public function __construct(string $name, string $location, 
        \DateTimeInterface $beginAt, \DateTimeInterface $endAt, 
        ?int $identifier = null)
    {
        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        if (trim($name) == '') {
            throw new EventEmptyException('The event name must not be empty.');
        }
        if (trim($location) == '') {
            throw new EventEmptyException('The event location must not be empty.');
        }
        if ($beginAt < $currentDate) {
            throw new EventDateException('The event begin date must not be in the past.');
        }
        if ($endAt < $currentDate) {
            throw new EventDateException('The event end date must not be in the past.');
        }
        if ($endAt <= $beginAt) {
            throw new EventDateException('The event end date must not be inferior or 
                equals to the begin date.');
        }
        $this->name = $name;
        $this->location = $location;
        $this->beginAt = $beginAt;
        $this->endAt = $endAt;
        $this->identifier = $identifier;
    }
    
    /**
     * Representation of the event object in a array
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'identifier' => $this->identifier,
            'name' => $this->name,
            'location'=> $this->location,
            'beginAt' => $this->beginAt,
            'endAt' => $this->endAt
        ];
    }

    /**
     * Save the Event into a database
     *
     * @param  GatewayInterface $gateway
     * @return bool
     */
    public function saveEvent(GatewayInterface $gateway) : bool
    {
        return true;
    }
}
