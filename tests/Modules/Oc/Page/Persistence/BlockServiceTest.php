<?php

namespace OcTest\Modules\Oc\Page\Persistence;

use Oc\Page\Persistence\BlockEntity;
use Oc\Page\Persistence\BlockRepository;
use Oc\Page\Persistence\BlockService;
use Oc\Repository\Exception\RecordsNotFoundException;
use OcTest\Modules\TestCase;

/**
 * Class BlockServiceTest
 */
class BlockServiceTest extends TestCase
{
    /**
     * Tests fetching one record by id with success - no exception is thrown.
     */
    public function testFetchingOneByIdReturnsEntity(): void
    {
        $entityArray = [
            new BlockEntity(),
        ];

        $whereClause = [
            'page_id' => 1,
            'locale' => 'de',
            'active' => 1,
        ];

        $repository = $this->createMock(BlockRepository::class);
        $repository->method('fetchBy')
            ->with($whereClause)
            ->willReturn($entityArray);

        $service = new BlockService($repository);

        $result = $service->fetchBy($whereClause);

        self::assertSame($entityArray, $result);
    }

    /**
     * Tests fetching one record by id - exception is thrown because there is no record.
     */
    public function testFetchingOneByIdThrowsException(): void
    {
        $exception = new RecordsNotFoundException('No records with given where clause found');

        $whereClause = [
            'page_id' => 1,
            'locale' => 'de',
            'active' => 1,
        ];

        $repository = $this->createMock(BlockRepository::class);
        $repository->method('fetchBy')
            ->with($whereClause)
            ->willThrowException($exception);

        $service = new BlockService($repository);

        $result = $service->fetchBy($whereClause);

        self::assertEmpty($result);
    }

    /**
     * Tests that create returns the entity.
     */
    public function testCreateReturnsEntity(): void
    {
        $entity = new BlockEntity();

        $repository = $this->createMock(BlockRepository::class);
        $repository->method('create')
            ->with($entity)
            ->willReturn($entity);

        $userService = new BlockService($repository);

        $result = $userService->create($entity);

        self::assertSame($entity, $result);
    }

    /**
     * Tests that update returns the entity.
     */
    public function testUpdateReturnsEntity(): void
    {
        $entity = new BlockEntity();

        $repository = $this->createMock(BlockRepository::class);
        $repository->method('update')
            ->with($entity)
            ->willReturn($entity);

        $userService = new BlockService($repository);

        $result = $userService->update($entity);

        self::assertSame($entity, $result);
    }

    /**
     * Tests that remove returns the entity.
     */
    public function testRemoveReturnsEntity(): void
    {
        $entity = new BlockEntity();

        $repository = $this->createMock(BlockRepository::class);
        $repository->method('remove')
            ->with($entity)
            ->willReturn($entity);

        $userService = new BlockService($repository);

        $result = $userService->remove($entity);

        self::assertSame($entity, $result);
    }
}
