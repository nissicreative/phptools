<?php
namespace Nissi\Traits;

use Nissi\ValueObjects\Gravatar;

trait HasAvatar
{
    /*
     * Defer to Gravatar image if not overridden.
     */
    public function avatarSrc($size = 40, $default = null)
    {
        return $this->gravatarSrc($size, $default);
    }

    /*
     * Return a Gravatar image src.
     */
    public function gravatarSrc($size = 40, $default = null)
    {
        $gravatar = new Gravatar();
        $gravatar->setEmail($this->email);
        $gravatar->setSize($size);
        $gravatar->setDefault($default);
        return $gravatar->getSrc(null, $size);
    }
}
