<?php

namespace App\Service\Serializer;

use App\Service\Serializer\Normalizer\DateTimeNormalizer;
use App\Service\Serializer\Normalizer\RelationNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EntitySerializer
{
    const REMOVAL_TOKEN = '__STOP__';

    protected RecursiveSerializer $serializer;

    public function __construct(RelationNormalizer $relationNormalizer)
    {
        $encoders = [new JsonEncoder()];
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return self::REMOVAL_TOKEN;
            },
        ];
        $normalizers = [$relationNormalizer->setRemovalToken(self::REMOVAL_TOKEN), new DateTimeNormalizer(), new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];
        $this->serializer = new RecursiveSerializer($normalizers, $encoders);
    }

    public function encode($entity)
    {
        $normalized = $this->serializer->serialize($entity, 'json', ['json_encode_options' => \JSON_FORCE_OBJECT]);
        return json_encode(self::cleanNormalized(json_decode($normalized, true)));
    }

    public function decode($data, $entityClass)
    {
        return $this->serializer->deserialize($data, $entityClass, 'json');
    }

    public function decodeInto($data, $entity)
    {
        return $this->serializer->deserialize($data, get_class($entity), 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $entity]);
    }

    protected static function isAssoc(array $arr)
    {
        return [] === $arr ? false : array_keys($arr) !== range(0, count($arr) - 1);
    }

    protected static function cleanNormalized(array $arr)
    {
        $is_assoc = self::isAssoc($arr);
        foreach($arr as $key => $val) {
            if(is_array($val))
                $arr[$key] = self::cleanNormalized($val);
            if($val === self::REMOVAL_TOKEN)
                unset($arr[$key]);
        }
        if($is_assoc)
            return $arr;
        else
            return array_values($arr);
    }

}
