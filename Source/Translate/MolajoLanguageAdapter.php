<?php
/**
 * Molajo Language Adapter
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajito\Translate;

use CommonApi\Language\TranslateInterface;
use CommonApi\Render\EscapeInterface;

/**
 * Molajo Language Adapter
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class MolajoLanguageAdapter extends AbstractAdapter implements TranslateInterface
{
    /**
     * Language Instance
     *
     * @var    object CommonApi\Language\LanguageInterface
     * @since  1.0
     */
    protected $language_controller;

    /**
     * Class Constructor
     *
     * @param  EscapeInterface         $escape_instance
     * @param  null|string             $parse_mask
     * @param  array                   $model_registry
     * @param  null|TranslateInterface $language_controller
     *
     * @since  1.0.0
     */
    public function __construct(
        EscapeInterface $escape_instance,
        $parse_mask = null,
        array $model_registry = array(),
        TranslateInterface $language_controller = null
    ) {
        $this->language_controller = $language_controller;

        parent::__construct($escape_instance, $parse_mask, $model_registry);
    }

    /**
     * Translate Token by Locating Array Value
     *
     * @param   string $string
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function translateToken($string)
    {
        return $this->language_controller->translate($string);
    }
}
