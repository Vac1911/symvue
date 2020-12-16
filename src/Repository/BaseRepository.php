<?php


namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Exception;

class BaseRepository extends EntityRepository
{
    /**
     * List of associations to load
     *
     * @var array
     */
    public array $eagerAssociations = [];

    /**
     * Fluent way to add to $this->eagerAssociations
     *
     * @return BaseRepository $this
     * @throws Exception
     */
    public function with() : BaseRepository
    {
        foreach(func_get_args() as $assoc)
            $this->eagerAssociations[] = $assoc;

//        foreach(func_get_args() as $assoc)
//            if(in_array($assoc, array_keys($this->getClassMetadata()->associationMappings)))
//                $this->eagerAssociations[] = $assoc;
//            else
//                throw new Exception("Trying to load undefined association \"{$assoc}\" of {$this->getEntityName()} ");

        return $this;
    }

    /**
     * Get a QueryBuilder instance with associations being loaded. {@see ResultSetMapping}
     *
     * @return \Doctrine\ORM\QueryBuilder The created QueryBuilder instance.
     */
    public function eagerQB() : QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder()
            ->select($this->getBaseAlias(), ...collect($this->eagerAssociations)->flatMap( fn($a) => explode('.', $a) )->unique())
            ->from($this->getClassName(), $this->getBaseAlias());

        foreach($this->getJoins() as $join)
            $qb = $qb->leftJoin($join->from . '.' . $join->to, $join->to);

        return $qb;
    }

    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll() : array
    {
        return $this->eagerQB()->getQuery()->getResult();
    }

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     * @return object|null The entity instance or NULL if the entity can not be found.
     * @throws \Doctrine\ORM\ORMException
     */
    public function find($id, $lockMode = null, $lockVersion = null) : ?object
    {
        $qb = $this->eagerQB()
            ->where($this->getBaseAlias() . '.' . $this->getClassMetadata()->getSingleIdentifierFieldName() . ' = :pk')
            ->setParameter('pk', $id);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * Creates an alias for {@see QueryBuilder}
     *
     * @return string
     */
    protected function getBaseAlias() : string
    {
//        return $this->getClassMetadata()->getTableName();
        return 'base';
    }

    protected function resolve($joinTo, $joinFrom = null)
    {
        echo 'join: ' . ($joinFrom ??= $this->getBaseAlias()) . '.' . $joinTo .', ' . $joinTo . PHP_EOL;
    }

    public function getJoins()
    {
        $joins =  collect([]);
        $keys = collect($this->eagerAssociations)->sortBy(fn($v) => substr_count($v, '.'))->values();
        foreach($keys as $key) {
            $prev = $this->getBaseAlias();
            $key = is_array($key) ? $key : explode('.', $key);
            foreach($key as $segment) {
                $joins->push((object) ['from' => $prev, 'to' => $segment]);
                $prev = $segment;
            }
        }
        return $joins->unique(function ($item) {
            return $item->from . $item->to;
        })->values();
    }

    /**
     * Get the type of relationship. In {@see ClassMetadataInfo}, they're stored as const ints.
     * {@internal not in use, just here for debugging or if it is ever needed.}
     *
     * @param $i
     * @return string
     */
    protected function getRelationType($i) : string
    {
        switch ($i) {
            case $this->getClassMetadata()::ONE_TO_ONE:
                return 'ONE_TO_ONE';
            case $this->getClassMetadata()::MANY_TO_ONE:
                return 'MANY_TO_ONE';
            case $this->getClassMetadata()::ONE_TO_MANY:
                return 'ONE_TO_MANY';
            case $this->getClassMetadata()::MANY_TO_MANY:
                return 'MANY_TO_MANY';
        }
    }
}
