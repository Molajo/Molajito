<?php
/**
 * Molajo View Adapter for Rendering Package
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\View;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Render\ViewInterface;
use stdClass;

/**
 * Molajo View Adapter for Rendering Package
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Molajo extends AbstractAdapter implements ViewInterface
{
    /**
     * Resource
     *
     * @var    object
     * @since  1.0.0
     */
    protected $resource = NULL;

    /**
     * Constructor
     *
     * @param  object $resource
     *
     * @since  1.0.0
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get View required for Rendering
     *
     * @param   object $token
     *
     * @return  stdClass
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getView($token)
    {
        $render         = new stdClass();
        $render->token  = $token;
        $scheme         = ucfirst(strtolower($token->type));
        $render->scheme = strtolower($scheme);

        if ($scheme == 'Page') {
            $protocol_location = 'Page:///Molajo//Views//Pages//';

        } elseif ($scheme == 'Template') {
            $protocol_location = 'Template:///Molajo//Views//Templates//';

        } elseif ($scheme == 'Wrap') {
            $protocol_location = 'Wrap:///Molajo//Views//Wraps//';

        } elseif ($scheme == 'Theme') {
            $protocol_location = 'Theme:///Molajo//Themes//';

        } else {
            throw new RuntimeException ('Molajo View Adapter: getExtension Invalid Scheme: ' . $scheme);
        }

        try {
            $render->extension = $this->resource->get(
                $protocol_location
                . ucfirst(strtolower($token->name))
            );

        } catch (Exception $e) {
            throw new RuntimeException('Molajito View Molajo Adapter Failed: '
                . $protocol_location . ucfirst(strtolower($token->name))
                . ' Message: ' . $e->getMessage());
        }

        return $render;
    }
}
