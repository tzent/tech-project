<?php

declare(strict_types=1);

namespace PS\Domain\Service\TransactionContext;

use PS\Domain\Entity\Transaction;

class LegalTransactionContext extends TransactionContextAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function cashIn(Transaction $transaction): float
    {
        return $this->calculateFeeByType(
            $transaction->getUserType(),
            $transaction->getType(),
            $transaction->getAmount(),
            $transaction->getCurrency()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function cashOut(Transaction $transaction): float
    {
        return $this->calculateFeeByType(
            $transaction->getUserType(),
            $transaction->getType(),
            $transaction->getAmount(),
            $transaction->getCurrency()
        );
    }
}
