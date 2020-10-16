<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\ProfileEmptyException;

class Profile
{
    private ?int $identifier;
    private string $name;
    private ?string $description;
    
    /**
     * Construct and initialize the instance of the object
     *
     * @param  string $name
     * @param  string|null $description Optional description for Profile
     * @param  int|null $identifier By default null
     * @return void
     */
    public function __construct(string $name, ?string $description = null, 
        ?int $identifier = null)
    {
        if (trim($name) == '' ) {
            throw new ProfileEmptyException('The profile name must not be empty.');
        }
        if (isset($description) === true && trim($description) === '' ) {
            throw new ProfileEmptyException('The profile description must not be a empty string.');
        }
        $this->name = $name;
        $this->description = $description;
        $this->identifier = $identifier;
    }

    /**
     * Representation of the profile object in a array
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'identifier' => $this->identifier,
            'name' => $this->name,
            'description' => $this->description
        ];
    }

    /**
     * Save the Profile into a database
     *
     * @param  GatewayInterface $gateway
     * @return bool
     */
    public function saveProfile(GatewayInterface $gateway) : bool
    {
        return true;
    }
}
