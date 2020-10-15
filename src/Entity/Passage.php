<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\NegativePassageException;
use App\Exception\BoundaryPassageException;

class Passage
{
    private ?int $identifier;
    private int $passageNumber;
    private float $time;
    private const MIN_PASSAGE = 1;
    private const MAX_PASSAGE = 1;
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
            throw new NegativePassageException('The passage parameter must not be a negative number.');
        }
        if ($time < 0) {
            throw new NegativePassageException('The time parameter must not be a negative number.');
        }
        if ($passageNumber < self::MIN_PASSAGE || $passageNumber > self::MAX_PASSAGE) {
            throw new BoundaryPassageException('The passage parameter must not be a number out of boundaries (only accept ' . self::MIN_PASSAGE . ' or ' . self::MAX_PASSAGE . ').');
        }
        if ($time > self::MAX_TIME) {
            throw new BoundaryPassageException('The time parameter must not be a number out of boundary (limit to ' . self::MAX_TIME . ').');
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
