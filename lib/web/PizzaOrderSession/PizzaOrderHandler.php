<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\PizzaOrderSession;

use PizzaService\Propel\Models\PizzaQuery;

/**
 * Handles changing of and getting information about the current order.
 */
class PizzaOrderHandler
{
    /**
     * The pizza order session handler
     *
     * @var PizzaOrderSessionHandler $sessionHandler
     */
    private $sessionHandler;


    /**
     * PizzaOrderHandler constructor.
     */
    public function __construct()
    {
        $this->sessionHandler = new PizzaOrderSessionHandler();
    }


    // Get information about the order

    /**
     * Returns the order array.
     *
     * @return int[] The order array
     */
    public function getOrder(): array
    {
        return $this->sessionHandler->getPizzaOrder();
    }

    /**
     * Returns the pizza ids of the order pizzas.
     *
     * @return int[] The pizza ids of the order pizzas
     */
    public function getPizzaIds()
    {
        return array_keys($this->sessionHandler->getPizzaOrder());
    }

    /**
     * Returns Pizza objects for all order pizzas.
     *
     * @return \PizzaService\Propel\Models\Pizza[]
     */
    public function getPizzas()
    {
        $pizzaIds = $this->getPizzaIds();
        $pizzas = PizzaQuery::create()->orderByOrderCode()
                                      ->findById($pizzaIds);

        return $pizzas;
    }

    /**
     * Returns the amount of order pizzas for a specific pizza id.
     *
     * @param int $_pizzaId The pizza id
     *
     * @return int The amount of pizzas in the order for the pizza id
     */
    public function getAmountOrderPizzas(int $_pizzaId): int
    {
        return $this->sessionHandler->getPizzaAmount($_pizzaId);
    }

    /**
     * Returns the total amount of order pizzas.
     *
     * @return int The total amount of order pizzas
     */
    public function getTotalAmountOrderPizzas()
    {
        return array_sum($this->sessionHandler->getPizzaOrder());
    }

    /**
     * Calculates and returns the total order price.
     *
     * @return float The total order price
     */
    public function getTotalPrice()
    {
        $totalPrice = 0;
        foreach ($this->getPizzas() as $pizza)
        {
            $pizzaAmount = $this->getAmountOrderPizzas($pizza->getId());
            $pizzaPrice = $pizza->getPrice();

            $totalPrice += $pizzaAmount * $pizzaPrice;
        }

        return $totalPrice;
    }


    // Change the order

    /**
     * Adds a amount of pizzas to a pizza.
     *
     * @param int $_pizzaId The pizza id
     * @param int $_amount The amount that is added to the current amount
     */
    private function addPizza(int $_pizzaId, int $_amount)
    {
        $previousAmount = $this->sessionHandler->getPizzaAmount($_pizzaId);
        $newAmount = $previousAmount + $_amount;

        $this->sessionHandler->setPizzaAmount($_pizzaId, $newAmount);
    }

    /**
     * Validates a pizza amount change.
     *
     * @param int $_pizzaId The pizza id
     * @param int $_amount The amount that is added to the current amount
     *
     * @return String|bool Error message or false
     */
    private function validatePizzaAmountChange(int $_pizzaId, int $_amount)
    {
        if ($this->getAmountOrderPizzas($_pizzaId) + $_amount > 50)
        {
            return "Es dürfen nicht mehr als 50 Stück je Pizza in der Bestellung vorhanden sein.";
        }
        elseif ($this->getTotalAmountOrderPizzas() + $_amount > 100)
        {
            return "Es dürfen nicht mehr als 100 Pizzen auf einmal bestellt werden.";
        }
        else return false;
    }

    /**
     * Checks whether the amount of pizzas in the order is valid and then adds
     *
     * @param int $_pizzaId The pizza id
     * @param int $_amount The amount that is added to the current amount
     *
     * @return String|bool Error message or false
     */
    public function changeAmountPizzas(int $_pizzaId, int $_amount)
    {
        $error = $this->validatePizzaAmountChange($_pizzaId, $_amount);
        if ($error) return $error;

        $this->addPizza($_pizzaId, $_amount);
        return false;
    }

    /**
     * Removes a pizza from the order.
     *
     * @param int $_pizzaId The pizza id
     */
    public function removePizza(int $_pizzaId)
    {
        $this->sessionHandler->setPizzaAmount($_pizzaId, 0);
    }

    /**
     * Resets the order to an empty array.
     */
    public function resetOrder()
    {
        $this->sessionHandler->setPizzaOrder(array());
    }
}
