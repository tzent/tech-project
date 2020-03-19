<?php

declare(strict_types=1);

namespace PS\Tests\Domain\Service\TransactionContext;

use PHPUnit\Framework\TestCase;
use PS\Domain\Entity\Transaction;
use PS\Domain\Service\Exchange;
use PS\Domain\Service\TransactionContext\LegalTransactionContext;
use PS\Domain\Service\TransactionContext\TransactionContextInterface;
use PS\Infrastructure\Repository\TransactionRepository;

class LegalTransactionContextTest extends TestCase
{
    /**
     * @var TransactionContextInterface
     */
    private TransactionContextInterface $transactionContext;

    /**
     * @var array
     */
    private array $config = [
        'commission' => [
            'cash_in' => [
                'legal' => [
                    'fee' => 0.03,
                    'min' => 0,
                    'max' => 5.0,
                ],
            ],
            'cash_out' => [
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
        $this->transactionContext = new LegalTransactionContext(
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
     * @param float $calculatedFee
     *
     * @dataProvider transactionDataProvider
     */
    public function testCreate(
        \DateTime $createdAt,
        int $userId,
        string $userType,
        string $transactionType,
        float $amount,
        string $currency,
        float $calculatedFee
    ): void {
        $transaction = $this->transactionContext->create($createdAt, $userId, $userType, $transactionType, $amount, $currency);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($userId, $transaction->getUserId());
        $this->assertEquals($userType, $transaction->getUserType());
        $this->assertEquals($transactionType, $transaction->getType());
        $this->assertEquals($amount, $transaction->getAmount());
        $this->assertEquals($currency, $transaction->getCurrency());
        $this->assertEquals($calculatedFee, $transaction->getFee());
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function transactionDataProvider(): array
    {
        return [
            'cashIn transaction' => [
                new \DateTime(), 1, 'legal', 'cash_in', 100.00, 'EUR', 0.030,
            ],
            'cashIn transaction max. fee' => [
                new \DateTime(), 1, 'legal', 'cash_in', 1000000.00, 'EUR', 5.000,
            ],
            'cashOut transaction' => [
                new \DateTime(), 1, 'legal', 'cash_out', 300.00, 'EUR', 0.900,
            ],
            'cashOut transaction min. fee' => [
                new \DateTime(), 1, 'legal', 'cash_out', 10.00, 'EUR', 0.500,
            ],

        ];
    }
}
