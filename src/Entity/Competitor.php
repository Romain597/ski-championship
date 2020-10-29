<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Exception\EmptyStringException;
use App\Entity\Exception\BoundaryDateException;
use App\Entity\Exception\NegativeNumberException;
use App\Entity\Exception\EmailAddressSyntaxException;
use App\Entity\Exception\AlreadySetException;
use App\Entity\Exception\BoundaryNumberException;
use App\Entity\Exception\ImageExtensionException;

/**
 * Class Competitor
 * A competitor participe to one competitor
 */
class Competitor
{
    private ?int $identifier;
    private string $name;
    private string $firstName;
    private string $emailAddress;
    private int $raceNumber;
    private \DateTimeInterface $birthDate;
    private ?string $photo;
    private int $contestIdentifier;
    private int $categoryIdentifier;
    private ?int $profileIdentifier;
    private const MAX_AGE_FOR_RACING = 120;
    private const MIN_AGE_FOR_RACING = 18;
    private const IMAGE_EXTENSION_ACCEPTED = ['jpg', 'png'];
    private const TIME_ZONE = 'Europe/Paris';

    /**
     * Construct and initialize the instance of the object
     *
     * @param  string $name
     * @param  string $firstName
     * @param  int $raceNumber
     * @param  DateTimeInterface $birthDate
     * @param  string $emailAddress
     * @param  int $contestIdentifier
     * @param  int $categoryIdentifier
     * @param  int|null $profileIdentifier
     * @param  string|null $photo Optional photo for Competitor
     * @param  int|null $identifier By default null
     * @throws EmptyStringException If name and/or firstname and/or email and/or photo 
     *      are empty strings
     * @throws BoundaryDateException If birth date is over or equal to max age for racing
     * @throws EmailAddressSyntaxException If email address syntax is not valid
     * @throws ImageExtensionException If photo file extension is not accepted
     * @throws NegativeNumberException If race number is negative
     * @throws BoundaryNumberException If race number is equal to zero
     * @return void
     */
    public function __construct(
        string $name,
        string $firstName,
        int $raceNumber,
        \DateTimeInterface $birthDate,
        string $emailAddress,
        int $contestIdentifier,
        int $categoryIdentifier,
        ?int $profileIdentifier,
        ?string $photo = null,
        ?int $identifier = null
    ) {
        $currentDate = new \DateTime('now', new \DateTimeZone(self::TIME_ZONE));
        $currentDateYear = intval($currentDate->format('Y'));
        $birthDateYear = intval($birthDate->format('Y'));
        $birthDateMaxYear = $currentDateYear - self::MAX_AGE_FOR_RACING;
        if ($birthDateYear <= $birthDateMaxYear) {
            throw new BoundaryDateException('The competitor age must not be 
                equal or superior to ' . self::MAX_AGE_FOR_RACING . ' years.');
        }
        $birthDateMinYear = $currentDateYear - self::MIN_AGE_FOR_RACING;
        if ($birthDateYear > $birthDateMinYear) {
            throw new BoundaryDateException('The competitor age must not be 
                inferior to ' . self::MIN_AGE_FOR_RACING . ' years.');
        }
        if ($raceNumber < 0) {
            throw new NegativeNumberException('The competitor race number must not be 
                a negative number.');
        }
        if ($raceNumber == 0) {
            throw new BoundaryNumberException('The competitor race number must not be zero.');
        }
        if (trim($name) == '') {
            throw new EmptyStringException('The competitor name must not be empty.');
        }
        if (trim($firstName) == '') {
            throw new EmptyStringException('The competitor firstname must not be empty.');
        }
        if (trim($emailAddress) == '') {
            throw new EmptyStringException('The competitor email address must not be empty.');
        }
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false) {
            throw new EmailAddressSyntaxException('The competitor email address must be 
                conform to syntax in RFC 822.');
        }
        if (isset($photo) === true && trim($photo) === '') {
            throw new EmptyStringException('The competitor photo must not be 
                a empty string.');
        }
        $imageExtensionAcceptedArray = self::IMAGE_EXTENSION_ACCEPTED;
        if (isset($photo) === true && count($imageExtensionAcceptedArray) > 0) {
            $imageExtensionAccepted = '';
            foreach ($imageExtensionAcceptedArray as $imageExtension) {
                $imageExtensionAccepted .= strtoupper($imageExtension) . '|';
            }
            $imageExtensionAccepted = preg_replace('/\|$/', '', $imageExtensionAccepted);
            if (preg_match('/\.(' . $imageExtensionAccepted . ')$/i', $photo) !== 1) {
                if (count($imageExtensionAcceptedArray) > 1) {
                    $imageExtensionAcceptedForException = preg_replace(
                        '/\|/',
                        ', ',
                        $imageExtensionAccepted
                    );
                    $imageExtensionAcceptedForException = preg_replace(
                        '/\,\s([^\,]+)$/i',
                        " and $1",
                        $imageExtensionAcceptedForException
                    );
                }
                throw new ImageExtensionException('The competitor photo must have 
                    a conform file extension (only accept 
                    ' . $imageExtensionAcceptedForException . ')');
            }
        }
        $this->name = $name;
        $this->firstName = $firstName;
        $this->raceNumber = $raceNumber;
        $this->birthDate = $birthDate;
        $this->emailAddress = $emailAddress;
        $this->photo = $photo;
        $this->contestIdentifier = $contestIdentifier;
        $this->categoryIdentifier = $categoryIdentifier;
        $this->profileIdentifier = $profileIdentifier;
        $this->identifier = $identifier;
    }

    public static function fromState(array $state): Competitor
    {
        $identifier = (empty($state['identifier']) === true
            && is_numeric($state['identifier']) === false)
            ? null : (int) $state['identifier'];
        $photo = (empty($state['photo']) === true
            || strtoupper($state['photo']) === 'NULL')
            ? null : (string) $state['photo'];
        $profileIdentifier = (empty($state['profileIdentifier']) === true
            && is_numeric($state['profileIdentifier']) === false)
            ? null : (int) $state['profileIdentifier'];
        return new self(
            (string) $state['name'],
            (string) $state['firstName'],
            (int) $state['raceNumber'],
            new \DateTimeImmutable($state['birthDate'], new \DateTimeZone(self::TIME_ZONE)),
            (string) $state['emailAddress'],
            (int) $state['contestIdentifier'],
            (int) $state['categoryIdentifier'],
            $profileIdentifier,
            $photo,
            $identifier
        );
    }

    /**
     * Representation of the competitor object in a array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'name' => $this->name,
            'firstName' => $this->firstName,
            'raceNumber' => $this->raceNumber,
            'birthDate' => $this->birthDate,
            'emailAddress' => $this->emailAddress,
            'photo' => $this->photo,
            'contestIdentifier' => $this->contestIdentifier,
            'categoryIdentifier' => $this->categoryIdentifier,
            'profileIdentifier' => $this->profileIdentifier
        ];
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
            throw new AlreadySetException('It is not possible to set a competitor identifier already set.');
        }
        $this->identifier = $identifier;
    }
        
    /**
     * @return string
     */
    public function getTimeZone(): string
    {
        return self::TIME_ZONE;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @throws EmptyStringException If parameter name is a empty string
     * @return void
     */
    public function setName(string $name): void
    {
        if (trim($name) == '') {
            throw new EmptyStringException('The competitor name must not be empty.');
        }
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param  string $firstname
     * @throws EmptyStringException If parameter first name is a empty string
     * @return void
     */
    public function setFirstName(string $firstName): void
    {
        if (trim($firstName) == '') {
            throw new EmptyStringException('The competitor firstname must not be empty.');
        }
        $this->firstName = $firstName;
    }

    /**
     * @return DateTimeInterface
     */
    public function getBirthDate(): \DateTimeInterface
    {
        return $this->birthDate;
    }

    /**
     * @param  DateTimeInterface $birthDate
     * @throws BoundaryDateException If parameter birth date is over or equal to max age for racing
     * @return void
     */
    public function setBirthDate(\DateTimeInterface $birthDate): void
    {
        $currentDate = new \DateTime('now', new \DateTimeZone(self::TIME_ZONE));
        $currentDateYear = intval($currentDate->format('Y'));
        $birthDateYear = intval($birthDate->format('Y'));
        $birthDateMaxYear = $currentDateYear - self::MAX_AGE_FOR_RACING;
        if ($birthDateYear <= $birthDateMaxYear) {
            throw new BoundaryDateException('The competitor age must not be 
                equal or superior to ' . self::MAX_AGE_FOR_RACING . ' years.');
        }
        $birthDateMinYear = $currentDateYear - self::MIN_AGE_FOR_RACING;
        if ($birthDateYear > $birthDateMinYear) {
            throw new BoundaryDateException('The competitor age must not be 
                inferior to ' . self::MIN_AGE_FOR_RACING . ' years.');
        }
        $this->birthDate = $birthDate;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @param  string $emailAddress
     * @throws EmptyStringException If parameter email address is a empty string
     * @throws EmailAddressSyntaxException If parameter email address symtax is not valid
     * @return void
     */
    public function setEmailAddress(string $emailAddress): void
    {
        if (trim($emailAddress) == '') {
            throw new EmptyStringException('The competitor email address must not be empty.');
        }
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false) {
            throw new EmailAddressSyntaxException('The competitor email address must be 
                conform to syntax in RFC 822.');
        }
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return int
     */
    public function getRaceNumber(): int
    {
        return $this->raceNumber;
    }

    /**
     * @param  int $raceNumber
     * @throws NegativeNumberException If parameter race number is negative
     * @throws BoundaryNumberException If parameter race number is equal to zero
     * @return void
     */
    public function setRaceNumber(int $raceNumber): void
    {
        if ($raceNumber < 0) {
            throw new NegativeNumberException('The competitor race number must not be 
                a negative number.');
        }
        if ($raceNumber == 0) {
            throw new BoundaryNumberException('The competitor race number must not be zero.');
        }
        $this->raceNumber = $raceNumber;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param  string|null $photo
     * @throws EmptyStringException If parameter photo is a empty string
     * @throws ImageExtensionException If photo file extension is not accepted
     * @return void
     */
    public function setPhoto(?string $photo): void
    {
        if (isset($photo) === true && trim($photo) === '') {
            throw new EmptyStringException('The competitor photo must not be 
                a empty string.');
        }
        $imageExtensionAcceptedArray = self::IMAGE_EXTENSION_ACCEPTED;
        if (isset($photo) === true && count($imageExtensionAcceptedArray) > 0) {
            $imageExtensionAccepted = '';
            foreach ($imageExtensionAcceptedArray as $imageExtension) {
                $imageExtensionAccepted .= strtoupper($imageExtension) . '|';
            }
            $imageExtensionAccepted = preg_replace('/\|$/', '', $imageExtensionAccepted);
            if (preg_match('/\.(' . $imageExtensionAccepted . ')$/i', $photo) !== 1) {
                if (count($imageExtensionAcceptedArray) > 1) {
                    $imageExtensionAcceptedForException = preg_replace(
                        '/\|/',
                        ', ',
                        $imageExtensionAccepted
                    );
                    $imageExtensionAcceptedForException = preg_replace(
                        '/\,\s([^\,]+)$/i',
                        " and $1",
                        $imageExtensionAcceptedForException
                    );
                }
                throw new ImageExtensionException('The competitor photo must have 
                    a conform file extension (only accept 
                    ' . $imageExtensionAcceptedForException . ')');
            }
        }
        $this->photo = $photo;
    }
}
