<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$loader = require_once(__DIR__ . "/../../../vendor/autoload.php");
$loader->addPsr4("PizzaService\\", __DIR__ . "/../../../");

// Initialize Propel with the runtime configuration
Propel::init(__DIR__ . "/../../../propel/conf/pizza_service-conf.php");


use PizzaService\Propel\Models\Customer;
use PizzaService\Propel\Models\CustomerQuery;
use PizzaService\Propel\Models\Order;
use PizzaService\Propel\Models\PizzaOrder;
use PizzaService\Propel\Models\PizzaQuery;

// Inserts the order into the database
if (session_id() == "") session_start();
if (! $_SESSION["orderPizzas"])
{
    echo "Fehler: Die Bestellung ist leer.";
    return;
}


$amountPizzas = 0;
foreach ($_SESSION["orderPizzas"] as $pizzaId => $amount)
{
    $amountPizzas += $amount;
}

if (! $amountPizzas)
{
    echo "Fehler: Die Bestellung ist leer.";
    return;
}

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
{ // Create a new customer if customer did not exist in the database

    // Check whether any value is empty
    if ($_GET["firstName"] == "" ||
        $_GET["lastName"] == "" ||
        $_GET["streetName"] == "" ||
        $_GET["houseNumber"] == "" ||
        $_GET["zip"] == "" ||
        $_GET["city"] == "")
    {
        echo "Fehler: Ein oder mehrere Felder sind leer";
    }

    // Check whether the numbers are integers
    if (intval($_GET["houseNumber"]) != $_GET["houseNumber"])
    {
        echo "Fehler: Die Hausnummer muss eine Ganzzahl sein";
        return;
    }
    else if (intval($_GET["zip"]) != $_GET["zip"])
    {
        echo "Fehler: Die Postleitzahl muss eine Ganzzahl sein";
        return;
    }

    $customer = new Customer();
    $customer->setFirstName($_GET["firstName"])
             ->setLastName($_GET["lastName"])
             ->setStreetName($_GET["streetName"])
             ->setHouseNumber((int)$_GET["houseNumber"])
             ->setZip((int)$_GET["zip"])
             ->setCity($_GET["city"])
             ->setCountry("Deutschland");
}


// Create order
$order = new Order();
try
{
    $order->setCustomer($customer);
}
catch (Exception $_exception)
{
    echo "Fehler: " .  $_exception->getMessage();
    return;
}

// Add pizza orders
foreach ($_SESSION["orderPizzas"] as $pizzaId => $amount)
{
    $pizza = PizzaQuery::create()->findOneById($pizzaId);

    if (! $pizza)
    {
        echo "Fehler: Ungültige Pizza ID in der Bestellung";
        return;
    }

    $pizzaOrder = new PizzaOrder();
    $pizzaOrder->setPizza($pizza)
               ->setAmount($amount);

    $order->addPizzaOrder($pizzaOrder);
}

try
{
    $order->save();
    unset($_SESSION["orderPizzas"]);

    echo 1;
}
catch (Exception $_exception)
{
    echo "Fehler: " . $_exception->getMessage();
}
