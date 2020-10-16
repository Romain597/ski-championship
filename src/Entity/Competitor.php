<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\CompetitorEmptyException;
use App\Exception\CompetitorDateException;
use App\Exception\CompetitorNumberException;
use App\Exception\CompetitorDataFormatException;

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
    private const MAX_AGE_FOR_RACING = 120;
    private const IMAGE_FORMAT_ACCEPTED = ['jpg', 'png'];
        
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
        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $currentDateYear = intval($currentDate->format('Y'));
        $birthDateYear = intval($birthDate->format('Y'));
        $birthDateLimitYear = $currentDateYear - self::MAX_AGE_FOR_RACING;
        if ($birthDateYear <= $birthDateLimitYear) {
            throw new CompetitorDateException('The competitor age must not be 
                equals or superior to ' . self::MAX_AGE_FOR_RACING . ' years.');
        }
        if ($raceNumber <= 0) {
            throw new CompetitorNumberException('The competitor race number must not be 
                a negative number or the number zero.');
        }
        if (trim($name) == '') {
            throw new CompetitorEmptyException('The competitor name must not be empty.');
        }
        if (trim($firstName) == '') {
            throw new CompetitorEmptyException('The competitor firstname must not be empty.');
        }
        if (trim($email) == '') {
            throw new CompetitorEmptyException('The competitor email must not be empty.');
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new CompetitorDataFormatException('The competitor email must be 
                conform to syntax in RFC 822.');
        }
        if (isset($photo) === true && trim($photo) === '') {
            throw new CompetitorEmptyException('The competitor photo must not be 
                a empty string.');
        }
        $imageFormatAcceptedArray = self::IMAGE_FORMAT_ACCEPTED;
        if (isset($photo) === true && count($imageFormatAcceptedArray) > 0) {
            $imageFormatAccepted = '';
            foreach($imageFormatAcceptedArray as $imageFormat) {
                $imageFormatAccepted .= strtoupper($imageFormat) . '|';
            }
            $imageFormatAccepted = preg_replace('/\|$/', '', $imageFormatAccepted);
            if (preg_match('/\.(' . $imageFormatAccepted . ')$/i', $photo) !== 1) {
                if (count($imageFormatAcceptedArray) > 1) {
                    $imageFormatAcceptedForException = preg_replace('/\|/', 
                        ', ', $imageFormatAccepted);
                    $imageFormatAcceptedForException = preg_replace('/\,\s([^\,]+)$/i', 
                        " and $1", $imageFormatAcceptedForException);
                }
                throw new CompetitorDataFormatException('The competitor photo must have 
                    a conform format (only accept 
                    ' . $imageFormatAcceptedForException . ' format)');
            }
        }
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
            'identifier' => $this->identifier,
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
