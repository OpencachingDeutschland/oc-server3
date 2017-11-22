<?php

namespace OcTest\Modules\Oc\User;

use Oc\Page\PageEntity;
use Oc\Page\PageRepository;
use Oc\Page\PageService;
use Oc\Repository\Exception\RecordNotFoundException;
use OcTest\Modules\TestCase;

/**
 * Class PageServiceTest
 */
class PageServiceTest extends TestCase
{
    /**
     * Tests fetching one record by id with success - no exception is thrown.
     */
    public function testFetchingOneByIdReturnsEntity()
    {
        $entity = new PageEntity();

        $whereClause = [
            'slug' => 'impressum',
        ];

        $repository = $this->createMock(PageRepository::class);
        $repository->method('fetchOneBy')
            ->with($whereClause)
            ->willReturn($entity);

        $service = new PageService($repository);

        $result = $service->fetchOneBy($whereClause);

        self::assertSame($entity, $result);
    }

    /**
     * Tests fetching one record by id - exception is thrown because there is no record.
     */
    public function testFetchingOneByIdThrowsException()
    {
        $exception = new RecordNotFoundException('Record with id #1 not found');

        $whereClause = [
            'slug' => 'impressum',
        ];

        $repository = $this->createMock(PageRepository::class);
        $repository->method('fetchOneBy')
            ->with($whereClause)
            ->willThrowException($exception);

        $service = new PageService($repository);

        $result = $service->fetchOneBy($whereClause);

        self::assertNull($result);
    }

    /**
     * Tests that create returns the entity.
     */
    public function testCreateReturnsEntity()
    {
        $entity = new PageEntity();

        $repository = $this->createMock(PageRepository::class);
        $repository->method('create')
            ->with($entity)
            ->willReturn($entity);

        $userService = new PageService($repository);

        $result = $userService->create($entity);

        self::assertSame($entity, $result);
    }

    /**
     * Tests that update returns the entity.
     */
    public function testUpdateReturnsEntity()
    {
        $entity = new PageEntity();

        $repository = $this->createMock(PageRepository::class);
        $repository->method('update')
            ->with($entity)
            ->willReturn($entity);

        $userService = new PageService($repository);

        $result = $userService->update($entity);

        self::assertSame($entity, $result);
    }

    /**
     * Tests that remove returns the entity.
     */
    public function testRemoveReturnsEntity()
    {
        $entity = new PageEntity();

        $repository = $this->createMock(PageRepository::class);
        $repository->method('remove')
            ->with($entity)
            ->willReturn($entity);

        $userService = new PageService($repository);

        $result = $userService->remove($entity);

        self::assertSame($entity, $result);
    }
}
