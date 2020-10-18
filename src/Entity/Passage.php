<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\NegativeNumberException;
use App\Exception\BoundaryNumberException;
use App\Exception\AlreadySetException;

class Passage
{
    private ?int $identifier;
    private int $passageNumber;
    private float $time;
    private const MIN_PASSAGE = 1;
    private const MAX_PASSAGE = 2;
    private const MAX_TIME = 90000;
    
    /**
     * Construct and initialize the instance of the object
     *
     * @param  int $passageNumber
     * @param  float $time
     * @param  int|null $identifier By default null
     * @throws NegativeNumberException If time and/or passage number are negatives
     * @throws BoundaryNumberException If time and/or passage number are out of boundaries
     * @return void
     */
    public function __construct(int $passageNumber, float $time, 
        ?int $identifier = null)
    {
        if ($passageNumber < 0) {
            throw new NegativeNumberException('The passage parameter must not be 
                a negative number.');
        }
        if ($time < 0) {
            throw new NegativeNumberException('The time parameter must not be 
                a negative number.');
        }
        if ($passageNumber < self::MIN_PASSAGE || $passageNumber > self::MAX_PASSAGE) {
            throw new BoundaryNumberException('The passage parameter must not be 
                a number out of boundaries (only accept ' . self::MIN_PASSAGE . ' 
                or ' . self::MAX_PASSAGE . ').');
        }
        if ($time > self::MAX_TIME) {
            throw new BoundaryNumberException('The time parameter must not be 
                a number out of boundary (limit to ' . self::MAX_TIME . ').');
        }
        $this->passageNumber = $passageNumber;
        $this->time = $time;
        $this->identifier = $identifier;
    }

    /**
     * Representation of the passage object in a array
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'identifier' => $this->identifier,
            'passageNumber' => $this->passageNumber,
            'time' => $this->time
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
            throw new AlreadySetException('It is not possible to set a passage identifier already set.');
        }
        $this->identifier = $identifier;
    }
    
    /**
     * @return int
     */
    public function getPassageNumber() : int
    {
        return $this->passageNumber;
    }
    
    /**
     * @param  int $passageNumber
     * @throws NegativeNumberException If parameter passage number is negative
     * @throws BoundaryNumberException If parameter passage number is out of boundaries
     * @return void
     */
    public function setPassageNumber(int $passageNumber) : void
    {
        if ($passageNumber < 0) {
            throw new NegativeNumberException('The passage parameter must not be 
                a negative number.');
        }
        if ($passageNumber < self::MIN_PASSAGE || $passageNumber > self::MAX_PASSAGE) {
            throw new BoundaryNumberException('The passage parameter must not be 
                a number out of boundaries (only accept ' . self::MIN_PASSAGE . ' 
                or ' . self::MAX_PASSAGE . ').');
        }
        $this->passageNumber = $passageNumber;
    }
    
    /**
     * @return float
     */
    public function getTime() : float
    {
        return $this->time;
    }
    
    /**
     * @param  float $time
     * @throws NegativeNumberException If parameter time number is negative
     * @throws BoundaryNumberException If parameter time number is out of boundaries
     * @return void
     */
    public function setTime(float $time) : void
    {
        if ($time < 0) {
            throw new NegativeNumberException('The time parameter must not be 
                a negative number.');
        }
        if ($time > self::MAX_TIME) {
            throw new BoundaryNumberException('The time parameter must not be 
                a number out of boundary (limit to ' . self::MAX_TIME . ').');
        }
        $this->time = $time;
    }
}
