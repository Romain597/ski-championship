<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\CategoryEmptyException;
use App\Exception\AlreadySetException;

class Category
{
    private ?int $identifier;
    private string $name;
    private ?string $description;

    /**
     * Construct and initialize the instance of the object
     *
     * @param  string $name
     * @param  string|null $description Optional description for Category
     * @param  int|null $identifier By default null
     * @throws CategoryEmptyException If name and/or description are empty strings
     * @return void
     */
    public function __construct(string $name, ?string $description = null, 
        ?int $identifier = null)
    {
        if (trim($name) == '') {
            throw new CategoryEmptyException('The category name must not be empty.');
        }
        if (isset($description) === true && trim($description) === '' ) {
            throw new CategoryEmptyException('The category description must not be a empty string.');
        }
        $this->name = $name;
        $this->description = $description;
        $this->identifier = $identifier;
    }

    /**
     * Representation of the category object in a array
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
            throw new AlreadySetException('It is not possible to set a category identifier already set.');
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
     * @throws CategoryEmptyException If parameter name is a empty string
     * @return void
     */
    public function setName(string $name) : void
    {
        if (trim($name) == '') {
            throw new CategoryEmptyException('The category name must not be empty.');
        }
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }
    
    /**
     * @param  string|null $description
     * @throws CategoryEmptyException If parameter description is a empty string
     * @return void
     */
    public function setDescription(?string $description) : void
    {
        if (isset($description) === true && trim($description) === '' ) {
            throw new CategoryEmptyException('The category description must not be a empty string.');
        }
        $this->description = $description;
    }

    /**
     * Save the Category into a database
     *
     * @param  GatewayInterface $gateway
     * @return bool
     */
    public function saveCategory(GatewayInterface $gateway) : bool
    {
        return true;
    }
}
