<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Competitor;

it('should create a competitor', function () {

    $competitorData = [
        'name' => 'test',
        'firstName' => 'prénom',
        'raceNumber' => 50,
        'birthDate' => new \DateTime(),
        'email' => 'truc@test.fr',
        'photo' => 'dossier_généré/photo_name.jpg'
    ];

    $newCompetitor = new Competitor($competitorData['name'], $competitorData['firstName'], 
        $competitorData['raceNumber'], $competitorData['birthDate'], 
        $competitorData['email'], $competitorData['photo']);

    $this->assertInstanceOf(Competitor::class, $newCompetitor);

    $this->assertSame($competitorData, $newCompetitor->toArray());

});
