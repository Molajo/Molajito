<?php
/**
 * Data Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Data;
use Molajito\Data\Molajo;
use stdClass;

/**
 * Data Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $data_resource
     */
    protected $data_resource;

    /**
     * @var $keys
     */
    protected $keys
        = array(
            'model_type',
            'model_name',
            'field_name',
            'query_results',
            'model_registry',
            'parameters'
        );

    /**
     * Create Data Instance
     */
    protected function setUp()
    {
        $this->data_resource = new Data(new Molajo());
    }

    /**
     * @covers Molajito\Data::__construct
     * @covers Molajito\Data::getData
     * @covers Molajito\Data::editOptions
     * @covers Molajito\Data::editToken
     * @covers Molajito\Data\Molajo::__construct
     * @covers Molajito\Data\Molajo::getData
     * @covers Molajito\Data\Molajo::initialise
     * @covers Molajito\Data\Molajo::setModel
     * @covers Molajito\Data\Molajo::setModelType
     * @covers Molajito\Data\Molajo::setModelName
     * @covers Molajito\Data\Molajo::setFieldName
     * @covers Molajito\Data\Molajo::getPrimaryData
     * @covers Molajito\Data\Molajo::getRuntimeData
     * @covers Molajito\Data\Molajo::getPluginData
     * @covers Molajito\Data\Molajo::setParameters
     * @covers Molajito\Data\Molajo::getDefaultData
     * @covers Molajito\Data\Molajo::setDataResults
     * @covers Molajito\Data\AbstractAdapter::__construct
     * @covers Molajito\Data\AbstractAdapter::setParametersFromToken
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetRuntimeData()
    {
        /** Input */
        $runtime_data                                = new stdClass();
        $runtime_data->application                   = new stdClass();
        $runtime_data->application->field_a          = 'a';
        $runtime_data->application->field_b          = 'b';
        $runtime_data->application->field_c          = 'c';
        $runtime_data->render                        = new stdClass();
        $runtime_data->render->extension             = new stdClass();
        $runtime_data->render->extension->parameters = array();

        $options                 = array();
        $options['runtime_data'] = $runtime_data;

        $token                           = new stdClass();
        $token->attributes               = array();
        $token->attributes['model_type'] = 'runtime_data';
        $token->attributes['model_name'] = 'application';

        $model_registry = new stdClass();

        /** Get Data */
        $data = $this->data_resource->getData($token, $options);

        /** Results */
        $this->assertEquals('runtime_data', $data->parameters->model_type);
        $this->assertEquals('application', $data->parameters->model_name);
        $this->assertEquals($data->query_results, array($runtime_data->application));
        $this->assertEquals($data->model_registry, $model_registry);
        $this->assertEquals($data->parameters->token, $token);

        return $this;
    }

    /**
     * @covers Molajito\Data::__construct
     * @covers Molajito\Data::getData
     * @covers Molajito\Data::editOptions
     * @covers Molajito\Data::editToken
     * @covers Molajito\Data\Molajo::__construct
     * @covers Molajito\Data\Molajo::getData
     * @covers Molajito\Data\Molajo::initialise
     * @covers Molajito\Data\Molajo::setModel
     * @covers Molajito\Data\Molajo::setModelType
     * @covers Molajito\Data\Molajo::setModelName
     * @covers Molajito\Data\Molajo::setFieldName
     * @covers Molajito\Data\Molajo::getPrimaryData
     * @covers Molajito\Data\Molajo::getRuntimeData
     * @covers Molajito\Data\Molajo::getPluginData
     * @covers Molajito\Data\Molajo::setParameters
     * @covers Molajito\Data\Molajo::getDefaultData
     * @covers Molajito\Data\Molajo::setDataResults
     * @covers Molajito\Data\AbstractAdapter::__construct
     * @covers Molajito\Data\AbstractAdapter::setParametersFromToken
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetDataModel()
    {
        /** Input */
        $runtime_data   = new stdClass();
        $plugin_data    = new stdClass();
        $plugin_data->b = 'b';

        $runtime_data->render                        = new stdClass();
        $runtime_data->render->extension             = new stdClass();
        $runtime_data->render->extension->parameters = new stdClass();

        $options                 = array();
        $options['runtime_data'] = $runtime_data;
        $options['plugin_data']  = $plugin_data;

        $token                           = new stdClass();
        $token->attributes               = array();
        $token->attributes['model_type'] = 'plugin_data';
        $token->attributes['model_name'] = 'B';
        $token->attributes['field_name'] = 'C';

        /** Get Data */
        $data = $this->data_resource->getData($token, $options);

        /** Results */
        $this->assertEquals($data->parameters->model_type, 'plugin_data');
        $this->assertEquals($data->parameters->model_name, 'B');
        $this->assertEquals($data->parameters->field_name, 'C');
        $this->assertEquals($data->query_results, array($plugin_data->b));
        $this->assertEquals($data->model_registry, array());
        $this->assertEquals($data->parameters->token, $token);

        return $this;
    }

    /**
     * @covers Molajito\Data::__construct
     * @covers Molajito\Data::getData
     * @covers Molajito\Data::editOptions
     * @covers Molajito\Data::editToken
     * @covers Molajito\Data\Molajo::__construct
     * @covers Molajito\Data\Molajo::getData
     * @covers Molajito\Data\Molajo::initialise
     * @covers Molajito\Data\Molajo::setModel
     * @covers Molajito\Data\Molajo::setModelType
     * @covers Molajito\Data\Molajo::setModelName
     * @covers Molajito\Data\Molajo::setFieldName
     * @covers Molajito\Data\Molajo::getPrimaryData
     * @covers Molajito\Data\Molajo::getRuntimeData
     * @covers Molajito\Data\Molajo::getPluginData
     * @covers Molajito\Data\Molajo::setParameters
     * @covers Molajito\Data\Molajo::getDefaultData
     * @covers Molajito\Data\Molajo::setDataResults
     * @covers Molajito\Data\AbstractAdapter::__construct
     * @covers Molajito\Data\AbstractAdapter::setParametersFromToken
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetPluginData()
    {
        /** Input */
        $runtime_data    = new stdClass();
        $plugin_data     = new stdClass();
        $row             = new stdClass();
        $row->field_a    = 'a';
        $row->field_b    = 'b';
        $row->field_c    = 'c';
        $query_results   = array();
        $query_results[] = $row;

        $model_registry          = new stdClass();
        $model_registry->field_a = 'a1';
        $model_registry->field_b = 'b1';
        $model_registry->field_c = 'c1';

        $plugin_data->collection                 = new stdClass();
        $plugin_data->collection->data           = $query_results;
        $plugin_data->collection->model_registry = $model_registry;
        $plugin_data->collection->parameters     = new stdClass();
        $plugin_data->collection->parameters->a  = 'a';

        $runtime_data->render                        = new stdClass();
        $runtime_data->render->extension             = new stdClass();
        $runtime_data->render->extension->parameters = new stdClass();

        $options                  = array();
        $options['query_results'] = $query_results;
        $options['runtime_data']  = $runtime_data;
        $options['plugin_data']   = $plugin_data;

        $token                           = new stdClass();
        $token->attributes               = array();
        $token->attributes['model_type'] = 'plugin_data';
        $token->attributes['model_name'] = 'collection';

        /** Get Data */
        $data = $this->data_resource->getData($token, $options);

        /** Results */
        $this->assertEquals($data->parameters->model_type, 'plugin_data');
        $this->assertEquals($data->parameters->model_name, 'collection');
        $this->assertEquals($data->query_results, $query_results);
        $this->assertEquals($data->model_registry, $model_registry);
        $this->assertEquals($data->parameters->token, $token);

        return $this;
    }

    /**
     * @covers Molajito\Data::__construct
     * @covers Molajito\Data::getData
     * @covers Molajito\Data::editOptions
     * @covers Molajito\Data::editToken
     * @covers Molajito\Data\Molajo::__construct
     * @covers Molajito\Data\Molajo::getData
     * @covers Molajito\Data\Molajo::initialise
     * @covers Molajito\Data\Molajo::setModel
     * @covers Molajito\Data\Molajo::setModelType
     * @covers Molajito\Data\Molajo::setModelName
     * @covers Molajito\Data\Molajo::setFieldName
     * @covers Molajito\Data\Molajo::getPrimaryData
     * @covers Molajito\Data\Molajo::getRuntimeData
     * @covers Molajito\Data\Molajo::getPluginData
     * @covers Molajito\Data\Molajo::setParameters
     * @covers Molajito\Data\Molajo::getDefaultData
     * @covers Molajito\Data\Molajo::setDataResults
     * @covers Molajito\Data\AbstractAdapter::__construct
     * @covers Molajito\Data\AbstractAdapter::setParametersFromToken
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetPluginDataField()
    {
        /** Input */
        $runtime_data = new stdClass();
        $plugin_data  = new stdClass();

        $row              = new stdClass();
        $field_c          = new stdClass();
        $field_c->field_a = 'a';
        $field_c->field_b = 'b';
        $field_c->field_c = 'c';
        $row->field_c     = $field_c;
        $query_results    = array();
        $query_results[]  = $row;

        $model_registry          = new stdClass();
        $model_registry->field_a = 'a1';
        $model_registry->field_b = 'b1';
        $model_registry->field_c = 'c1';

        $plugin_data->collection                 = new stdClass();
        $plugin_data->collection->data           = $query_results;
        $plugin_data->collection->model_registry = $model_registry;
        $plugin_data->collection->parameters     = new stdClass();
        $plugin_data->collection->parameters->a  = 'a';

        $runtime_data->render                        = new stdClass();
        $runtime_data->render->extension             = new stdClass();
        $runtime_data->render->extension->parameters = new stdClass();

        $options                  = array();
        $options['query_results'] = $query_results;
        $options['runtime_data']  = $runtime_data;
        $options['plugin_data']   = $plugin_data;

        $token                           = new stdClass();
        $token->attributes               = array();
        $token->attributes['model_type'] = 'plugin_data';
        $token->attributes['model_name'] = 'collection';
        $token->attributes['field_name'] = 'field_c';

        /** Get Data */
        $data = $this->data_resource->getData($token, $options);

        /** Results */
        $this->assertEquals($data->parameters->model_type, 'plugin_data');
        $this->assertEquals($data->parameters->model_name, 'collection');
        $this->assertEquals($data->query_results[0]->field_c, $field_c);
        $this->assertEquals($data->model_registry, $model_registry);
        $this->assertEquals($data->parameters->token, $token);

        return $this;
    }
}
