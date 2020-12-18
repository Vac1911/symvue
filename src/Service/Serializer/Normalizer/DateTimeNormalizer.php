<?php

namespace App\Service\Serializer\Normalizer;


use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer as ParentNormalizer;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * Normalizes an object implementing the {@see \DateTimeInterface} to a date string.
 * Denormalizes a date string to an instance of {@see \DateTime} or {@see \DateTimeImmutable}.
 *
 * @author Andrew Mellor <andrew@quasars.com>
 */
class DateTimeNormalizer extends ParentNormalizer
{
    /**
     * {@inheritdoc}
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $dateTimeFormat = $context[self::FORMAT_KEY] ?? null;
        if (null !== $dateTimeFormat)
            $data = Carbon::createFromFormat($dateTimeFormat, '2020-10-19T14:09:22+05:00');
        else
            $data = Carbon::parse($data);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        if(is_string($data))
            try {
                Carbon::parse($data);
                return true;
            }
            catch(InvalidFormatException $e) {
                return false;
            }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }
}
