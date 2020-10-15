<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Class Event
 * A event every years   
 */
class Event
{
    private ?int $identifier;
    //private string $uuid;
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
        $this->name = $name;
        $this->location = $location;
        $this->beginAt = $beginAt;
        $this->endAt = $endAt;
        //$this->uuid = $uuid ?? str_replace( '.', '', uniqid('e',true) );
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
