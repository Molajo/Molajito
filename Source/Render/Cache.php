<?php
/**
 * Cache Processing for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Render\EscapeInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\RenderInterface;

/**
 * Cache Processing for Molajito
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Cache extends AbstractRenderer
{
    /**
     * Cache Trait
     *
     * @var     object  CommonApi\Cache\CacheTrait
     * @since   1.0.0
     */
    use \CommonApi\Cache\CacheTrait;

    /**
     * Constructor
     *
     * @param  EscapeInterface $escape_instance
     * @param  RenderInterface $render_instance
     * @param  EventInterface  $event_instance
     * @param  Object          $runtime_data
     * @param  callable        $get_cache_callback
     * @param  callable        $set_cache_callback
     * @param  callable        $delete_cache_callback
     *
     * @since  1.0.0
     */
    public function __construct(
        EscapeInterface $escape_instance,
        $render_instance,
        EventInterface $event_instance,
        $runtime_data,
        $get_cache_callback = null,
        $set_cache_callback = null,
        $delete_cache_callback = null
    ) {
        $this->get_cache_callback    = $get_cache_callback;
        $this->set_cache_callback    = $set_cache_callback;
        $this->delete_cache_callback = $delete_cache_callback;
        $this->cache_type            = 'Cacheview';

        parent::__construct(
            $escape_instance,
            $render_instance,
            $event_instance,
            $runtime_data
        );
    }

    /**
     * Get Cache Item if it is to be used for Model Registry and if it exists
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getViewCache()
    {
        $this->rendered_view = '';

        if ($this->checkCacheService() === false) {
            return $this;
        }

        $key        = $this->setCacheKey();
        $cache_item = $this->getCache($key);

        if ($cache_item->isHit() === true) {
            $this->rendered_view = $cache_item->getValue();
        }

        return $this;
    }

    /**
     * Get Cache Item if it is to be used for Model Registry and if it exists
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function checkCacheService()
    {
        if (isset($this->plugin_data->render->extension->parameters->cache_service)) {
        } else {
            return false;
        }

        return (bool)$this->plugin_data->render->extension->parameters->cache_service;
    }

    /**
     * Set Cache if it is to be used for Model Registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setViewCache()
    {
        if ($this->checkCacheService() === false) {
            return $this;
        }

        $key = $this->setCacheKey();

        $this->setCache($key, $this->rendered_view);

        return $this;
    }

    /**
     * Get Cache Item if it is to be used for Model Registry and if it exists
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function useViewCache()
    {
        if (isset($this->model_registry['cache_off'])
            && $this->model_registry['cache_off'] === true
        ) {
            unset($this->model_registry['cache_off']);

            return false;
        }

        return $this->useCache();
    }

    /**
     * Delete Cache for a specific item or all of this type
     *
     * @param   string $key
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function deleteViewCache($key = null)
    {
        if ($key === null) {
            return $this->clearCache();
        }

        $this->deleteCache(md5($key));

        return $this;
    }

    /**
     * Set View Cache Key
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setCacheKey()
    {
        $key = md5(
            'Molajito-TemplateView-' . serialize($this->parameters->token)
        );

        return $key;
    }
}
