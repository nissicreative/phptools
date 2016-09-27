<?php
namespace Nissi\ValueObjects;

use Nissi\Proxies\Filter;

class Gravatar extends AbstractValueObject
{
    protected $email;
    protected $size    = 80;
    protected $default = 'mm';
    protected $rating  = 'g';

    /**
     * Returns `src` attribute for a Gravatar image.
     *
     * @return string
     */
    public function getSrc($email = null, $size = null, $default = null, $rating = null)
    {
        $email = $email ?: $this->email;

        if ( ! Filter::isEmail($email)) {
            $email = 'test@example.com';
        }

        $hash = md5($email);

        $params = [
            's' => $size ?: $this->size,
            'd' => $default ?: $this->default,
            'r' => $rating ?: $this->rating
        ];

        $url = sprintf('//www.gravatar.com/avatar/%s?', $hash);
        $url .= http_build_query($params);

        return $url;
    }
}
