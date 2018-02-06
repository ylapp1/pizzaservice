<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Validators;

use PizzaService\Propel\Models\OrderPizza;

/**
 * Checks whether an OrderPizza object is valid.
 */
class OrderPizzaValidator
{
    /**
     * Checks whether an OrderPizza object is valid.
     *
     * @param OrderPizza $_orderPizza The order pizza
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    public function validateOrderPizza(OrderPizza $_orderPizza)
    {
        $pizza = $_orderPizza->getPizza();
        if (! $pizza) return "Fehler: Ungültige Bestellpizza";

        $pizzaHasId = false;
        if ($pizza->getId()) $pizzaHasId = true;

        $pizzaValidator = new PizzaValidator();

        $error = $pizzaValidator->validatePizza($pizza, $pizzaHasId);
        if ($error) return $error;

        $error = $this->validateAmount($_orderPizza->getAmount());
        if ($error) return $error;

        return false;
    }

    /**
     * Checks whether the amount of an order pizza is valid.
     *
     * @param int $_amount The amount of the order pizza
     *
     * @return String|bool Error message or false
     */
    private function validateAmount(int $_amount)
    {
        if ($_amount > 50) return "Fehler: Eine Pizza in der Bestellung ist über 50 mal bestellt worden.";
        else if ($_amount < 0) return "Fehler: Die Bestellmenge je Pizza darf 0 nicht unterschreiten.";
        else return false;
    }
}
