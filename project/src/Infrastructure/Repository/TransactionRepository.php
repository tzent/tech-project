<?php

declare(strict_types=1);

namespace PS\Infrastructure\Repository;

use PS\Domain\Entity\Transaction;

class TransactionRepository extends RepositoryAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getCollectionName(): string
    {
        return Transaction::class;
    }
}
