<?php

namespace OcTest\Modules\Oc\User;

use Oc\Language\LanguageEntity;
use Oc\Language\LanguageRepository;
use Oc\Language\LanguageService;
use Oc\Repository\Exception\RecordsNotFoundException;
use OcTest\Modules\TestCase;

/**
 * Class LanguageServiceTest
 */
class LanguageServiceTest extends TestCase
{
    /**
     * Tests fetching all records with success - no exception is thrown.
     */
    public function testFetchingAllReturnsArrayWithLanguageEntities()
    {
        $entityArray = [
            new LanguageEntity(),
            new LanguageEntity(),
        ];

        $repository = $this->createMock(LanguageRepository::class);
        $repository->method('fetchAll')
            ->willReturn($entityArray);

        $service = new LanguageService($repository);

        $result = $service->fetchAll();

        self::assertSame($entityArray, $result);
    }

    /**
     * Tests fetching all records - exception is thrown because there are no records.
     */
    public function testFetchingAllThrowsException()
    {
        $exception = new RecordsNotFoundException('No records found');

        $repository = $this->createMock(LanguageRepository::class);
        $repository->method('fetchAll')
            ->willThrowException($exception);

        $service = new LanguageService($repository);

        $result = $service->fetchAll();

        self::assertEmpty($result);
    }

    /**
     * Tests fetching all translated records with success - no exception is thrown.
     */
    public function testFetchingAllTranslatedReturnsArrayWithLanguageEntities()
    {
        $entityArray = [
            new LanguageEntity(),
            new LanguageEntity(),
        ];

        $repository = $this->createMock(LanguageRepository::class);
        $repository->method('fetchAllTranslated')
                   ->willReturn($entityArray);

        $service = new LanguageService($repository);

        $result = $service->fetchAllTranslated();

        self::assertSame($entityArray, $result);
    }

    /**
     * Tests fetching all records - exception is thrown because there are no records.
     */
    public function testFetchingAllTranslatedThrowsException()
    {
        $exception = new RecordsNotFoundException('No records found');

        $repository = $this->createMock(LanguageRepository::class);
        $repository->method('fetchAllTranslated')
                   ->willThrowException($exception);

        $service = new LanguageService($repository);

        $result = $service->fetchAllTranslated();

        self::assertEmpty($result);
    }

    /**
     * Tests that create returns the entity.
     */
    public function testCreateReturnsEntity()
    {
        $entity = new LanguageEntity();

        $repository = $this->createMock(LanguageRepository::class);
        $repository->method('create')
            ->with($entity)
            ->willReturn($entity);

        $userService = new LanguageService($repository);

        $result = $userService->create($entity);

        self::assertSame($entity, $result);
    }

    /**
     * Tests that update returns the entity.
     */
    public function testUpdateReturnsEntity()
    {
        $entity = new LanguageEntity();

        $repository = $this->createMock(LanguageRepository::class);
        $repository->method('update')
            ->with($entity)
            ->willReturn($entity);

        $userService = new LanguageService($repository);

        $result = $userService->update($entity);

        self::assertSame($entity, $result);
    }

    /**
     * Tests that remove returns the entity.
     */
    public function testRemoveReturnsEntity()
    {
        $entity = new LanguageEntity();

        $repository = $this->createMock(LanguageRepository::class);
        $repository->method('remove')
            ->with($entity)
            ->willReturn($entity);

        $userService = new LanguageService($repository);

        $result = $userService->remove($entity);

        self::assertSame($entity, $result);
    }

    /**
     * Tests getting all available translations.
     */
    public function testGetAvailableTranslations()
    {
        $language1 = new LanguageEntity();
        $language1->short = 'DE';

        $language2 = new LanguageEntity();
        $language2->short = 'EN';

        $languageEntities = [
            $language1,
            $language2,
        ];

        $service = $this->getMock(
            LanguageService::class,
            ['fetchAllTranslated'],
            [$this->createMock(LanguageRepository::class)]
        );
        $service->method('fetchAllTranslated')
            ->willReturn($languageEntities);

        $result = $service->getAvailableTranslations();

        self::assertCount(2, $result);
        self::assertSame('de', $result[0]);
        self::assertSame('en', $result[1]);
    }
}
