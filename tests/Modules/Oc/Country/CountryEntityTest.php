<?php

namespace OcTest\Modules\Oc\User;

use Oc\Country\CountryEntity;
use OcTest\Modules\TestCase;

/**
 * Class CountryEntityTest
 */
class CountryEntityTest extends TestCase
{
    /**
     * Tests that isNew returns true when the identifier id is null.
     */
    public function testIsNewReturnsTrueOnIdentifierEqualsNull(): void
    {
        $entity = new CountryEntity();

        self::assertTrue($entity->isNew());
    }

    /**
     * Tests that isNew returns false when the identifier id is not null.
     */
    public function testIsNewReturnsFalseWhenIdentifierIsPresent(): void
    {
        $entity = new CountryEntity();
        $entity->short = 'de';

        self::assertFalse($entity->isNew());
    }

    /**
     * Tests toArray returns correct array.
     */
    public function testToArray(): void
    {
        $entity = new CountryEntity();
        $entity->short = 'AD';
        $entity->name = 'Andorra';
        $entity->translationId = 447;
        $entity->de = 'Andorra';
        $entity->en = 'Andorra';
        $entity->listDefaultDe = 0;
        $entity->listDefaultEn = 'andorra';
        $entity->sortDe = 0;
        $entity->sortEn = 'andorra';

        $result = $entity->toArray();

        self::assertSame($entity->short, $result['short']);
        self::assertSame($entity->name, $result['name']);
        self::assertSame($entity->translationId, $result['translationId']);
        self::assertSame($entity->de, $result['de']);
        self::assertSame($entity->en, $result['en']);
        self::assertSame($entity->listDefaultDe, $result['listDefaultDe']);
        self::assertSame($entity->listDefaultEn, $result['listDefaultEn']);
        self::assertSame($entity->sortDe, $result['sortDe']);
        self::assertSame($entity->sortEn, $result['sortEn']);
    }

    /**
     * Tests fromArray applies correct values.
     */
    public function testFromArray(): void
    {
        $entityArray = [
            'short' => 'AD',
            'name' => 'Andorra',
            'translationId' => 447,
            'de' => 'Andorra',
            'en' => 'Andorra',
            'listDefaultDe' => 0,
            'listDefaultEn' => 'andorra',
            'sortDe' => 0,
            'sortEn' => 'andorra',
        ];

        $entity = new CountryEntity();
        $entity->fromArray($entityArray);

        self::assertSame($entityArray['short'], $entity->short);
        self::assertSame($entityArray['name'], $entity->name);
        self::assertSame($entityArray['translationId'], $entity->translationId);
        self::assertSame($entityArray['de'], $entity->de);
        self::assertSame($entityArray['en'], $entity->en);
        self::assertSame($entityArray['listDefaultDe'], $entity->listDefaultDe);
        self::assertSame($entityArray['listDefaultEn'], $entity->listDefaultEn);
        self::assertSame($entityArray['sortDe'], $entity->sortDe);
        self::assertSame($entityArray['sortEn'], $entity->sortEn);
    }
}
