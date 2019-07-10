<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Validators;

use PizzaService\Lib\ConfigLoader;
use PizzaService\Lib\Web\PizzaOrder;
use PizzaService\Propel\Models\OrderPizza;

/**
 * Checks whether a pizza order is valid.
 */
class PizzaOrderValidator
{
    /**
     * The maximum allowed amount per pizza
     *
     * @var $maxAmountPerPizza
     */
    private $maxAmountPerPizza;

    /**
     * The maximum allowed total amount of pizzas in the order
     *
     * @var int $maxTotalAmountPizzas
     */
    private $maxTotalAmountPizzas;


    /**
     * PizzaOrderValidator constructor.
     */
    public function __construct()
    {
        $configLoader = new ConfigLoader(__DIR__ . "/../../config/config.json");

        $this->maxAmountPerPizza = $configLoader->getConfigValue("maxAmountPerPizza", 50);
        $this->maxTotalAmountPizzas = $configLoader->getConfigValue("maxTotalAmountPizzas", 100);
    }

    /**
     * Checks whether a pizza order is valid.
     *
     * @param PizzaOrder $_pizzaOrder The pizza order
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    public function validatePizzaOrder(PizzaOrder $_pizzaOrder)
    {
        $error = $this->validateOrderPizzas($_pizzaOrder->getOrder());
        if ($error) return $error;

        $error = $this->validateTotalAmount($_pizzaOrder->getTotalAmountOrderPizzas());
        if ($error) return $error;

        return false;
    }

    /**
     * Checks whether the total amount of pizzas in an order is valid.
     *
     * @param int $_totalAmount The total amount of pizzas in the order
     *
     * @return String|bool Error message or false
     */
    private function validateTotalAmount(int $_totalAmount)
    {
        if ($_totalAmount == 0) return "Fehler: Die Bestellung ist leer";
        else if ($_totalAmount > $this->maxTotalAmountPizzas) return "Fehler: Es dürfen nicht mehr als " . $this->maxTotalAmountPizzas . " Pizzas auf einmal bestellt werden";
        else return false;
    }

    /**
     * Checks whether the order pizzas in the order are valid.
     *
     * @param OrderPizza[] $_orderPizzas The order pizzas in the order
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    private function validateOrderPizzas($_orderPizzas)
    {
        $orderPizzaValidator = new OrderPizzaValidator();

        foreach ($_orderPizzas as $orderPizza)
        {
            $error = $orderPizzaValidator->validateOrderPizza($orderPizza, $this->maxAmountPerPizza);
            if ($error) return $error;
        }

        return false;
    }
}
