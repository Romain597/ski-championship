<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Profile;
use App\Exception\ProfileEmptyException;

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

it('should throw a empty exception for instantiate a profile with empty strings parameter', 
    function () {
    
    $newProfile1 = new Profile('');

    $newProfile2 = new Profile('profile2', '');

})->throws(ProfileEmptyException::class);

it('should throw a type error for instantiate a profile with bad types parameters', 
    function () {
    
    $newProfile1 = new Profile(1);

    $newProfile2 = new Profile(null, 'a');

    $newProfile3 = new Profile('b', true);

    $newProfile4 = new Profile([], '10.5', 1);

    $newProfile5 = new Profile();

})->throws(\TypeError::class);

it('should return a array of a profile object properties for the method "toArray()"', 
    function () {

    $newProfile = new Profile('profile1','text');
    $profileData = $newProfile->toArray();
    $this->assertIsArray($profileData);
    $this->assertArrayHasKey('identifier', $profileData);
    $this->assertArrayHasKey('name', $profileData);
    $this->assertArrayHasKey('description', $profileData);

});

/*it('should throw a exception when setting a passage time with a not conform float', 
    function () {

    $newProfile = createProfileObject([
            'identifier' => null,
            'name' => 'profile2',
            'description' => 'text2'
        ]);

    $this->expectException(PassageBoundaryException::class);
    $newProfile->setTime(95000);

});*/
