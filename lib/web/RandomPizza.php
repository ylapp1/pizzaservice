<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web;

/**
 * Saves and loads one random pizza from the session.
 */
class RandomPizza
{
    /**
     * @var null
     */
    private $orderPizza;

    public function __construct($_orderPizza = null)
    {
        if ($_orderPizza) $this->orderPizza = $_orderPizza;
        else $this->fromSession();
    }

    public function toSession()
    {

    }

    public function fromSession()
    {

    }

    public function getPizza()
    {

    }

    public function setPizza()
    {

    }
}