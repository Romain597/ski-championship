<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Profile;
use App\Exception\EmptyStringException;
use App\Exception\AlreadySetException;

function createProfileObject(array $profileDataParameter = []) : Profile
{
    $profileData = [
        'identifier' => 4,
        'name' => 'profile',
        'description' => 'text'
    ];
    if (!empty($profileDataParameter) === true && 
        count(array_diff_key($profileDataParameter,$profileData)) === 0) {
        $profileData = $profileDataParameter;
    }
    return new Profile($profileData['name'], $profileData['description'], 
        $profileData['identifier']);
}

it('should create a profile with right parameters', function () {

    $profileData1 = [
        'identifier' => null,
        'name' => 'profile1',
        'description' => null
    ];
    $newProfile1 = new Profile($profileData1['name']);
    $this->assertInstanceOf(Profile::class, $newProfile1);
    $this->assertSame($profileData1, $newProfile1->toArray());

    $profileData2 = [
        'identifier' => null,
        'name' => 'profile2',
        'description' => 'text2'
    ];
    $newProfile2 = new Profile($profileData2['name'], $profileData2['description']);
    $this->assertInstanceOf(Profile::class, $newProfile2);
    $this->assertSame($profileData2, $newProfile2->toArray());

    $profileData3 = [
        'identifier' => 4,
        'name' => 'profile3',
        'description' => 'text3'
    ];
    $newProfile3 = new Profile($profileData3['name'], $profileData3['description'], 
        $profileData3['identifier']);
    $this->assertInstanceOf(Profile::class, $newProfile3);
    $this->assertSame($profileData3, $newProfile3->toArray());

});

it('should throws empty string exception for instantiate a profile with empty strings parameter', 
    function () {
    
    $newProfile1 = new Profile('');

    $newProfile2 = new Profile('profile2', '');

})->throws(EmptyStringException::class);

it('should throws type error for instantiate a profile with bad types parameters', 
    function () {
    
    $newProfile1 = new Profile(1);

    $newProfile2 = new Profile(null, 'a');

    $newProfile3 = new Profile('b', true);

    $newProfile4 = new Profile([], '10.5', 1);

    $newProfile5 = new Profile();

})->throws(\TypeError::class);

it('should return a array of a profile object properties for the method "toArray()"', 
    function () {

    $newProfile = createProfileObject([
            'identifier' => null,
            'name' => 'profile1',
            'description' => 'text'
        ]);
    $profileData = $newProfile->toArray();
    $this->assertIsArray($profileData);
    $this->assertArrayHasKey('identifier', $profileData);
    $this->assertArrayHasKey('name', $profileData);
    $this->assertArrayHasKey('description', $profileData);

});

it('should throws a empty string exception when setting a profile string property', 
    function () {

    $newProfile = createProfileObject([
            'identifier' => null,
            'name' => 'profile2',
            'description' => 'text2'
        ]);
    
    $newProfile->setName('');
    $newProfile->setDescription('');  

})->throws(EmptyStringException::class);

it('should throw a identifier already set exception', function () {

    $newProfile = createProfileObject([
        'identifier' => 1,
        'name' => 'profile1',
        'description' => 'text'
    ]);

    $newProfile->setIdentifier(2);

})->throws(AlreadySetException::class);
