<?php


namespace App\Entity\Traits;

use Doctrine\ORM\PersistentCollection;

trait Proxiable
{
    public function isLoaded($key)
    {
        if ($this->$key instanceof PersistentCollection) {
            return $this->$key->isInitialized();
        }

        // else collection instance of ArrayCollection and already populated.
        return true;
    }
}
