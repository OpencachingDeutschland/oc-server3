<?php

namespace OcTest\Modules\Oc\User;

use Oc\Country\CountryEntity;
use Oc\Country\CountryRepository;
use Oc\Country\CountryService;
use Oc\Repository\Exception\RecordsNotFoundException;
use OcTest\Modules\TestCase;

/**
 * Class CountryServiceTest
 */
class CountryServiceTest extends TestCase
{
    /**
     * Tests fetching all records with success - no exception is thrown.
     */
    public function testFetchingAllReturnsArrayWithCountryEntities(): void
    {
        $entityArray = [
            new CountryEntity(),
            new CountryEntity(),
        ];

        $repository = $this->createMock(CountryRepository::class);
        $repository->method('fetchAll')
            ->willReturn($entityArray);

        $service = new CountryService($repository);

        $result = $service->fetchAll();

        self::assertSame($entityArray, $result);
    }

    /**
     * Tests fetching all records - exception is thrown because there are no records.
     */
    public function testFetchingAllThrowsException(): void
    {
        $exception = new RecordsNotFoundException('No records found');

        $repository = $this->createMock(CountryRepository::class);
        $repository->method('fetchAll')
            ->willThrowException($exception);

        $service = new CountryService($repository);

        $result = $service->fetchAll();

        self::assertEmpty($result);
    }

    /**
     * Tests that create returns the entity.
     */
    public function testCreateReturnsEntity(): void
    {
        $entity = new CountryEntity();

        $repository = $this->createMock(CountryRepository::class);
        $repository->method('create')
            ->with($entity)
            ->willReturn($entity);

        $userService = new CountryService($repository);

        $result = $userService->create($entity);

        self::assertSame($entity, $result);
    }

    /**
     * Tests that update returns the entity.
     */
    public function testUpdateReturnsEntity(): void
    {
        $entity = new CountryEntity();

        $repository = $this->createMock(CountryRepository::class);
        $repository->method('update')
                   ->with($entity)
                   ->willReturn($entity);

        $userService = new CountryService($repository);

        $result = $userService->update($entity);

        self::assertSame($entity, $result);
    }

    /**
     * Tests that remove returns the entity.
     */
    public function testRemoveReturnsEntity(): void
    {
        $entity = new CountryEntity();

        $repository = $this->createMock(CountryRepository::class);
        $repository->method('remove')
                   ->with($entity)
                   ->willReturn($entity);

        $userService = new CountryService($repository);

        $result = $userService->remove($entity);

        self::assertSame($entity, $result);
    }
}
