<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\PassageNumberException;
use App\Exception\PassageBoundaryException;

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
     * @return void
     */
    public function __construct(int $passageNumber, float $time, 
        ?int $identifier = null)
    {
        if ($passageNumber < 0) {
            throw new PassageNumberException('The passage parameter must not be 
                a negative number.');
        }
        if ($time < 0) {
            throw new PassageNumberException('The time parameter must not be 
                a negative number.');
        }
        if ($passageNumber < self::MIN_PASSAGE || $passageNumber > self::MAX_PASSAGE) {
            throw new PassageBoundaryException('The passage parameter must not be 
                a number out of boundaries (only accept ' . self::MIN_PASSAGE . ' 
                or ' . self::MAX_PASSAGE . ').');
        }
        if ($time > self::MAX_TIME) {
            throw new PassageBoundaryException('The time parameter must not be 
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

    public function getIdentifier() : int
    {
        return $this->identifier;
    }
    
    public function setIdentifier(int $identifier) : void
    {
        $this->identifier = $identifier;
    }

    public function getPassageNumber() : int
    {
        return $this->passageNumber;
    }

    public function setPassageNumber(int $passageNumber) : void
    {
        if ($passageNumber < 0) {
            throw new PassageNumberException('The passage parameter must not be 
                a negative number.');
        }
        if ($passageNumber < self::MIN_PASSAGE || $passageNumber > self::MAX_PASSAGE) {
            throw new PassageBoundaryException('The passage parameter must not be 
                a number out of boundaries (only accept ' . self::MIN_PASSAGE . ' 
                or ' . self::MAX_PASSAGE . ').');
        }
        $this->passageNumber = $passageNumber;
    }

    public function getTime() : float
    {
        return $this->time;
    }

    public function setTime(float $time) : void
    {
        if ($time < 0) {
            throw new PassageNumberException('The time parameter must not be 
                a negative number.');
        }
        if ($time > self::MAX_TIME) {
            throw new PassageBoundaryException('The time parameter must not be 
                a number out of boundary (limit to ' . self::MAX_TIME . ').');
        }
        $this->time = $time;
    }

    /**
     * Save the Passage into a database
     *
     * @param  GatewayInterface $gateway
     * @return bool
     */
    public function savePassage(GatewayInterface $gateway) : bool
    {
        return true;
    }
}
