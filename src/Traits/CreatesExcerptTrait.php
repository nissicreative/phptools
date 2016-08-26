<?php
namespace Nissi\Traits;

use Nissi\Proxies\Text;

trait CreatesExcerptTrait
{
    /**
     * Get article or review excerpt. Create one from full text if excerpt is not available
     *
     * @access public
     * @param  int      $length  (default: 300)
     * @param  array    $options (default: [])
     * @return string
     */
    public function getExcerpt($length = 300, array $options = [])
    {
        $defaults = [
            'force_length'       => false,     // Set true to force-truncate a pre-defined excerpt
            'ellipsis'           => '…',       // Text to indicate truncated content
            'excerpt_attribute'  => 'excerpt', // Name of pre-defined excerpt attribute
            'fulltext_attribute' => 'content', // Name of full-text attribute
            'allowable_tags'     => ''         // i.e. "<strong><em>"
        ];

        $options = array_merge($defaults, $options);
        extract($options); // Array keys to local variables

        // Use pre-defined excerpt text if available
        $text = isset($this->$excerpt_attribute) ? $this->$excerpt_attribute : $this->$fulltext_attribute;

        // Be careful allowing tags; content may be truncated before tag is closed!
        $text = strip_tags($text, $allowable_tags);

        // Return full content of pre-defined excerpt unless length is forced
        if ($this->$excerpt_attribute &&  ! $force_length) {
            return $text;
        }

        // Truncate to desired length; append ellipsis if necessary
        $appended = strlen($text) > $length ? $ellipsis : '';

        // Truncate and preserve words
        return Text::truncate($text, $length, ['exact' => false]) . $appended;
    }
}
