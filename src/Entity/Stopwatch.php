<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Exception\NegativeNumberException;
use App\Entity\Exception\BoundaryNumberException;
use App\Entity\Exception\AlreadySetException;

class Stopwatch
{
    private ?int $identifier;
    private int $turn;
    private float $time;
    private int $competitorIdentifier;
    private int $contestIdentifier;
    private const MIN_STOPWATCH = 1;
    private const MAX_STOPWATCH = 2;
    private const MAX_TIME = 90000;

    /**
     * Construct and initialize the instance of the object
     *
     * @param  int $turn
     * @param  float $time
     * @param  int|null $identifier By default null
     * @param  int $competitorIdentifier
     * @param  int $contestIdentifier
     * @throws NegativeNumberException If time and/or Stopwatch number are negatives
     * @throws BoundaryNumberException If time and/or Stopwatch number are out of boundaries
     * @return void
     */
    public function __construct(
        int $turn,
        float $time,
        int $competitorIdentifier,
        int $contestIdentifier,
        ?int $identifier = null
    ) {
        if ($turn < 0) {
            throw new NegativeNumberException('The Stopwatch parameter must not be 
                a negative number.');
        }
        if ($time < 0) {
            throw new NegativeNumberException('The time parameter must not be 
                a negative number.');
        }
        if ($turn < self::MIN_STOPWATCH || $turn > self::MAX_STOPWATCH) {
            throw new BoundaryNumberException('The Stopwatch parameter must not be 
                a number out of boundaries (only accept ' . self::MIN_STOPWATCH . ' 
                or ' . self::MAX_STOPWATCH . ').');
        }
        if ($time > self::MAX_TIME) {
            throw new BoundaryNumberException('The time parameter must not be 
                a number out of boundary (limit to ' . self::MAX_TIME . ').');
        }
        $this->turn = $turn;
        $this->time = $time;
        $this->competitorIdentifier = $competitorIdentifier;
        $this->contestIdentifier = $contestIdentifier;
        $this->identifier = $identifier;
    }

    public static function fromState(array $state): Stopwatch
    {
        $identifier = (empty($state['identifier']) === true
            && is_numeric($state['identifier']) === false)
            ? null : (int) $state['identifier'];
        return new self(
            (int) $state['turn'],
            (float) $state['time'],
            (int) $state['competitorIdentifier'],
            (int) $state['contestIdentifier'],
            $identifier
        );
    }

    /**
     * Representation of the Stopwatch object in a array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'turn' => $this->turn,
            'time' => $this->time,
            'competitorIdentifier' => $this->competitorIdentifier,
            'contestIdentifier' => $this->contestIdentifier
        ];
    }
    
    /**
     * @return int
     */
    public function getCompetitorIdentifier(): int
    {
        return $this->competitorIdentifier;
    }
    
    /**
     * @return int
     */
    public function getContestIdentifier(): int
    {
        return $this->contestIdentifier;
    }

    /**
     * @return bool
     */
    public function isSaved(): bool
    {
        return isset($this->identifier) ? true : false;
    }

    /**
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
    }

    /**
     * @param  int $identifier
     * @throws AlreadySetException If the identifier is already set
     * @return void
     */
    public function setIdentifier(int $identifier): void
    {
        if ($this->isSaved() === true) {
            throw new AlreadySetException('It is not possible to set a Stopwatch identifier already set.');
        }
        $this->identifier = $identifier;
    }

    /**
     * @return int
     */
    public function getTurn(): int
    {
        return $this->turn;
    }

    /**
     * @param  int $turn
     * @throws NegativeNumberException If parameter stopwatch number is negative
     * @throws BoundaryNumberException If parameter stopwatch number is out of boundaries
     * @return void
     */
    public function setTurn(int $turn): void
    {
        if ($turn < 0) {
            throw new NegativeNumberException('The Stopwatch parameter must not be 
                a negative number.');
        }
        if ($turn < self::MIN_STOPWATCH || $turn > self::MAX_STOPWATCH) {
            throw new BoundaryNumberException('The Stopwatch parameter must not be 
                a number out of boundaries (only accept ' . self::MIN_STOPWATCH . ' 
                or ' . self::MAX_STOPWATCH . ').');
        }
        $this->turn = $turn;
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @param  float $time
     * @throws NegativeNumberException If parameter time number is negative
     * @throws BoundaryNumberException If parameter time number is out of boundaries
     * @return void
     */
    public function setTime(float $time): void
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
