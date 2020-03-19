#!/usr/local/bin/php
<?php

include_once implode(DIRECTORY_SEPARATOR, ['vendor', 'autoload.php']);

if ($argc !== 2) {
    printf("Error: Wrong command parameters. Only filename is expecting.\n");
    exit(1);
}

// file validation
if (strcmp(strtolower(pathinfo($argv[1], PATHINFO_EXTENSION)), 'csv')) {
    printf("Error: Only .csv files are supported.\n");
    exit(1);
}

if (!file_exists($argv[1]) || !is_file($argv[1])) {
    printf("Error: File %s not exists.\n", $argv[1]);
    exit(1);
}

$file = new \SplFileObject($argv[1]);
if (!$file->valid()) {
    printf("Error: File %s is not valid.\n", $argv[1]);
    exit(1);
}

try {
    $config = include_once implode(DIRECTORY_SEPARATOR, ['config', 'parameters.php']);
    $handler = new \PS\Application\Handler\TransactionHandler(
        new \PS\Infrastructure\Repository\TransactionRepository(),
        new \PS\Domain\Service\Exchange($config),
        $config
    );

    $file->setFlags(\SplFileObject::READ_CSV);
    foreach ($file as $row) {
        $request = new \PS\Application\Request(array_combine(
            [
                'transaction_date',
                'user_id',
                'user_type',
                'transaction_type',
                'amount',
                'currency'
            ],
            $row
        ));
        $response = $handler->process($request);

        printf("%s\n", $response->getBody());
    }
} catch (\Throwable $exception) {
    printf("Error: %s\n", $exception->getMessage());
    exit(1);
}

exit(0);
