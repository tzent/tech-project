<?php

declare(strict_types=1);

namespace PS\Infrastructure\Repository;

use PS\Domain\Entity\EntityInterface;
use PS\Domain\Entity\Transaction;

interface RepositoryInterface
{
    public function findBy(array $criteria = null, array $sort = null): array;

    public function findOneBy(array $criteria, array $sort = null): ?EntityInterface;

    public function count(array $criteria = null): int;

    public function insert(Transaction &$transaction): void;
}
