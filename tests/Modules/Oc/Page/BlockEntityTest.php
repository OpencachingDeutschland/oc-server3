<?php

namespace OcTest\Modules\Oc\User;

use DateTime;
use Oc\Page\BlockEntity;
use OcTest\Modules\TestCase;

/**
 * Class BlockEntityTest
 */
class BlockEntityTest extends TestCase
{
    /**
     * Tests that isNew returns true when the identifier id is null.
     */
    public function testIsNewReturnsTrueOnIdentifierEqualsNull()
    {
        $entity = new BlockEntity();

        self::assertTrue($entity->isNew());
    }

    /**
     * Tests that isNew returns false when the identifier id is not null.
     */
    public function testIsNewReturnsFalseWhenIdentifierIsPresent()
    {
        $entity = new BlockEntity();
        $entity->id = 1;

        self::assertFalse($entity->isNew());
    }

    /**
     * Tests toArray returns correct array.
     */
    public function testToArray()
    {
        $entity = new BlockEntity();
        $entity->id = 1;
        $entity->pageId = 1;
        $entity->title = 'my title';
        $entity->html = 'my html';
        $entity->position = 1;
        $entity->updatedAt = new DateTime();
        $entity->active = true;

        $result = $entity->toArray();

        self::assertSame($entity->id, $result['id']);
        self::assertSame($entity->pageId, $result['pageId']);
        self::assertSame($entity->title, $result['title']);
        self::assertSame($entity->html, $result['html']);
        self::assertSame($entity->position, $result['position']);
        self::assertSame($entity->updatedAt, $result['updatedAt']);
        self::assertSame($entity->active, $result['active']);
    }

    /**
     * Tests fromArray applies correct values.
     */
    public function testFromArray()
    {
        $entityArray = [
            'id' => 1,
            'pageId' => 1,
            'title' => 'my title',
            'html' => 'my html',
            'position' => 1,
            'updatedAt' => new DateTime(),
            'active' => true,
        ];

        $entity = new BlockEntity();
        $entity->fromArray($entityArray);

        self::assertSame($entityArray['id'], $entity->id);
        self::assertSame($entityArray['pageId'], $entity->pageId);
        self::assertSame($entityArray['title'], $entity->title);
        self::assertSame($entityArray['html'], $entity->html);
        self::assertSame($entityArray['position'], $entity->position);
        self::assertSame($entityArray['updatedAt'], $entity->updatedAt);
        self::assertSame($entityArray['active'], $entity->active);
    }
}
