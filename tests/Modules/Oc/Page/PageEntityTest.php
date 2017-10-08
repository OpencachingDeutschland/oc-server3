<?php

namespace OcTest\Modules\Oc\User;

use DateTime;
use Oc\Page\PageEntity;
use OcTest\Modules\TestCase;

/**
 * Class PageEntityTest
 *
 * @package OcTest\Modules\Oc\Country
 */
class PageEntityTest extends TestCase
{
    /**
     * Tests that isNew returns true when the identifier id is null.
     *
     * @return void
     */
    public function testIsNewReturnsTrueOnIdentifierEqualsNull()
    {
        $entity = new PageEntity();

        self::assertTrue($entity->isNew());
    }

    /**
     * Tests that isNew returns false when the identifier id is not null.
     *
     * @return void
     */
    public function testIsNewReturnsFalseWhenIdentifierIsPresent()
    {
        $entity = new PageEntity();
        $entity->id = 1;

        self::assertFalse($entity->isNew());
    }

    /**
     * Tests toArray returns correct array.
     *
     * @return void
     */
    public function testToArray()
    {
        $entity = new PageEntity();
        $entity->id = 1;
        $entity->slug = 'impressum';
        $entity->metaKeywords = 'keywords';
        $entity->metaDescription = 'description';
        $entity->metaSocial = 'social';
        $entity->updatedAt = new DateTime();
        $entity->active = true;

        $result = $entity->toArray();

        self::assertSame($entity->id, $result['id']);
        self::assertSame($entity->slug, $result['slug']);
        self::assertSame($entity->metaKeywords, $result['metaKeywords']);
        self::assertSame($entity->metaDescription, $result['metaDescription']);
        self::assertSame($entity->metaSocial, $result['metaSocial']);
        self::assertSame($entity->updatedAt, $result['updatedAt']);
        self::assertSame($entity->active, $result['active']);
    }

    /**
     * Tests fromArray applies correct values.
     *
     * @return void
     */
    public function testFromArray()
    {
        $entityArray = [
            'id' => 1,
            'slug' => 'impressum',
            'metaKeywords' => 'keywords',
            'metaDescription' => 'description',
            'metaSocial' => 'social',
            'updatedAt' => new DateTime(),
            'active' => true
        ];

        $entity = new PageEntity();
        $entity->fromArray($entityArray);

        self::assertSame($entityArray['id'], $entity->id);
        self::assertSame($entityArray['slug'], $entity->slug);
        self::assertSame($entityArray['metaKeywords'], $entity->metaKeywords);
        self::assertSame($entityArray['metaDescription'], $entity->metaDescription);
        self::assertSame($entityArray['metaSocial'], $entity->metaSocial);
        self::assertSame($entityArray['updatedAt'], $entity->updatedAt);
        self::assertSame($entityArray['active'], $entity->active);
    }
}
