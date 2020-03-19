<?php

declare(strict_types=1);

namespace PS\Application\Handler;

use PS\Application\Request;
use PS\Application\Response;
use PS\Domain\Entity\Transaction;
use PS\Domain\Service\Exchange;
use PS\Domain\Service\TransactionFactory;
use PS\Domain\Service\Validation;
use PS\Infrastructure\Repository\TransactionRepository;

class TransactionHandler
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
     * TransactionHandler constructor.
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
     * @param Request $request
     * @return Response
     */
    public function process(Request $request): Response
    {
        $validation = new Validation(
            $request->getArrayCopy(),
            [
                'transaction_date' => 'required;date',
                'user_id' => 'required;integer;positive',
                'user_type' => sprintf('required;in:["%s"]', implode('","', Transaction::USER_TYPES)),
                'transaction_type' => sprintf('required;in:["%s"]', implode('","', Transaction::TYPES)),
                'amount' => 'required;numeric;positive',
                'currency' => sprintf('required;in:["%s"]', implode('","', $this->exchange->getSupportedCurrencies())),
            ]
        );

        if (!$validation->validate($request)) {
            return new Response(json_encode($validation->getErrors()));
        }

        $transaction = (new TransactionFactory($this->transactionRepository, $this->exchange, $this->config))
            ->create(
                \DateTime::createFromFormat('Y-m-d', $request->offsetGet('transaction_date')),
                filter_var($request->offsetGet('user_id'), FILTER_VALIDATE_INT),
                $request->offsetGet('user_type'),
                $request->offsetGet('transaction_type'),
                filter_var($request->offsetGet('amount'), FILTER_VALIDATE_FLOAT),
                $request->offsetGet('currency')
            );

        $this->transactionRepository->insert($transaction);

        return new Response(number_format($transaction->getFee(), 2, '.', ''));
    }
}
