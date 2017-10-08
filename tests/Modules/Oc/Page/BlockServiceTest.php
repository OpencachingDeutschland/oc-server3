<?php

namespace OcTest\Modules\Oc\User;

use Oc\Page\BlockEntity;
use Oc\Page\BlockRepository;
use Oc\Page\BlockService;
use Oc\Repository\Exception\RecordsNotFoundException;
use OcTest\Modules\TestCase;

/**
 * Class BlockServiceTest
 *
 * @package OcTest\Modules\Oc\Block
 */
class BlockServiceTest extends TestCase
{
    /**
     * Tests fetching one record by id with success - no exception is thrown.
     *
     * @return void
     */
    public function testFetchingOneByIdReturnsEntity()
    {
        $entity = new BlockEntity();

        $whereClause = [
            'page_id' => 1,
            'locale' => 'de',
            'active' => 1
        ];

        $repository = $this->createMock(BlockRepository::class);
        $repository->method('fetchBy')
            ->with($whereClause)
            ->willReturn($entity);

        $service = new BlockService($repository);

        $result = $service->fetchBy($whereClause);

        self::assertSame($entity, $result);
    }

    /**
     * Tests fetching one record by id - exception is thrown because there is no record.
     *
     * @return void
     */
    public function testFetchingOneByIdThrowsException()
    {
        $exception = new RecordsNotFoundException('No records with given where clause found');

        $whereClause = [
            'page_id' => 1,
            'locale' => 'de',
            'active' => 1
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
     *
     * @return void
     */
    public function testCreateReturnsEntity()
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
     *
     * @return void
     */
    public function testUpdateReturnsEntity()
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
     *
     * @return void
     */
    public function testRemoveReturnsEntity()
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
