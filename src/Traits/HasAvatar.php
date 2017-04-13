<?php

namespace Nissi\Traits;

use Nissi\ValueObjects\Gravatar;

trait HasAvatar
{
    /**
     * Defer to Gravatar image if not overridden.
     */
    public function avatarSrc($size = 40, $default = null)
    {
        return $this->gravatarSrc($size, $default);
    }

    /**
     * Return a Gravatar image src.
     */
    public function gravatarSrc($size = 40, $default = null)
    {
        $gravatar = new Gravatar();

        $gravatar->setEmail($this->email);
        $gravatar->setSize($size);
        $gravatar->setDefault($default ?? $this->getDefaultAvatar());

        return $gravatar->getSrc(null, $size);
    }

    /**
     * Return URL to default avatar if it exists. NULL otherwise.
     */
    public function getDefaultAvatar()
    {
        return $this->defaultAvatar ? url($this->defaultAvatar) : null;
    }

}
