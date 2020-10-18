<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\CompetitorEmptyException;
use App\Exception\CompetitorDateException;
use App\Exception\CompetitorNumberException;
use App\Exception\CompetitorDataFormatException;
use App\Exception\AlreadySetException;

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
    private const IMAGE_EXTENSION_ACCEPTED = ['jpg', 'png'];
        
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
     * @throws CompetitorEmptyException If name and/or firstname and/or email and/or photo 
     *      are empty strings
     * @throws CompetitorDateException If birth date is not a valid date
     * @throws CompetitorDataFormatException If email and/or photo format are not valid
     * @throws CompetitorNumberException If race number is negative or equals to zero
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
        $imageExtensionAcceptedArray = self::IMAGE_EXTENSION_ACCEPTED;
        if (isset($photo) === true && count($imageExtensionAcceptedArray) > 0) {
            $imageExtensionAccepted = '';
            foreach($imageExtensionAcceptedArray as $imageExtension) {
                $imageExtensionAccepted .= strtoupper($imageExtension) . '|';
            }
            $imageExtensionAccepted = preg_replace('/\|$/', '', $imageExtensionAccepted);
            if (preg_match('/\.(' . $imageExtensionAccepted . ')$/i', $photo) !== 1) {
                if (count($imageExtensionAcceptedArray) > 1) {
                    $imageExtensionAcceptedForException = preg_replace('/\|/', 
                        ', ', $imageExtensionAccepted);
                    $imageExtensionAcceptedForException = preg_replace('/\,\s([^\,]+)$/i', 
                        " and $1", $imageExtensionAcceptedForException);
                }
                throw new CompetitorDataFormatException('The competitor photo must have 
                    a conform file extension (only accept 
                    ' . $imageExtensionAcceptedForException . ')');
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
            throw new AlreadySetException('It is not possible to set a competitor identifier already set.');
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
     * @throws CompetitorEmptyException If parameter name is a empty string
     * @return void
     */
    public function setName(string $name) : void
    {
        if (trim($name) == '') {
            throw new CompetitorEmptyException('The competitor name must not be empty.');
        }
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getFirstName() : string
    {
        return $this->firstName;
    }
    
    /**
     * @param  string $firstname
     * @throws CompetitorEmptyException If parameter first name is a empty string
     * @return void
     */
    public function setFirstName(string $firstName) : void
    {
        if (trim($firstName) == '') {
            throw new CompetitorEmptyException('The competitor firstname must not be empty.');
        }
        $this->firstName = $firstName;
    }

    /**
     * @return DateTimeInterface
     */
    public function getBirthDate() : \DateTimeInterface
    {
        return $this->birthDate;
    }

    /**
     * @param  DateTimeInterface $birthDate
     * @throws CompetitorDateException If parameter birthDate is not a valid date
     * @return void
     */
    public function setBirthDate(\DateTimeInterface $birthDate) : void
    {
        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $currentDateYear = intval($currentDate->format('Y'));
        $birthDateYear = intval($birthDate->format('Y'));
        $birthDateLimitYear = $currentDateYear - self::MAX_AGE_FOR_RACING;
        if ($birthDateYear <= $birthDateLimitYear) {
            throw new CompetitorDateException('The competitor age must not be 
                equals or superior to ' . self::MAX_AGE_FOR_RACING . ' years.');
        }
        $this->birthDate = $birthDate;
    }

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }
    
    /**
     * @param  string $email
     * @throws CompetitorEmptyException If parameter email is a empty string
     * @throws CompetitorDataFormatException If parameter email symtax is not valid
     * @return void
     */
    public function setEmail(string $email) : void
    {
        if (trim($email) == '') {
            throw new CompetitorEmptyException('The competitor email must not be empty.');
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new CompetitorDataFormatException('The competitor email must be 
                conform to syntax in RFC 822.');
        }
        $this->email = $email;
    }
    
    /**
     * @return int
     */
    public function getRaceNumber() : int
    {
        return $this->raceNumber;
    }
    
    /**
     * @param  int $raceNumber
     * @throws CompetitorNumberException If parameter race number is negative or equals to zero
     * @return void
     */
    public function setRaceNumber(int $raceNumber) : void
    {
        if ($raceNumber <= 0) {
            throw new CompetitorNumberException('The competitor race number must not be 
                a negative number or the number zero.');
        }
        $this->raceNumber = $raceNumber;
    }

    /**
     * @return string|null
     */
    public function getPhoto() : ?string
    {
        return $this->photo;
    }
    
    /**
     * @param  string|null $photo
     * @throws CompetitorEmptyException If parameter photo is a empty string
     * @throws CompetitorDataFormatException If parameter photo extension is not valid
     * @return void
     */
    public function setPhoto(?string $photo) : void
    {
        if (isset($photo) === true && trim($photo) === '') {
            throw new CompetitorEmptyException('The competitor photo must not be 
                a empty string.');
        }
        $imageExtensionAcceptedArray = self::IMAGE_EXTENSION_ACCEPTED;
        if (isset($photo) === true && count($imageExtensionAcceptedArray) > 0) {
            $imageExtensionAccepted = '';
            foreach($imageExtensionAcceptedArray as $imageExtension) {
                $imageExtensionAccepted .= strtoupper($imageExtension) . '|';
            }
            $imageExtensionAccepted = preg_replace('/\|$/', '', $imageExtensionAccepted);
            if (preg_match('/\.(' . $imageExtensionAccepted . ')$/i', $photo) !== 1) {
                if (count($imageExtensionAcceptedArray) > 1) {
                    $imageExtensionAcceptedForException = preg_replace('/\|/', 
                        ', ', $imageExtensionAccepted);
                    $imageExtensionAcceptedForException = preg_replace('/\,\s([^\,]+)$/i', 
                        " and $1", $imageExtensionAcceptedForException);
                }
                throw new CompetitorDataFormatException('The competitor photo must have 
                    a conform file extension (only accept 
                    ' . $imageExtensionAcceptedForException . ')');
            }
        }
        $this->photo = $photo;
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
