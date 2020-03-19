<?php

declare(strict_types=1);

namespace PS\Domain\Entity;

interface EntityInterface
{
    /**
     * @return int
     */
    public function getId(): ?int;

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self;
}
