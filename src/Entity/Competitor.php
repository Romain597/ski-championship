<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Class Competitor
 * A competitor participe to one event
 */
class Competitor
{
    private ?int $identifier;
    private string $name;
    private string $firstName;
    private int $raceNumber;
    private \DateTimeInterface $birthDate;
    private ?string $photo;
        
    /**
     * Construct and initialize the instance of the object
     *
     * @param  string $name
     * @param  string $firstName
     * @param  int $raceNumber
     * @param  DateTimeInterface $birthDate
     * @param  string $email
     * @param  string|null $photo Optional photo for Competitor
     * @param  int|null $identifier By default null
     * @return void
     */
    public function __construct(string $name, string $firstName, int $raceNumber, 
        \DateTimeInterface $birthDate, string $email, ?string $photo = null, 
        ?int $identifier = null)
    {
        $this->name = $name;
        $this->firstName = $firstName;
        $this->raceNumber = $raceNumber;
        $this->birthDate = $birthDate;
        $this->email = $email;
        $this->photo = $photo;
        $this->identifier = $identifier;
    }

    /**
     * Representation of the competitor object in a array
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'name' => $this->name,
            'firstName' => $this->firstName,
            'raceNumber' => $this->raceNumber,
            'birthDate' => $this->birthDate,
            'email' => $this->email,
            'photo' => $this->photo
        ];
    }

    /**
     * Save the Competitor into a database
     *
     * @param  GatewayInterface $gateway
     * @return bool
     */
    public function saveCompetitor(GatewayInterface $gateway) : bool
    {
        return true;
    }
}
