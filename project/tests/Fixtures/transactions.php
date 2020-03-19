<?php
declare(strict_types=1);

$file = new \SplFileObject(__DIR__ . DIRECTORY_SEPARATOR . 'transactions.csv');
$file->setFlags(\SplFileObject::READ_CSV);

$transactions = [];

foreach ($file as $index => $row) {
    $transactions[] = (new \PS\Domain\Entity\Transaction())
        ->setId($index + 1)
        ->setCreatedAt(DateTime::createFromFormat('Y-m-d', $row[0]))
        ->setUserId((int) $row[1])
        ->setUserType($row[2])
        ->setType($row[3])
        ->setAmount((float) $row[4])
        ->setCurrency($row[5]);
}

return $transactions;
