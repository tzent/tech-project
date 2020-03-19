<?php

declare(strict_types=1);

namespace PS\Domain\Service\TransactionContext;

use PS\Domain\Entity\Transaction;
use PS\Domain\Service\Exchange;

class NaturalTransactionContext extends TransactionContextAbstract
{
    private const DISCOUNT_MAX_TRANSACTIONS = 3;
    private const DISCOUNT_MAX_AMOUNT = 1000.00;

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
        $amount = $transaction->getAmount();
        $discount = $this->getDiscount($transaction);

        if ($discount > 0) {
            $defCurrencyAmount = $this->exchange->convert(
                (string) $amount,
                $transaction->getCurrency(),
                Exchange::DEFAULT_CURRENCY
            );

            $rest = bcsub($defCurrencyAmount, (string) $discount, 3);

            if (bccomp($rest, '0', 3) !== 1) {
                return 0;
            }

            $amount = (float) $this->exchange->convert(
                $rest,
                Exchange::DEFAULT_CURRENCY,
                $transaction->getCurrency()
            );
        }

        return $this->calculateFeeByType(
            $transaction->getUserType(),
            $transaction->getType(),
            $amount,
            $transaction->getCurrency()
        );
    }

    /**
     * @param Transaction $transaction
     * @return float
     */
    private function getDiscount(Transaction $transaction): float
    {
        $transactions = $this->transactionRepository->findBy(
            [
                'userId' => $transaction->getUserId(),
                'type' => $transaction->getType(),
            ],
            [
                'createdAt' => 'desc',
            ]
        );

        $count = 0;
        $totalAmount = 0;
        $lastMondayTime = strtotime('last Monday', strtotime($transaction->getCreatedAt()->format('Y-m-d')));
        /** @var Transaction $t */
        foreach ($transactions as $t) {
            if ($t->getCreatedAt()->getTimestamp() >= $lastMondayTime) {
                ++$count;
                $totalAmount += $this->exchange->convert(
                    (string) $t->getAmount(),
                    $t->getCurrency(),
                    Exchange::DEFAULT_CURRENCY
                );
            } else {
                //we don't need to check older
                break;
            }
        }

        if ($count >= static::DISCOUNT_MAX_TRANSACTIONS || $totalAmount > static::DISCOUNT_MAX_AMOUNT) {
            return 0;
        }

        return (float) bcsub((string) static::DISCOUNT_MAX_AMOUNT, (string) $totalAmount, 3);
    }
}
