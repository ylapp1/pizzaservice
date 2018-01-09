<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\App\Controller;

use PizzaService\Lib\Web\PizzaOrderSession\PizzaOrderHandler;
use PizzaService\Propel\Models\Customer;
use PizzaService\Propel\Models\CustomerQuery;
use PizzaService\Propel\Models\Order;
use PizzaService\Propel\Models\PizzaOrder;
use PizzaService\Propel\Models\PizzaQuery;

/**
 * Handles the processing of a pizza order.
 */
class PizzaOrderProcessController
{
    /**
     * The pizza order handler.
     *
     * @var PizzaOrderHandler $pizzaOrderHandler
     */
    private $pizzaOrderHandler;


    /**
     * PizzaOrderProcessController constructor.
     */
    public function __construct()
    {
        $this->pizzaOrderHandler = new PizzaOrderHandler();
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

        // Check whether the numbers are integers
        if (intval($_GET["houseNumber"]) != $_GET["houseNumber"])
        {
            return "Fehler: Die Hausnummer muss eine Ganzzahl sein";
        }
        elseif (intval($_GET["zip"]) != $_GET["zip"])
        {
            return "Fehler: Die Postleitzahl muss eine Ganzzahl sein";
        }

        return false;
    }

    /**
     * Creates and returns a new customer or returns an existing customer from the database.
     *
     * @return Customer The customer object
     */
    private function getCustomer(): Customer
    {
        // Check whether a customer with the customer data already exists in the database
        $customer = CustomerQuery::create()->filterByFirstName($_GET["firstName"])
                                           ->filterByLastName($_GET["lastName"])
                                           ->filterByStreetName($_GET["streetName"])
                                           ->filterByHouseNumber((int)$_GET["houseNumber"])
                                           ->filterByZip((int)$_GET["zip"])
                                           ->filterByCity($_GET["city"])
                                           ->filterByCountry("Germany")
                                           ->findOne();

        if (! $customer)
        { // Create a new customer if customer does not exist in the database

            $customer = new Customer();
            $customer->setFirstName($_GET["firstName"])
                     ->setLastName($_GET["lastName"])
                     ->setStreetName($_GET["streetName"])
                     ->setHouseNumber((int)$_GET["houseNumber"])
                     ->setZip((int)$_GET["zip"])
                     ->setCity($_GET["city"])
                     ->setCountry("Deutschland");
        }

        return $customer;
    }

    /**
     * Checks whether every order value is valid.
     *
     * @return String|bool Error message or false
     */
    private function validateOrder()
    {
        $totalAmountPizzas = $this->pizzaOrderHandler->getTotalAmountOrderPizzas();

        if ($totalAmountPizzas == 0) return "Fehler: Die Bestellung ist leer";
        elseif ($totalAmountPizzas > 100) return "Fehler: Es dürfen nicht mehr als 100 Pizzas auf einmal bestellt werden";
        else
        {
            foreach ($this->pizzaOrderHandler->getOrder() as $pizzaId => $amount)
            {
                $pizza = PizzaQuery::create()->findOneById($pizzaId);
                if (! $pizza) return "Fehler: Ungültige Pizza ID in der Bestellung";
                elseif ($amount > 50) return "Fehler: Eine Pizza in der Bestellung ist über 50 mal bestellt worden.";
            }
        }

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
        foreach ($this->pizzaOrderHandler->getOrder() as $pizzaId => $amount)
        {
            $pizzaOrder = new PizzaOrder();
            $pizzaOrder->setPizzaId($pizzaId)
                ->setAmount($amount);

            $order->addPizzaOrder($pizzaOrder);
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

        $this->getOrder($this->getCustomer())->save();
        $this->pizzaOrderHandler->resetOrder();

        return "";
    }
}
