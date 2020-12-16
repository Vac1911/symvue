<?php


namespace App\Entity\Traits;

use App\Entity\Traits\Identifiable;

use App\Serializer\Annotation\VirtualProperty;
//use voku\helper\ASCII;

/**
 * Trait Sluggable - adds a virtual slug, with the option to set a custom slug
 * @package App\Traits
 * @author Andrew Mellor <andrew@quasars.com>
 */
trait Sluggable
{
    use Identifiable;

//    protected string $slug;

    /**
     * The field to use store a custom slug. If null, then custom slugs cannot be used.
     * @internal set the property '$customSlugProperty' in the model to use a different field.
     *
     * @return string|null
     */
    protected function getCustomSlugProperty() : ?string
    {
        return $this->customSlugProperty ?? null;
    }

    /**
     * Get this model's slug.
     * @Groups({"virtual"})
     * @VirtualProperty
     * @return string
     */
    public function getSlug() : string
    {

        // If this model has a custom slug: return the custom slug.
        if(!is_null($this->getCustomSlugProperty()) && !empty($this->{$this->getCustomSlugProperty()}))
            return $this->{$this->getCustomSlugProperty()};
        // Otherwise generate a slug then return the generated slug.
        else
            return $this->generate_slug();
    }

    /**
     * Set this model's slug if there is a custom slug field and the new slug is unique.
     * @param $val
     * @Groups({"virtual"})
     * @VirtualProperty
     * @return string
     */
    public function setSlug($val)
    {
        // if there is no custom slug field, you can't set a slug
        if(is_null($this->getCustomSlugProperty()))
            return false;

        // Encode whatever we are attempting to make slug
        $nextSlug = $this->encode_slug($val);

        // Set custom slug prop unless $nextSlug is the same as what would be the generated slug
        if($nextSlug != $this->getSlug())
            $this->{$this->getCustomSlugProperty()} = $nextSlug;
        else
            $this->{$this->getCustomSlugProperty()} = null;
    }

    /**
     * alias of self::getSlug
     *
     * @return string|null
     */
    public function slug(): ?string
    {
        return $this->getSlug();
    }

    /**
     * Generate a URL friendly slug from this label.
     *
     * @return string
     */
    protected function generate_slug() : string
    {
        return $this->encode_slug($this->getLabel());
    }

    /**
     * Generate a URL friendly slug from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * @param  string  $language
     * @return string
     */
    protected function encode_slug($title, $separator = '-', $language = 'en') : string
    {
//        $title = ASCII::to_ascii((string) $title, $language);

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';
        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Replace @ with the word 'at'
        $title = str_replace('@', $separator.'at'.$separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }
}
