<?php
/**
 * Data Resource Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\DataResource;
use stdClass;

/**
 * Data Resource Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class DataResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Object
     */
    protected $data_resource;

    /**
     * @var Object
     */
    protected $keys = array(
        'model_type',
        'model_name',
        'field_name',
        'query_results',
        'model_registry',
        'parameters'
    );

    /**
     * Initialises Adapter
     */
    protected function setUp()
    {

    }

    /**
     * @test    - Model Type and Name from Token
     *
     * @param   array $options
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

        $token                           = new stdClass();
        $token->attributes               = array();
        $token->attributes['model_type'] = 'runtime_data';
        $token->attributes['model_name'] = 'application';

        $model_registry = new stdClass();

        /** Instantiate Data Resource */
        $this->data_resource = new DataResource(
            $runtime_data,
            $token
        );

        /** Get Data */
        $data = $this->data_resource->getData();

        /** Results */
        $this->assertEquals('runtime_data', $data->parameters->model_type);
        $this->assertEquals('application', $data->parameters->model_name);
        $this->assertEquals($data->query_results, array($runtime_data->application));
        $this->assertEquals($data->model_registry, $model_registry);
        $this->assertEquals($data->parameters->token, $token);

        return $this;
    }


    /**
     * @test    - Model Type and Name from Token
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetDataModel()
    {
        /** Input */
        $runtime_data                 = new stdClass();
        $runtime_data->plugin_data    = new stdClass();
        $runtime_data->plugin_data->b = 'b';

        $token                           = new stdClass();
        $token->attributes               = array();
        $token->attributes['model_type'] = 'plugin_data';
        $token->attributes['model_name'] = 'B';
        $token->attributes['field_name'] = 'C';

        $runtime_data->render                        = new stdClass();
        $runtime_data->render->extension             = new stdClass();
        $runtime_data->render->extension->parameters = new stdClass();

        /** Instantiate Data Resource */
        $this->data_resource = new DataResource(
            $runtime_data,
            $token
        );

        /** Get Data */
        $data = $this->data_resource->getData();

        /** Results */
        $this->assertEquals($data->parameters->model_type, 'plugin_data');
        $this->assertEquals($data->parameters->model_name, 'b');
        $this->assertEquals($data->parameters->field_name, 'c');
        $this->assertEquals($data->query_results, array($runtime_data->plugin_data->b));
        $this->assertEquals($data->model_registry, array());
        $this->assertEquals($data->parameters->token, $token);

        return $this;
    }

    /**
     * @test    - Model Type and Name from Token
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetPluginData()
    {
        /** Input */
        $runtime_data              = new stdClass();
        $runtime_data->plugin_data = new stdClass();
        $collection                = new stdClass();
        $collection->field_a       = 'a';
        $collection->field_b       = 'b';
        $collection->field_c       = 'c';
        $row                       = array();
        $row[]                     = $collection;

        $model_registry          = new stdClass();
        $model_registry->field_a = 'a1';
        $model_registry->field_b = 'b1';
        $model_registry->field_c = 'c1';

        $runtime_data->plugin_data->collection                 = new stdClass();
        $runtime_data->plugin_data->collection->data           = $row;
        $runtime_data->plugin_data->collection->model_registry = $model_registry;
        $runtime_data->plugin_data->collection->parameters     = new stdClass();
        $runtime_data->plugin_data->collection->parameters->a  = 'a';

        $runtime_data->render                        = new stdClass();
        $runtime_data->render->extension             = new stdClass();
        $runtime_data->render->extension->parameters = new stdClass();

        $token                           = new stdClass();
        $token->attributes               = array();
        $token->attributes['model_type'] = 'plugin_data';
        $token->attributes['model_name'] = 'collection';

        /** Instantiate Data Resource */
        $this->data_resource = new DataResource(
            $runtime_data,
            $token
        );

        /** Get Data */
        $data = $this->data_resource->getData();

        /** Results */
        $this->assertEquals($data->parameters->model_type, 'plugin_data');
        $this->assertEquals($data->parameters->model_name, 'collection');
        $this->assertEquals($data->query_results, $row);
        $this->assertEquals($data->model_registry, $model_registry);
        $this->assertEquals($data->parameters->token, $token);

        return $this;
    }


    /**
     * @test    - Model Type and Name from Token
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetPluginDataField()
    {
        /** Input */
        $runtime_data              = new stdClass();
        $runtime_data->plugin_data = new stdClass();

        $collection          = new stdClass();
        $field_c             = new stdClass();
        $field_c->field_a    = 'a';
        $field_c->field_b    = 'b';
        $field_c->field_c    = 'c';
        $collection->field_c = $field_c;
        $row                 = array();
        $row[]               = $collection;

        $model_registry          = new stdClass();
        $model_registry->field_a = 'a1';
        $model_registry->field_b = 'b1';
        $model_registry->field_c = 'c1';

        $runtime_data->plugin_data->collection                 = new stdClass();
        $runtime_data->plugin_data->collection->data           = $row;
        $runtime_data->plugin_data->collection->model_registry = $model_registry;
        $runtime_data->plugin_data->collection->parameters     = new stdClass();
        $runtime_data->plugin_data->collection->parameters->a  = 'a';

        $runtime_data->render                        = new stdClass();
        $runtime_data->render->extension             = new stdClass();
        $runtime_data->render->extension->parameters = new stdClass();

        $token                           = new stdClass();
        $token->attributes               = array();
        $token->attributes['model_type'] = 'plugin_data';
        $token->attributes['model_name'] = 'collection';
        $token->attributes['field_name'] = 'field_c';

        /** Instantiate Data Resource */
        $this->data_resource = new DataResource(
            $runtime_data,
            $token
        );

        /** Get Data */
        $data = $this->data_resource->getData();

        /** Results */
        $this->assertEquals($data->parameters->model_type, 'plugin_data');
        $this->assertEquals($data->parameters->model_name, 'collection');
        $this->assertEquals($data->query_results[0]->field_c, $field_c);
        $this->assertEquals($data->model_registry, $model_registry);
        $this->assertEquals($data->parameters->token, $token);

        return $this;
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
}
