<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Competitor;
use App\Model\CompetitorModel;

class CompetitorRepository implements RepositoryInterface
{
    public function add(object $object): void
    {}
    public function remove(int $id): void
    {}
    public function modify(object $object): void
    {}
    public function findById(int $id): ?object
    {}
    public function findBy(array $conditions, int $offset = 0, int $limit = -1, array $order = [], array $having = [], array $group = []): ?array
    {}
    public function findAll(): ?array
    {}
    public function findByRaceNumber(int $raceNumber, int $contestIdentifier): Competitor
    {
        $data = ['identifier' => 1,
        'name' => 'name',
        'firstName' => 'firstName',
        'raceNumber' => 10,
        'birthDate' => '2000-10-27',
        'emailAddress' => 'romain@orange.fr',
        'photo' => 'photo.jpg',
        'contestIdentifier' => 1,
        'categoryIdentifier' => 1,
        'profileIdentifier' => 1];
        return Competitor::fromState($data);
    }
}