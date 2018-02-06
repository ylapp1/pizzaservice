<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Validators;

use PizzaService\Lib\Web\PizzaOrder;
use PizzaService\Propel\Models\OrderPizza;

/**
 * Checks whether a pizza order is valid.
 */
class PizzaOrderValidator
{
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
        $error = $this->validateTotalAmount($_pizzaOrder->getTotalAmountOrderPizzas());
        if ($error) return $error;

        $error = $this->validateOrderPizzas($_pizzaOrder->getOrder());
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
        else if ($_totalAmount > 100) return "Fehler: Es dürfen nicht mehr als 100 Pizzas auf einmal bestellt werden";
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
            $error = $orderPizzaValidator->validateOrderPizza($orderPizza);
            if ($error) return $error;
        }

        return false;
    }
}
