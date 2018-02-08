<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\App\Controller;

use PizzaService\Lib\Validators\PizzaOrderValidator;
use PizzaService\Lib\Web\CustomerCreator;
use PizzaService\Lib\Web\PizzaOrder;
use PizzaService\Propel\Models\Customer;
use PizzaService\Propel\Models\Order;

/**
 * Handles the processing of a pizza order.
 */
class PizzaOrderProcessController
{
    /**
     * The pizza order.
     *
     * @var PizzaOrder $pizzaOrder
     */
    private $pizzaOrder;


    /**
     * PizzaOrderProcessController constructor.
     *
     * @throws \PropelException
     */
    public function __construct()
    {
        $this->pizzaOrder = new PizzaOrder();
    }


    /**
     * Checks whether every customer value is valid.
     *
     * @return String|bool Error message or false
     */
    private function validateCustomer()
    {
        // Check whether any value is empty
        if ($_GET["firstName"] == "" ||
            $_GET["lastName"] == "" ||
            $_GET["streetName"] == "" ||
            $_GET["houseNumber"] == "" ||
            $_GET["zip"] == "" ||
            $_GET["city"] == "") {
            return "Fehler: Ein oder mehrere Felder sind leer";
        }
        else return false;
    }

    /**
     * Checks whether every order value is valid.
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    private function validateOrder()
    {
        $pizzaOrderValidator = new PizzaOrderValidator();

        $error = $pizzaOrderValidator->validatePizzaOrder($this->pizzaOrder);
        if ($error) return $error;
        return false;
    }

    /**
     * Creates and returns a new Order object without saving it to the database.
     *
     * @param Customer $_customer The customer object
     *
     * @return Order The order object
     *
     * @throws \Exception
     * @throws \PropelException
     */
    private function getOrder(Customer $_customer): Order
    {
        // Create a new order
        $order = new Order();
        $order->setCustomer($_customer);

        // Add pizzas to the order
        foreach ($this->pizzaOrder->getOrder() as $orderPizza)
        {
            $order->addOrderPizza($orderPizza);
        }

        return $order;
    }

    /**
     * Adds the order to the database when the customer and pizza data is valid.
     *
     * @return String Error message or empty string
     *
     * @throws \Exception
     * @throws \PropelException
     */
    public function addOrder(): String
    {
        $error = $this->validateCustomer();
        if ($error) return $error;

        $error = $this->validateOrder();
        if ($error) return $error;

        $customerCreator = new CustomerCreator();
        $customer = $customerCreator->getCustomer($_GET["firstName"], $_GET["lastName"] , "Deutschland",
                                                  $_GET["city"], $_GET["zip"], $_GET["streetName"], $_GET["houseNumber"]);

        $this->getOrder($customer)->save();
        $this->pizzaOrder->resetOrder();

        return "";
    }
}
