<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\EventEmptyException;
use App\Exception\EventDateException;
use App\Exception\AlreadySetException;

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
     * @throws EventEmptyException If name and/or location are empty strings
     * @throws EventDateException If beginAt and/or endAt are not valid dates
     * @return void
     */
    public function __construct(string $name, string $location, 
        \DateTimeInterface $beginAt, \DateTimeInterface $endAt, 
        ?int $identifier = null)
    {
        if (trim($name) == '') {
            throw new EventEmptyException('The event name must not be empty.');
        }
        if (trim($location) == '') {
            throw new EventEmptyException('The event location must not be empty.');
        }
        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
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
     * @return bool
     */
    public function isSaved() : bool
    {
        return isset($this->identifier) ? true : false;
    }

    /**
     * @return int
     */
    public function getIdentifier() : int
    {
        return $this->identifier;
    }
        
    /**
     * @param  int $identifier
     * @throws AlreadySetException If the identifier is already set
     * @return void
     */
    public function setIdentifier(int $identifier) : void
    {
        if ($this->isSaved() === true) {
            throw new AlreadySetException('It is not possible to set a event identifier already set.');
        }
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * @param  string $name
     * @throws EventEmptyException If parameter name is a empty string
     * @return void
     */
    public function setName(string $name) : void
    {
        if (trim($name) == '') {
            throw new EventEmptyException('The event name must not be empty.');
        }
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLocation() : string
    {
        return $this->location;
    }
    
    /**
     * @param  string $location
     * @throws EventEmptyException If parameter location is a empty string
     * @return void
     */
    public function setLocation(string $location) : void
    {
        if (trim($location) == '') {
            throw new EventEmptyException('The event location must not be empty.');
        }
        $this->location = $location;
    }

    /**
     * @return DateTimeInterface
     */
    public function getBeginAt() : \DateTimeInterface
    {
        return $this->beginAt;
    }

    /**
     * @param  DateTimeInterface $beginAt
     * @throws EventDateException If parameter beginAt is not a valid date
     * @return void
     */
    public function setBeginAt(\DateTimeInterface $beginAt) : void
    {
        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        if ($beginAt < $currentDate) {
            throw new EventDateException('The event begin date must not be in the past.');
        }
        $this->beginAt = $beginAt;
    }

    /**
     * @return DateTimeInterface
     */
    public function getEndAt() : \DateTimeInterface
    {
        return $this->endAt;
    }

    /**
     * @param  DateTimeInterface $endAt
     * @throws EventDateException If parameter endAt is not a valid date
     * @return void
     */
    public function setEndAt(\DateTimeInterface $endAt) : void
    {
        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        if ($endAt < $currentDate) {
            throw new EventDateException('The event end date must not be in the past.');
        }
        if ($endAt <= $this->beginAt) {
            throw new EventDateException('The event end date must not be inferior or 
                equals to the begin date.');
        }
        $this->beginAt = $endAt;
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
