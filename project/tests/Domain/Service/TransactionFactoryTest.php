<?php

declare(strict_types=1);

namespace PS\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use PS\Domain\Entity\Transaction;
use PS\Domain\Service\Exchange;
use PS\Domain\Service\TransactionFactory;
use PS\Infrastructure\Repository\TransactionRepository;

class TransactionFactoryTest extends TestCase
{
    /**
     * @var TransactionFactory
     */
    private TransactionFactory $transactionFactory;

    /**
     * @var array
     */
    private array $config = [
        'commission' => [
            'cash_in' => [
                'natural' => [
                    'fee' => 0.03,
                    'min' => 0,
                    'max' => 5.0,
                ],
                'legal' => [
                    'fee' => 0.03,
                    'min' => 0,
                    'max' => 5.0,
                ],
            ],
            'cash_out' => [
                'natural' => [
                    'fee' => 0.3,
                    'min' => 0,
                    'max' => 0,
                ],
                'legal' => [
                    'fee' => 0.3,
                    'min' => 0.5,
                    'max' => 0,
                ],
            ],
        ],
        'currencies' => [
            'default' => 'EUR',
            'rates' => [
                'EUR' => 1,
                'USD' => 1.1497,
                'JPY' => 129.53,
            ],
        ],
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->transactionFactory = new TransactionFactory(
            $this->createMock(TransactionRepository::class),
            new Exchange($this->config),
            $this->config
        );
    }

    /**
     * @param \DateTime $createdAt
     * @param int $userId
     * @param string $userType
     * @param string $transactionType
     * @param float $amount
     * @param string $currency
     *
     * @dataProvider transactionDataProvider
     */
    public function testCreate(
        \DateTime $createdAt,
        int $userId,
        string $userType,
        string $transactionType,
        float $amount,
        string $currency
    ): void {
        /** @var Transaction $transaction */
        $transaction = $this->transactionFactory
            ->create($createdAt, $userId, $userType, $transactionType, $amount, $currency);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($userId, $transaction->getUserId());
        $this->assertEquals($userType, $transaction->getUserType());
        $this->assertEquals($transactionType, $transaction->getType());
        $this->assertEquals($amount, $transaction->getAmount());
        $this->assertEquals($currency, $transaction->getCurrency());
        $this->assertIsFloat($transaction->getFee());
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function transactionDataProvider(): array
    {
        return [
            'natural client' => [
                new \DateTime(), 1, 'natural', 'cash_in', 100, 'EUR',
            ],
            'legal client' => [
                new \DateTime(), 1, 'natural', 'cash_in', 100, 'EUR',
            ],
        ];
    }
}
