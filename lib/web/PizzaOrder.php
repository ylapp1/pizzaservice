<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web;

use PizzaService\Propel\Models\OrderPizza;
use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\PizzaIngredient;
use PizzaService\Propel\Models\PizzaQuery;

/**
 * Stores the order pizzas and saves/loads them from the $_SESSION variable.
 *
 * Remember to call toSession() after changing the order.
 */
class PizzaOrder
{
    /**
     * The list of order Pizzas
     *
     * @var OrderPizza[] $orderPizzas
     */
    private $orderPizzas;

    /**
     * The index of the pizza order in the $_SESSION variable
     *
     * @var String $sessionIndex
     */
    private $pizzaOrderSessionIndex = "orderPizzas";


    /**
     * PizzaOrder constructor.
     *
     * @throws \PropelException
     */
    public function __construct()
    {
        $this->orderPizzas = array();
        $this->fromSession();
    }


    /**
     * Adds an order pizza to the order.
     *
     * @param OrderPizza $_orderPizza The order pizza
     *
     * @return String Error message or empty string
     *
     * @throws \PropelException
     */
    public function addOrderPizza(OrderPizza $_orderPizza)
    {
        $orderCode = $_orderPizza->getPizza()->getOrderCode();

        if (substr($orderCode, 0, 1) != "G")
        { // If pizza is not a generated one

            $pizzaId = $_orderPizza->getPizza()->getId();
            if (! $pizzaId) return "Ungültige Pizza Id";

            $pizza = PizzaQuery::create()->findOneById($pizzaId);
            if (! $pizza) return "Ungültige Pizza Id";
        }


        if ($this->orderPizzas[$orderCode])
        { // If pizza with that order code is already in order

            $this->changeAmountOrderPizzas($_orderPizza);

        }
        else $this->addNewOrderPizza($_orderPizza);
    }

    /**
     * @param OrderPizza $_orderPizza
     *
     * @throws \PropelException
     */
    public function addNewOrderPizza(OrderPizza $_orderPizza)
    {
        $orderCode = $_orderPizza->getPizza()->getOrderCode();
        $this->orderPizzas[$orderCode] = $_orderPizza;
    }

    /**
     * @param OrderPizza $_orderPizza
     * @throws \PropelException
     */
    public function changeAmountOrderPizzas(OrderPizza $_orderPizza)
    {
        $orderCode = $_orderPizza->getPizza()->getOrderCode();
        $orderPizza = $this->orderPizzas[$orderCode];

        $newAmount = $orderPizza->getAmount() + $_orderPizza->getAmount();
        $orderPizza->setAmount($newAmount);
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
        if ($this->getAmountOrderPizzas($_pizzaId) + $_amount > 50 || $this->getAmountOrderPizzas($_pizzaId) + $_amount < 1)
        {
            return "Die Anzahl je Pizza muss zwischen 1 und 50 liegen.";
        }
        elseif ($this->getTotalAmountOrderPizzas() + $_amount > 100)
        {
            return "Es dürfen nicht mehr als 100 Pizzen auf einmal bestellt werden.";
        }
        else return false;
    }


    /**
     * Removes a order pizza from the order.
     *
     * @param String $_orderCode The order code of the pizza
     */
    public function removeOrderPizza(String $_orderCode)
    {
        unset($this->orderPizzas[$_orderCode]);
    }

    /**
     * Saves the order pizza list to the session.
     *
     * @throws \PropelException
     */
    public function toSession()
    {
        $this->startSession();

        foreach ($this->orderPizzas as $orderCode => $orderPizza)
        {
            $pizzaId = $orderPizza->getPizza()->getId();

            if ($pizzaId)
            { // If pizza has an id in the database save only the amount
                $_SESSION[$this->pizzaOrderSessionIndex][$pizzaId] = $orderPizza->getAmount();
            }
            else
            { // If pizza has no id in the database save the pizza configuration and the amount

                $orderCode = $orderPizza->getPizza()->getOrderCode();
                $_SESSION[$this->pizzaOrderSessionIndex][$orderCode] = $this->pizzaToArray($orderPizza);
            }
        }
    }

