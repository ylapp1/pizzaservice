<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$pizzaId = (int)$_GET["pizza-id"];
$amount = (int)$_GET["amount"];

// Adds an amount of pizzas to an order pizza
if (session_id() == "") session_start();

if (! $_SESSION["orderPizzas"]) $_SESSION["orderPizzas"] = array($pizzaId => $amount);
else
{
    if (! $_SESSION["orderPizzas"][$pizzaId]) $_SESSION["orderPizzas"][$pizzaId] = 0;

    if ($_SESSION["orderPizzas"][$pizzaId] + $amount > 50)
    {
        echo "Es dürfen nicht mehr als 50 Stück je Pizza in der Bestellung vorhanden sein.";
    }
    elseif (array_sum($_SESSION["orderPizzas"]) + $amount > 100)
    {
        echo "Es dürfen nicht mehr als 100 Pizzen auf einmal bestellt werden.";
    }
    else $_SESSION["orderPizzas"][$pizzaId] += $amount;
}
