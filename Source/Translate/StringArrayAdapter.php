<?php
/**
 * String Array Adapter {T Array This T}
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajito\Translate;

use CommonApi\Language\TranslateInterface;
use CommonApi\Render\EscapeInterface;

/**
 * String Array Adapter {T Array This T}
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class StringArrayAdapter extends AbstractAdapter implements TranslateInterface
{
    /**
     * Language Strings
     *
     * @var    array
     * @since  1.0.0
     */
    protected $language_strings = array();

    /**
     * Class Constructor
     *
     * @param  EscapeInterface $escape_instance
     * @param  null|string     $parse_mask
     * @param  array           $model_registry
     * @param  array           $language_strings
     *
     * @since  1.0.0
     */
    public function __construct(
        EscapeInterface $escape_instance,
        $parse_mask = null,
        array $model_registry = array(),
        array $language_strings = array()
    ) {
        parent::__construct($escape_instance, $parse_mask, $model_registry);

        $this->language_strings = $language_strings;
    }

    /**
     * Translate Token by Locating Array Value
     *
     * @param   string $string
     *
     * @return  string
     * @since   1.0
     */
    protected function translateToken($string)
    {
        if (isset($this->language_strings[ $string ])) {
            return $this->language_strings[ $string ];
        }

        return $string;
    }
}
