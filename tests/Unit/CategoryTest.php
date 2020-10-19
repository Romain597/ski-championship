<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Category;
use App\Exception\EmptyStringException;
use App\Exception\AlreadySetException;

function createCategoryObject(array $categoryDataParameter = []) : Category
{
    $categoryData = [
        'identifier' => 4,
        'name' => 'cat',
        'description' => 'text'
    ];
    if (!empty($categoryDataParameter) === true && 
        count(array_diff_key($categoryDataParameter,$categoryData)) === 0) {
        $categoryData = $categoryDataParameter;
    }
    return new Category($categoryData['name'], $categoryData['description'], 
        $categoryData['identifier']);
}

it('should create a category with right parameters', function () {

    $categoryData1 = [
        'identifier' => null,
        'name' => 'cat1',
        'description' => null
    ];
    $newCategory1 = new Category($categoryData1['name']);
    $this->assertInstanceOf(Category::class, $newCategory1);
    $this->assertSame($categoryData1, $newCategory1->toArray());

    $categoryData2 = [
        'identifier' => null,
        'name' => 'cat2',
        'description' => 'text2'
    ];
    $newCategory2 = new Category($categoryData2['name'], $categoryData2['description']);
    $this->assertInstanceOf(Category::class, $newCategory2);
    $this->assertSame($categoryData2, $newCategory2->toArray());

    $categoryData3 = [
        'identifier' => 520,
        'name' => 'cat3',
        'description' => 'text3'
    ];
    $newCategory3 = new Category($categoryData3['name'], $categoryData3['description'], 
        $categoryData3['identifier']);
    $this->assertInstanceOf(Category::class, $newCategory3);
    $this->assertSame($categoryData3, $newCategory3->toArray());

});

it('should throw a empty exception for instantiate a category with empty strings parameter', 
    function () {
    
    $newCategory1 = new Category('');

    $newCategory2 = new Category('cat2','');

})->throws(EmptyStringException::class);

it('should throw a type error for instantiate a category with bad types parameters', 
    function () {
    
    $newCategory1 = new Category(1);

    $newCategory2 = new Category(null, 'a');

    $newCategory3 = new Category('b', true);

    $newCategory4 = new Category([], '10.5', 1);

    $newCategory5 = new Category();

})->throws(\TypeError::class);

it('should return a array of a category object properties for the method "toArray()"', 
    function () {

    $newCategory = new Category('category1','text');
    $categoryData = $newCategory->toArray();
    $this->assertIsArray($categoryData);
    $this->assertArrayHasKey('identifier', $categoryData);
    $this->assertArrayHasKey('name', $categoryData);
    $this->assertArrayHasKey('description', $categoryData);

});

it('should throws a empty string exception when setting a category string property', 
    function () {

    $newCategory = createCategoryObject();
    
    $newCategory->setName('');
    $newCategory->setDescription('');  

})->throws(EmptyStringException::class);

it('should throw a identifier already set exception', function () {

    $newCategory = createCategoryObject([
        'identifier' => 1,
        'name' => 'cat1',
        'description' => 'text'
    ]);

    $newCategory->setIdentifier(2);

})->throws(AlreadySetException::class);
