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
use PizzaService\Propel\Models\PizzaQuery;

/**
 * Stores the order pizzas and saves/loads them from the $_SESSION variable.
 */
class PizzaOrder
{
    use PizzaConverterTrait;

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


    // Write and read orderPizzas from session

    /**
     * Starts a session and initializes the order array if no session is running.
     */
    private function startSession()
    {
        if (session_id() == "") session_start();
        if (! $_SESSION[$this->pizzaOrderSessionIndex]) $_SESSION[$this->pizzaOrderSessionIndex] = array();
    }

    /**
     * Saves the order pizza list to the session.
     *
     * @throws \PropelException
     */
    public function toSession()
    {
        $this->startSession();

        unset($_SESSION[$this->pizzaOrderSessionIndex]);

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

        foreach ($_SESSION[$this->pizzaOrderSessionIndex] as $pizzaOrderCode => $pizzaOrderData)
        {
            $orderPizza = new OrderPizza();

            if (gettype($pizzaOrderData) == "array")
            {
                $pizza = $this->pizzaFromArray($pizzaOrderData);
                $orderPizza->setPizza($pizza)
                           ->setAmount($pizzaOrderData["Amount"]);
            }
            else
            {
                $pizza = PizzaQuery::create()->findOneByOrderCode($pizzaOrderCode);
                $orderPizza->setPizza($pizza)
                           ->setAmount($pizzaOrderData);
            }

            $this->orderPizzas[$pizzaOrderCode] = $orderPizza;
        }
    }


    // Get information about the order

    /**
     * Returns the order array.
     *
     * @return OrderPizza[] The order array
     */
    public function getOrder()
    {
        return $this->orderPizzas;
    }

    /**
     * Returns an order pizza from the order with a specific order code.
     *
     * @param String $_orderCode The order code
     *
     * @return OrderPizza|bool The order pizza or false
     */
    public function getOrderPizza(String $_orderCode)
    {
        $orderPizza = $this->orderPizzas[$_orderCode];

        if ($orderPizza) return $orderPizza;
        else return false;
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
     * Returns the pizzas that are currently stored in the order.
     *
     * @return Pizza[] The list of pizzas
     *
     * @throws \PropelException
     */
    public function getPizzas()
    {
        $pizzas = array();

        foreach ($this->orderPizzas as $orderPizza)
        {
            $pizzas[] = $orderPizza->getPizza();
        }

        return $pizzas;
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
            $amountOrderPizzas += $orderPizza->getAmount();
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


    // Manipulate the order pizzas

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

        $error = $this->validatePizzaAmount($_orderPizza, true);
        if ($error) return $error;

        if ($this->getOrderPizza($orderCode)) return $this->changeAmountOrderPizzas($_orderPizza);
        else return $this->addNewOrderPizza($_orderPizza);
    }

    /**
     * @param OrderPizza $_orderPizza
     *
     * @return String Empty string
     *
     * @throws \PropelException
     */
    public function addNewOrderPizza(OrderPizza $_orderPizza): String
    {
        $orderCode = $_orderPizza->getPizza()->getOrderCode();
        $this->orderPizzas[$orderCode] = $_orderPizza;

        $this->toSession();

        return "";
    }

    /**
     * @param OrderPizza $_orderPizza
     *
     * @return String Error message or empty string
     *
     * @throws \PropelException
     */
    public function changeAmountOrderPizzas(OrderPizza $_orderPizza): String
    {
        $orderCode = $_orderPizza->getPizza()->getOrderCode();
        $orderPizza = $this->orderPizzas[$orderCode];

        $error = $this->validatePizzaAmount($_orderPizza);
        if ($error) return $error;

        $newAmount = $orderPizza->getAmount() + $_orderPizza->getAmount();
        $orderPizza->setAmount($newAmount);

        $this->toSession();

        return false;
    }

    /**
     * Validates whether a pizza amount is ok.
     *
     * @param OrderPizza $_validateOrderPizza The order pizza
     * @param bool $_isAddOrderPizza Indicates whether the order pizza is added to the order
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    private function validatePizzaAmount(OrderPizza $_validateOrderPizza, bool $_isAddOrderPizza = false)
    {
        $orderPizza = $this->getOrderPizza($_validateOrderPizza->getPizza()->getOrderCode());

        if ($_isAddOrderPizza)
        {
            $validateOrderPizza = $_validateOrderPizza;
        }
        else
        {
            $validateOrderPizza = clone $_validateOrderPizza;
            $validateOrderPizza->setAmount($validateOrderPizza->getAmount() + $orderPizza->getAmount());
        }


        // Validate pizza amount for the single pizza
        if ($validateOrderPizza->getAmount() < 1 || $validateOrderPizza->getAmount() > 50)
        {
            return "Fehler: Die Anzahl je Pizza muss zwischen 1 und 50 liegen.";
        }

        // Validate total pizza amount
        $totalPizzaAmount = 0;

        foreach ($this->getOrder() as $orderPizza)
        {
            if ($orderPizza->getPizza()->getOrderCode() == $validateOrderPizza->getPizza()->getOrderCode())
            {
                $totalPizzaAmount += $validateOrderPizza->getAmount();
            }
            else $totalPizzaAmount += $orderPizza->getAmount();
        }

        if ($totalPizzaAmount > 100) return "Es dürfen nicht mehr als 100 Pizzen auf einmal bestellt werden.";

        return false;
    }

    /**
     * Removes a order pizza from the order.
     *
     * @param String $_orderCode The order code of the pizza
     *
     * @throws \PropelException
     */
    public function removeOrderPizza(String $_orderCode)
    {
        unset($this->orderPizzas[$_orderCode]);
        $this->toSession();
    }

    /**
     * Resets the order to an empty array.
     *
     * @throws \PropelException
     */
    public function resetOrder()
    {
        $this->orderPizzas = array();
        $this->toSession();
    }
}