    /**
     * Loads the order pizza list from the session.
     *
     * @throws \PropelException
     */
    public function fromSession()
    {
        $this->startSession();

        $this->orderPizzas = array();

        foreach ($_SESSION[$this->pizzaOrderSessionIndex] as $pizzaId => $pizzaOrderData)
        {
            $orderPizza = new OrderPizza();

            if (gettype($pizzaOrderData) == "array")
            {
                $pizza = $this->pizzaFromArray($pizzaOrderData);
                $orderPizza->setPizza($pizza)
                           ->setAmount($pizzaOrderData["amount"]);
            }
            else
            {
                $pizza = PizzaQuery::create()->findOneById($pizzaId);
                $orderPizza->setPizza($pizza)
                           ->setAmount($pizzaOrderData);
            }

            $this->orderPizzas[] = $orderPizza;
        }
    }

    /**
     * Converts a pizza and its ingredients to an array.
     *
     * @param OrderPizza $_orderPizza The order pizza
     *
     * @return array The pizza as an array
     *
     * @throws \PropelException
     */
    private function pizzaToArray(OrderPizza $_orderPizza): array
    {
        $pizzaIngredients = array();

        $pizza = $_orderPizza->getPizza();
        foreach ($pizza->getPizzaIngredients() as $pizzaIngredient)
        {
            $pizzaIngredients[] = $pizzaIngredient->toJSON(true);
        }

        return array(
            "Pizza" => $pizza->toJSON(true),
            "PizzaIngredients" => $pizzaIngredients,
            "Amount" => $_orderPizza->getAmount()
        );
    }

    /**
     * Creates a pizza object from an array of pizza data.
     *
     * @param array $_pizzaArray The pizza data
     *
     * @return Pizza The Pizza object
     */
    private function pizzaFromArray(array $_pizzaArray): Pizza
    {
        $pizza = new Pizza();
        $pizza->fromJSON($_pizzaArray["Pizza"]);

        foreach ($_pizzaArray["PizzaIngredients"] as $pizzaIngredientJSON)
        {
            $pizzaIngredient = new PizzaIngredient();
            $pizzaIngredient->fromJSON($pizzaIngredientJSON);
            $pizza->addPizzaIngredient($pizzaIngredient);
        }

        return $pizza;
    }

    /**
     * Starts a session and initializes the order array if no session is running.
     */
    private function startSession()
    {
        if (session_id() == "") session_start();
        if (! $_SESSION[$this->pizzaOrderSessionIndex]) $_SESSION[$this->pizzaOrderSessionIndex] = array();
    }


    // Get information about the order
    /**
     * Returns the order array.
     *
     * @return OrderPizza[] The order array
     */
    public function getOrder(): array
    {
        return $this->orderPizzas;
    }

    /**
     * Returns the pizza ids of the order pizzas.
     *
     * @return int[] The pizza ids of the order pizzas
     */
    public function getPizzaOrderCodes()
    {
        return array_keys($this->orderPizzas);
    }

    /**
     * Returns the total amount of order pizzas.
     *
     * @return int The total amount of order pizzas
     */
    public function getTotalAmountOrderPizzas()
    {
        $amountOrderPizzas = 0;
        foreach ($this->orderPizzas as $orderPizza)
        {
            $amountOrderPizzas = $orderPizza->getAmount();
        }

        return $amountOrderPizzas;
    }

    /**
     * Calculates and returns the total order price.
     *
     * @return float The total order price
     *
     * @throws \PropelException
     */
    public function getTotalPrice()
    {
        $totalPrice = 0;
        foreach ($this->orderPizzas as $orderPizza)
        {
            $pizzaAmount = $orderPizza->getAmount();
            $pizzaPrice = $orderPizza->getPizza()->getPrice();

            $totalPrice += $pizzaAmount * $pizzaPrice;
        }

        return $totalPrice;
    }


    // Change the order

    /**
     * Resets the order to an empty array.
     */
    public function resetOrder()
    {
        $this->orderPizzas = array();
    }
}