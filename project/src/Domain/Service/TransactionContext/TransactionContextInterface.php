<?php

declare(strict_types=1);

namespace PS\Domain\Service\TransactionContext;

use PS\Domain\Entity\EntityInterface;

interface TransactionContextInterface
{
    /**
     * @param \DateTimeInterface $createdAt
     * @param int $userId
     * @param string $userType
     * @param string $transactionType
     * @param float $amount
     * @param string $currency
     * @return EntityInterface
     */
    public function create(
        \DateTimeInterface $createdAt,
        int $userId,
        string $userType,
        string $transactionType,
        float $amount,
        string $currency
    ): EntityInterface;
}
