<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\EmptyProfileException;

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
            throw new EmptyProfileException('The profile name must not be empty.');
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
