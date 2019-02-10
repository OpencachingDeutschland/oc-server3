<?php

namespace OcTest\Modules\Oc\User;

use Oc\User\UserEntity;
use OcTest\Modules\TestCase;

/**
 * Class UserEntityTest
 */
class UserEntityTest extends TestCase
{
    /**
     * Tests that isNew returns true when the identifier id is null.
     */
    public function testIsNewReturnsTrueOnIdentifierEqualsNull(): void
    {
        $user = new UserEntity();

        self::assertTrue($user->isNew());
    }

    /**
     * Tests that isNew returns false when the identifier id is not null.
     */
    public function testIsNewReturnsFalseWhenIdentifierIsPresent(): void
    {
        $user = new UserEntity();
        $user->id = 1;

        self::assertFalse($user->isNew());
    }

    /**
     * Tests toArray returns correct array.
     */
    public function testToArray(): void
    {
        $user = new UserEntity();
        $user->id = 1;
        $user->firstname = 'Max';
        $user->lastname = 'Mustermann';
        $user->country = 'DE';
        $user->username = 'mmustermann';
        $user->email = 'max@mustermann.de';
        $user->language = 'de';
        $user->latitude = 51.00;
        $user->longitude = 09.00;
        $user->isActive = true;

        $result = $user->toArray();

        self::assertSame($user->id, $result['id']);
        self::assertSame($user->firstname, $result['firstname']);
        self::assertSame($user->lastname, $result['lastname']);
        self::assertSame($user->country, $result['country']);
        self::assertSame($user->username, $result['username']);
        self::assertSame($user->email, $result['email']);
        self::assertSame($user->language, $result['language']);
        self::assertSame($user->latitude, $result['latitude']);
        self::assertSame($user->longitude, $result['longitude']);
        self::assertSame($user->isActive, $result['isActive']);
    }

    /**
     * Tests fromArray applies correct values.
     */
    public function testFromArray(): void
    {
        $userArray = [
            'id' => 1,
            'firstname' => 'Max',
            'lastname' => 'Mustermann',
            'country' => 'DE',
            'username' => 'mmustermann',
            'email' => 'max@mustermann.de',
            'language' => 'de',
            'latitude' => 51.00,
            'longitude' => 09.00,
            'isActive' => true,

            //Property that does not exist in the user entity to test the AbstractEntity
            'no_property' => null,
        ];

        $user = new UserEntity();
        $user->fromArray($userArray);

        self::assertSame($userArray['id'], $user->id);
        self::assertSame($userArray['firstname'], $user->firstname);
        self::assertSame($userArray['lastname'], $user->lastname);
        self::assertSame($userArray['country'], $user->country);
        self::assertSame($userArray['username'], $user->username);
        self::assertSame($userArray['email'], $user->email);
        self::assertSame($userArray['language'], $user->language);
        self::assertSame($userArray['latitude'], $user->latitude);
        self::assertSame($userArray['longitude'], $user->longitude);
        self::assertSame($userArray['isActive'], $user->isActive);
    }
}
