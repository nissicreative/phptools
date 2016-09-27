<?php
namespace Nissi\Traits;

trait HasAccessLevel
{
    /*
     * List of roles a user might possess.
     */
    protected static $roles = [
        0  => 'Guest',
        10 => 'User',
        20 => 'Contributor',
        30 => 'Editor',
        40 => 'Manager',
        50 => 'Web Administrator',
        60 => 'System Administrator'
    ];

    /**
     * Convenience method to retrieve array of roles.
     */
    public static function getRoles()
    {
        return static::$roles;
    }

    /*
     * The minimum level to be considered an administrator.
     */
    protected $adminLevel = 50;

    /**
     * Convenience method to retrieve "admin" level.
     */
    public function getAdminLevel()
    {
        return $this->adminLevel;
    }

    /*
     * The minimum level to be considered a system administrator.
     */
    protected $sysAdminLevel = 60;

    /**
     * Convenience method to retrieve "sysadmin" level.
     */
    public function getSysAdminLevel()
    {
        return $this->sysAdminLevel;
    }

    /*
     * The name of the attribute which defines a user's "access level".
     */
    protected $levelAttribute = 'access_level';

    /*
     * Convenience method to retrieve access level attribute.
     */
    public static function getLevelAttributeName()
    {
        return $this->levelAttribute;
    }

    /*
    |--------------------------------------------------------------------------
    | Public Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Is user an administrator?
     *
     * @return bool
     */
    public function isAdmin()
    {
        $prop = $this->getLevelAttributeName();

        return $this->$prop >= $this->getAdminLevel();
    }

    /**
     * Is user a system administrator?
     *
     * @return bool
     */
    public function isSysAdmin()
    {
        $prop = $this->getLevelAttributeName();

        return $this->$prop >= $this->getSysAdminLevel();
    }

    /**
     * Name of user's "role"
     *
     * @param  $onErr   default 'N/A'
     * @return string
     */
    public function roleName($onErr = 'N/A')
    {
        $roles = static::getRoles();
        $prop  = $this->getLevelAttributeName();

        $userLevel = $this->$prop;

        foreach ($roles as $level => $roleName) {
            if ($level == $userLevel) {
                return $roles[$level];
            }
        }

        return $onErr;
    }
}
