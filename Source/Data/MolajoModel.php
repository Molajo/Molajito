<?php
/**
 * Molajo Model Type, Name, and Field for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Data;

/**
 * Molajo Model Type, Name, and Field for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class MolajoModel
{
    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $runtime_data = null;

    /**
     * Plugin Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $plugin_data = null;

    /**
     * Token
     *
     * @var    object
     * @since  1.0.0
     */
    protected $token = null;

    /**
     * Model Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_type = '';

    /**
     * Model Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_name = '';

    /**
     * Field Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $field_name = '';

    /**
     * Class Constructor
     *
     * @param  object $token
     * @param  object $runtime_data
     * @param  object $plugin_data
     *
     * @since  1.0.0
     */
    public function __construct(
        $token,
        $runtime_data,
        $plugin_data
    ) {
        $this->token        = $token;
        $this->runtime_data = $runtime_data;
        $this->plugin_data  = $plugin_data;
    }

    /**
     * Set Model Type, Model Name and Field Name values used for data retrieval
     *
     * @return  array
     * @since   1.0
     */
    public function setModel()
    {
        $this->setModelType();

        $this->setModelName();

        if ($this->model_type === 'default' && $this->model_name === 'default') {
            $this->setDefaultModelTypeName();
        }

        $this->setFieldName();

        return array(
            'model_type' => $this->model_type,
            'model_name' => $this->model_name,
            'field_name' => $this->field_name
        );
    }

    /**
     * Set Model Type
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModelType()
    {
        $model_type = $this->setModelTypeToken();

        if ($model_type === '') {
            $model_type = $this->setModelTypeParameters(
                'runtime_data->render->extension->menuitem->parameters->model_type'
            );
        }

        if ($model_type === '') {
            $model_type = $this->setModelTypeParameters(
                'runtime_data->render->extension->parameters->model_type'
            );

        }

        if ($model_type === '') {
            $model_type = 'default';
        }

        $this->model_type = strtolower($model_type);

        return $this;
    }

    /**
     * Set Model Type using Token
     *
     * @return  string
     * @since   1.0
     */
    protected function setModelTypeToken()
    {
        if (isset($this->token->attributes['model_type'])) {
            return $this->token->attributes['model_type'];
        }

        return '';
    }

    /**
     * Set Model Type using Parameters
     *
     * @return  string
     * @since   1.0
     */
    protected function setModelTypeParameters($location)
    {
        if (isset($this->$location)) {
            return $this->$location;
        }

        return '';
    }

    /**
     * Set Model Name
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelName()
    {
        $name = '';

        if (isset($this->token->attributes['model_name'])) {
            $name = $this->setModelNameToken();
        }

        if ($name === '') {
            $name = $this->setModelExtensionParameters();
        }

        if ($name === '') {
            $name = 'default';
        }

        $this->model_name = strtolower($name);

        return $this;
    }

    /**
     * Set Model Name using Token
     *
     * @return  string
     * @since   1.0
     */
    protected function setModelNameToken()
    {
        $name = strtolower($this->token->attributes['model_name']);

        if ($this->model_type == 'runtime_data' && isset($this->runtime_data->$name)) {
            return $name;
        }

        if (isset($this->plugin_data->$name)) {
            $this->model_type = 'plugin_data';
            return $name;
        }

        return '';
    }

    /**
     * Set Model Name using Extension Parameters
     *
     * @return  string
     * @since   1.0
     */
    protected function setModelExtensionParameters()
    {
        if (isset($this->runtime_data->render->extension->parameters->model_name)) {
            return strtolower($this->runtime_data->render->extension->parameters->model_name);
        }

        return '';
    }

    /**
     * Set Field Name
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setFieldName()
    {
        if (isset($this->token->attributes['field_name'])) {
            $this->field_name = $this->token->attributes['field_name'];
        }

        $this->field_name = strtolower($this->field_name);

        return $this;
    }

    /**
     * Set Model Type
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setDefaultModelTypeName()
    {
        $name = strtolower($this->token->name);

        if (isset($this->plugin_data->$name)) {
            $this->model_type = 'plugin_data';
            $this->model_name = $name;
        }

        return $this;
    }
}
