<?php

declare(strict_types=1);

namespace App\Service\Serializer\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 *
 * @author Alexander Klimenkov <alx.devel@gmail.com>
 */
final class VirtualProperty
{
    /**
     * @var string
     */
    public $exp;

    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $options = [];

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data['name'] = $data['value'];
            unset($data['value']);
        }

        foreach ($data as $key => $value) {
            if (!property_exists(self::class, $key)) {
                throw new \Exception(sprintf('Unknown property "%s" on annotation "%s".', $key, self::class));
            }

            $this->{$key} = $value;
        }
    }
}
