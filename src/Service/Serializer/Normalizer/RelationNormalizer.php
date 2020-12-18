<?php

namespace App\Service\Serializer\Normalizer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Serializer;

class RelationNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var string
     */
    protected string $removalToken = '';

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected \Symfony\Component\PropertyAccess\PropertyAccessor $propAccessor;

    public function __construct(EntityManagerInterface $em)
    {

        $this->propAccessor = PropertyAccess::createPropertyAccessor();
        $this->em = $em;
    }

    /**
     * @return string
     */
    public function getRemovalToken(): string
    {
        return $this->removalToken;
    }

    /**
     * @param string $removalToken
     * @return RelationNormalizer
     */
    public function setRemovalToken(string $removalToken): RelationNormalizer
    {
        $this->removalToken = $removalToken;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return ($this->isProxyEntity($data) && !$data->__isInitialized()) || ($this->isProxyCollection($data) && !$data->isInitialized());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return is_array($data) && $this->isEntity(new $type);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return $this->removalToken ?? '__STOP__';
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $relations = $this->getAssociationMappings($type);
        foreach ($relations as $relation) {
            if ($relation['fieldName'] && isset($data[$relation['fieldName']])) {
                $relation['type'] = $this->getRelationType($relation);
                if (!$relation['type']['toMany']) {
                    $relationData = $data[$relation['fieldName']];

                    $childContext = $context;
                    // If this data represents an existing entity
                    try {
                        $this->em->clear($relation['targetEntity']);
                        $prevEntity = $this->em->getRepository($relation['targetEntity'])->find($relationData['id']);
                        $childContext[AbstractNormalizer::OBJECT_TO_POPULATE] = $prevEntity;
                        $nextEntity = $context['serializer']->denormalize($relationData, $relation['targetEntity'], $format, $childContext);
                        $this->em->persist($nextEntity);

                        if($context[AbstractNormalizer::OBJECT_TO_POPULATE] && $this->propAccessor->getValue($context[AbstractNormalizer::OBJECT_TO_POPULATE], $relation['fieldName'])->getId() == $nextEntity->getId())
                            unset($data[$relation['fieldName']]);
                        else
                            $data[$relation['fieldName']] = $nextEntity;
                    }
                    // If this data represents a new entity
                    catch (NoResultException $e) {
                        unset($childContext[AbstractNormalizer::OBJECT_TO_POPULATE]);
                        $nextEntity = $context['serializer']->denormalize($relationData, $relation['targetEntity'], $format, $childContext);
                        $this->em->persist($nextEntity);
                        $data[$relation['fieldName']] = $nextEntity;
                    }


                }
            }
        }
//
//        if(is_array($data))
//        if (array_key_exists('author', $data)) {
//            $author = $this->makeSerializer()->denormalize($data['author'], User::class, $format, $context);
//        }

        return $data;
    }

    protected function isProxyCollection($var): bool
    {
        if (!is_object($var))
            return false;
        return strpos(\get_class($var), 'PersistentCollection') !== false;
    }

    protected function isProxyEntity($var): bool
    {
        if (!is_object($var))
            return false;
        return strpos(\get_class($var), 'Proxies') !== false;
    }

    protected function isEntity($var): bool
    {
        if (!is_object($var))
            return false;
        return strpos(\get_class($var), 'Entity') !== false;
    }

    protected function getAssociationMappings($var)
    {
        return $this->em->getClassMetadata($var)->associationMappings;
    }

    /**
     * Get the type of relationship. In {@see ClassMetadataInfo}, they're stored as const ints.
     * {@internal not in use, just here for debugging or if it is ever needed.}
     *
     * @param $relation
     * @return array
     */
    protected function getRelationType(array $relation): array
    {
        switch ($relation['type']) {
            case ClassMetadataInfo::ONE_TO_ONE:
                return ['name' => 'ONE_TO_ONE', 'toMany' => false];
            case ClassMetadataInfo::MANY_TO_ONE:
                return ['name' => 'MANY_TO_ONE', 'toMany' => false];
            case ClassMetadataInfo::ONE_TO_MANY:
                return ['name' => 'ONE_TO_MANY', 'toMany' => true];
            case ClassMetadataInfo::MANY_TO_MANY:
                return ['name' => 'MANY_TO_MANY', 'toMany' => true];
        }
    }
}
