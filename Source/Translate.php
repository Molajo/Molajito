<?php
/**
 * Proxy Class for Molajito Translate Adapters
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajito;

use CommonApi\Language\TranslateInterface;
use CommonApi\Exception\RuntimeException;
use Exception;

/**
 * Proxy Class for Molajito Translate Adapters
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Translate implements TranslateInterface
{
    /**
     * Translate Adapter
     *
     * @var     object  CommonApi\Language\TranslateInterface
     * @since  1.0.0
     */
    protected $translate_adapter = NULL;

    /**
     * Class Constructor
     *
     * @param   TranslateInterface $translate_adapter
     *
     * @since   1.0
     */
    public function __construct(
        TranslateInterface $translate_adapter
    ) {
        $this->translate_adapter = $translate_adapter;
    }

    /**
     * Translate String
     *
     * @param   $string
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function translate($string)
    {
        try {
            return $this->translate_adapter->translate($string);

        } catch (Exception $e) {

            throw new RuntimeException
            (
                'Molajito Translate: Failed for String: ' . $string . ' ' . $e->getMessage()
            );
        }
    }
}
