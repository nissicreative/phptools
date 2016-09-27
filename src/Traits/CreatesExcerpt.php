<?php
namespace Nissi\Traits;

use Nissi\Proxies\Text;

trait CreatesExcerpt
{
    /**
     * The attribute containing the pre-defined excerpt.
     *
     * @var string
     */
    protected $excerptAttribute = 'excerpt';

    /**
     * The attribute containing the fulltext content.
     *
     * @var string
     */
    protected $fulltextAttribute = 'body';

    /**
     * Get article or review excerpt. Create one from full text if excerpt is not available.
     *
     * @access public
     * @param  int      $length  (default: 300)
     * @param  array    $options (default: [])
     * @return string
     */
    public function getExcerpt($length = 300, array $options = [])
    {
        $defaults = [
            // Text to indicate truncated content
            'ellipsis'       => '…',
            // Set true to force-truncate a pre-defined excerpt
            'force_length'   => false,
            // i.e. "<strong><em>"
            'allowable_tags' => '',
            // Set true to use fulltext attribute even if there is
            // already a pre-defined excerpt
            'use_fulltext'   => false
        ];

        $options += $defaults;
        extract($options);

        $excerptAttribute  = $this->getExcerptAttributeName();
        $fulltextAttribute = $this->getFulltextAttributeName();

        // Use pre-defined excerpt text if available, unless the
        // use_fulltext directive is true
        $text = ( ! empty($this->$excerptAttribute) &&  ! $use_fulltext)
            ? $this->$excerptAttribute
            : $this->$fulltextAttribute;

        // Be careful allowing tags; content may be truncated before tag is closed!
        $text = strip_tags($text, $allowable_tags);

        // Return full content of pre-defined excerpt unless length is forced
        if ( ! empty($this->$excerptAttribute)
            &&  ! $use_fulltext
            &&  ! $force_length) {
            return $text;
        }

        // Truncate to desired length; append ellipsis if necessary
        $appended = strlen($text) > $length ? $ellipsis : '';

        // Truncate and preserve words
        return Text::truncate($text, $length, ['exact' => false]) . $appended;
    }

    /**
     * The name of the attribute containing the pre-defined excerpt.
     *
     * @return string
     */
    protected function getExcerptAttributeName()
    {
        return $this->excerptAttribute;
    }

    /**
     * The name of the attribute containing the full text content.
     *
     * @return string
     */
    protected function getFulltextAttributeName()
    {
        return $this->fulltextAttribute;
    }
}
