<?php

abstract class Entity
{
    /**
     * Properties
     *
     * @var    array
     */
    protected $properties = array();

    /**
     * Get class properties
     *
     * @return  $this
     * @since   1.0
     */
    public function getClassProperties()
    {
        foreach ($this->properties as $key) {
            echo $key . ' ' . $this->$key . '<br>';
        }

        return $this;
    }
}

class Cat extends Entity
{
    /**
     * Cry
     *
     * @var    string
     */
    protected $cry = 'meow';

    /**
     * Food
     *
     * @var    string
     */
    protected $food = 'meat';

    /**
     * Properties
     *
     * @var    array
     */
    protected $properties = array('cry', 'food');
}

class Tiger extends Cat
{
    /**
     * Cry
     *
     * @var    string
     */
    protected $cry = 'meow';

    /**
     * Food
     *
     * @var    string
     */
    protected $food = 'meat';

    /**
     * Ability
     *
     * @var    string
     */
    protected $ability = 'pounce';

    /**
     * Properties
     *
     * @var    array
     */
    protected $properties = array('cry', 'food', 'ability');
}

class Sabertooth extends Tiger
{
// expected final model: the same as Tiger's
}

class Chimera extends Sabertooth
{

    /**
     * Food
     *
     * @var    string
     */
    protected $food = 'heroes';

    /**
     * Ability
     *
     * @var    string
     */
    protected $ability = 'fly';
}
