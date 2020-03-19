<?php

declare(strict_types=1);

namespace PS\Domain\Service;

use PS\Domain\Entity\EntityInterface;
use PS\Infrastructure\Repository\TransactionRepository;

class TransactionFactory
{
    /**
     * @var TransactionRepository
     */
    private TransactionRepository $transactionRepository;

    /**
     * @var Exchange
     */
    private Exchange $exchange;

    /**
     * @var array
     */
    private array $config;

    /**
     * TransactionFactory constructor.
     * @param TransactionRepository $transactionRepository
     * @param Exchange $exchange
     * @param array $config
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        Exchange $exchange,
        array $config
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->exchange = $exchange;
        $this->config = $config;
    }

    /**
     * @param \DateTimeInterface $transactionDate
     * @param int $userId
     * @param string $userType
     * @param string $transactionType
     * @param float $amount
     * @param string $currency
     * @return EntityInterface
     */
    public function create(
        \DateTimeInterface $transactionDate,
        int $userId,
        string $userType,
        string $transactionType,
        float $amount,
        string $currency
    ): EntityInterface {
        $transactionContextClass = sprintf(
            '%s\TransactionContext\%sTransactionContext',
            __NAMESPACE__,
            ucfirst($userType)
        );

        if (!class_exists($transactionContextClass)) {
            throw new \InvalidArgumentException(sprintf('Invalid transaction context %s', $transactionType));
        }

        return (new $transactionContextClass($this->transactionRepository, $this->exchange, $this->config))
            ->create(
                $transactionDate,
                $userId,
                $userType,
                $transactionType,
                $amount,
                $currency
            );
    }
}
