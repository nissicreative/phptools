<?php
namespace Nissi\Traits;

trait HasAccessLevel
{
    /**
     * List of roles a user might possess.
     *
     * @return array
     */
    public function getRoles()
    {
        return [
            0  => 'Guest',
            10 => 'User',
            20 => 'Contributor',
            30 => 'Editor',
            40 => 'Manager',
            50 => 'Web Administrator',
            60 => 'System Administrator',
        ];
    }

    /**
     * Only the roles that are less than or equal to the authenticated user.
     */
    public function getEditableRoles()
    {
        if (auth()->guest()) {
            return [];
        }

        return collect($this->getRoles())
            ->filter(function ($role, $level) {
                return $level <= auth()->user()->access_level;
            });
    }

    /**
     * The name of the attribute which defines a user's "access level".
     *
     * @return string
     */
    public function getLevelAttributeName()
    {
        return 'access_level';
    }

    /**
     * The minimum level to access the admin panel.
     *
     * @return int
     */
    public function getAdminLevel()
    {
        return 40;
    }

    /**
     * The minimum level to be considered a system administrator.
     *
     * @return int
     */
    public function getSysAdminLevel()
    {
        return 60;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
     */

    /**
     * Admin users (i.e. can log into panel).
     */
    public function scopeAdmins($query)
    {
        return $query->where('access_level', '>=', $this->getAdminLevel());
    }

    /**
     * System admin users.
     */
    public function scopeSysadmins($query)
    {
        return $query->where('access_level', '>=', $this->getSysAdminLevel());
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
        $roles = $this->getRoles();
        $prop  = $this->getLevelAttributeName();

        $userLevel = $this->$prop;

        foreach ($roles as $level => $roleName) {
            if ($level == $userLevel) {
                return $roles[$level];
            }
        }

        return $onErr;
    }

    /**
     * Accessor method for outputting a user's role.
     */
    public function getRoleAttribute()
    {
        return $this->roleName();
    }
}
