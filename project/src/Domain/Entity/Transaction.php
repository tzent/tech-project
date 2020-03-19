<?php

declare(strict_types=1);

namespace PS\Domain\Entity;

use DateTimeInterface;

class Transaction implements EntityInterface
{
    public const USER_TYPES = ['natural', 'legal'];
    public const TYPES = ['cash_in', 'cash_out'];

    /**
     * @var int|null
     */
    private ?int $id;

    /**
     * @var int
     */
    private int $userId;

    /**
     * @var string
     */
    private string $userType;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var float
     */
    private float $amount;

    /**
     * @var string
     */
    private string $currency;

    /**
     * @var float
     */
    private float $fee = 0.0;

    /**
     * @var DateTimeInterface
     */
    private DateTimeInterface $createdAt;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @param string $userType
     * @return $this
     */
    public function setUserType(string $userType): self
    {
        $userType = strtolower($userType);

        if (!in_array($userType, self::USER_TYPES, true)) {
            throw new \InvalidArgumentException(sprintf('User type %s is not allowed', $userType));
        }

        $this->userType = $userType;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $type = strtolower($type);

        if (!in_array($type, self::TYPES, true)) {
            throw new \InvalidArgumentException(sprintf('Type %s is not allowed', $type));
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = strtoupper($currency);

        return $this;
    }

    /**
     * @return float
     */
    public function getFee(): float
    {
        return $this->fee;
    }

    /**
     * @param float $fee
     * @return $this
     */
    public function setFee(float $fee): self
    {
        $this->fee = ((int) ceil($fee * 100)) / 100;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
