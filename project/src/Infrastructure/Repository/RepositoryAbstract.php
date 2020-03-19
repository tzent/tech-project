<?php

declare(strict_types=1);

namespace PS\Infrastructure\Repository;

use ArrayObject as Connection;
use PS\Domain\Entity\EntityInterface;

abstract class RepositoryAbstract implements RepositoryInterface
{
    /**
     * @var Connection
     */
    protected Connection $connection;

    /**
     * RepositoryAbstract constructor.
     */
    public function __construct()
    {
        $this->connection = new Connection();
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria = null, array $sort = null): array
    {
        $collectionName = $this->getCollectionName();

        $rows = [];

        if ($this->connection->offsetExists($collectionName)) {
            $rows = array_filter(
                    $this->connection->offsetGet($collectionName),
                    function (EntityInterface $row) use ($criteria) {
                        if (!empty($criteria)) {
                            foreach ($criteria as $parameter => $value) {
                                $getter = sprintf('get%s', ucfirst($parameter));
                                if ((string) $row->{$getter}() !== (string) $value) {
                                    return false;
                                }
                            }
                        }

                        return true;
                    }
                );

            $rows = $this->sort($rows, $sort);
        }

        return $rows;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria = null, array $sort = null): ?EntityInterface
    {
        $rows = $this->findBy($criteria, $sort);

        return empty($rows)
            ? null
            : current($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function count(array $criteria = null): int
    {
        return empty($criteria)
            ? $this->connection->count()
            : count($this->findBy($criteria));
    }

    protected function sort(array $rows, array $sort = null): array
    {
        if (!empty($sort)) {
            if (count($sort) !== 1) {
                throw new \InvalidArgumentException('Sort by many fields is not allowed');
            }

            $direction = strtolower(current($sort));
            $getter = sprintf('get%s', ucfirst(array_key_first($sort)));
            usort($rows, function (EntityInterface $first, EntityInterface $second) use ($getter, $direction) {
                return $direction === 'desc'
                    ? -($first->{$getter}() <=> $second->{$getter}())
                    : ($first->{$getter}() <=> $second->{$getter}());
            });
        }

        return $rows;
    }

    /**
     * @param EntityInterface &$entity
     */
    public function insert(EntityInterface &$entity): void
    {
        $collectionName = $this->getCollectionName();

        if ($this->connection->offsetExists($collectionName)) {
            $last = $this->findOneBy(null, ['id' => 'desc']);
            $entity->setId($last->getId() + 1);

            $collection = $this->connection->offsetGet($collectionName);
            $collection[] = $entity;

            $this->connection->offsetSet($collectionName, $collection);
        } else {
            $entity->setId(1);
            $this->connection->offsetSet($collectionName, [$entity]);
        }
    }

    abstract public function getCollectionName(): string;
}
