<?php

namespace Nissi\Traits;

use Nissi\ValueObjects\Gravatar;

trait HasAvatar
{
    /**
     * Defer to Gravatar image if not overridden.
     */
    public function avatarSrc($size = 40, $default = 'mm')
    {
        return $this->gravatarSrc($size, $default);
    }

    /**
     * Return a Gravatar image src.
     */
    public function gravatarSrc($size = 40, $default = null)
    {
        $gravatar = new Gravatar();

        $gravatar->setEmail($this->email ?? 'test@example.com');
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

    /**
     * The avatar_uri accessor.
     */
    public function getAvatarUriAttribute($val)
    {
        return $this->avatarSrc('256');
    }
}
