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

// Inserts the order into the database
if (session_id() == "") session_start();
if (! $_SESSION["orderPizzas"]) return;


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
    $customer = new Customer();
    $customer->setFirstName($_GET["firstName"])
             ->setLastName($_GET["lastName"])
             ->setStreetName($_GET["streetName"])
             ->setHouseNumber((int)$_GET["houseNumber"])
             ->setZip((int)$_GET["zip"])
             ->setCity($_GET["city"])
             ->setCountry("Germany");
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
    $pizzaOrder = new PizzaOrder();
    $pizzaOrder->setPizzaId($pizzaId)
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
