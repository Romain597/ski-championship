<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\EmptyStringException;
use App\Exception\BoundaryDateException;
use App\Exception\AlreadySetException;
use App\Exception\PastDateException;

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
     * @throws EmptyStringException If name and/or location are empty strings
     * @throws BoundaryDateException If end date is equal or inferior to begin date
     * @throws PastDateException If begin date and/or end date are in the past
     * @return void
     */
    public function __construct(string $name, string $location, 
        \DateTimeInterface $beginAt, \DateTimeInterface $endAt, 
        ?int $identifier = null)
    {
        if (trim($name) == '') {
            throw new EmptyStringException('The event name must not be empty.');
        }
        if (trim($location) == '') {
            throw new EmptyStringException('The event location must not be empty.');
        }
        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        if ($beginAt < $currentDate) {
            throw new PastDateException('The event begin date must not be in the past.');
        }
        if ($endAt < $currentDate) {
            throw new PastDateException('The event end date must not be in the past.');
        }
        if ($endAt <= $beginAt) {
            throw new BoundaryDateException('The event end date must not be inferior or 
                equal to the begin date.');
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
     * @throws EmptyStringException If parameter name is a empty string
     * @return void
     */
    public function setName(string $name) : void
    {
        if (trim($name) == '') {
            throw new EmptyStringException('The event name must not be empty.');
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
     * @throws EmptyStringException If parameter location is a empty string
     * @return void
     */
    public function setLocation(string $location) : void
    {
        if (trim($location) == '') {
            throw new EmptyStringException('The event location must not be empty.');
        }
        $this->location = $location;
    }

    /**
     * @return DateTimeInterface
     */
    public function getBeginDate() : \DateTimeInterface
    {
        return $this->beginAt;
    }

    /**
     * @param  DateTimeInterface $beginAt
     * @throws PastDateException If parameter begin date is in the past
     * @return void
     */
    public function setBeginDate(\DateTimeInterface $beginAt) : void
    {
        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        if ($beginAt < $currentDate) {
            throw new PastDateException('The event begin date must not be in the past.');
        }
        $this->beginAt = $beginAt;
    }

    /**
     * @return DateTimeInterface
     */
    public function getEndDate() : \DateTimeInterface
    {
        return $this->endAt;
    }

    /**
     * @param  DateTimeInterface $endAt
     * @throws PastDateException If parameter end date is in the past
     * @throws BoundaryDateException If end date is equal or inferior to begin date
     * @return void
     */
    public function setEndDate(\DateTimeInterface $endAt) : void
    {
        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        if ($endAt < $currentDate) {
            throw new PastDateException('The event end date must not be in the past.');
        }
        if ($endAt <= $this->beginAt) {
            throw new BoundaryDateException('The event end date must not be inferior or 
                equal to the begin date.');
        }
        $this->beginAt = $endAt;
    }
}
