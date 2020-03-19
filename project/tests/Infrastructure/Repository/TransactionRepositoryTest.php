<?php

declare(strict_types=1);

namespace PS\Tests\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use PS\Domain\Entity\Transaction;
use PS\Infrastructure\Repository\TransactionRepository;

class TransactionRepositoryTest extends TestCase
{
    /**
     * @var TransactionRepository
     */
    private TransactionRepository $repository;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new TransactionRepository();
        $this->repository
            ->getConnection()
            ->offsetSet(
                $this->repository->getCollectionName(),
                require implode(
                    DIRECTORY_SEPARATOR,
                    [
                        dirname(__DIR__),
                        '..',
                        'Fixtures',
                        'transactions.php',
                    ]
                )
            );
    }

    /**
     * @param array $criteria
     * @param array $sort
     * @param array $results
     *
     * @dataProvider criteriaDataProvider
     */
    public function testFindBy(array $criteria = null, array $sort = null, array $results): void
    {
        $rows = $this->repository->findBy($criteria, $sort);

        $this->assertIsArray($rows);
        $this->assertCount($results[0], $rows);
        $this->assertInstanceOf(Transaction::class, $rows[0]);
        $this->assertEquals($results[1], $rows[0]->getCreatedAt()->format('Y-m-d'));
    }

    /**
     * @return array
     */
    public function criteriaDataProvider(): array
    {
        return [
            'get all records unsorted' => [
                null,
                null,
                [13, '2016-12-31'],
            ],
            'get all records with user id 4 and sort desc' => [
                ['userId' => 4],
                ['createdAt' => 'desc'],
                [3, '2016-12-31'],
            ],
            'get all records with user id 1 and sort asc' => [
                ['userId' => 1],
                ['createdAt' => 'asc'],
                [6, '2016-01-05'],
            ],
        ];
    }

    /**
     * @param array $criteria
     * @param string|null $sortField
     *
     * @testWith    [{"userId": 1}]
     */
    public function testFindOneBy(array $criteria): void
    {
        $entity = $this->repository->findOneBy($criteria);

        $this->assertInstanceOf(Transaction::class, $entity);
    }

    /**
     * @param array $criteria
     *
     * @testWith    [{"userId": 4}]
     */
    public function testCount(array $criteria): void
    {
        $count = $this->repository->count($criteria);

        $this->assertIsInt($count);
        $this->assertEquals(3, $count);
    }

    /**
     * @return void
     */
    public function testInsert(): void
    {
        $this->repository->getConnection()->offsetUnset($this->repository->getCollectionName());

        $firstTransaction = new Transaction();
        $this->repository->insert($firstTransaction);

        $this->assertEquals(1, $firstTransaction->getId());

        $secondTransaction = new Transaction();
        $this->repository->insert($secondTransaction);

        $this->assertEquals(2, $secondTransaction->getId());
    }
}
