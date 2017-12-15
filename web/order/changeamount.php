<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$pizzaId = (int)$_GET["pizza-id"];
$amount = (int)$_GET["amount"];


if (session_id() == "") session_start();

if (isset($_SESSION["orderPizzas"]))
{
    if (isset($_SESSION["orderPizzas"][$pizzaId])) $_SESSION["orderPizzas"][$pizzaId] = $amount;

    $amountPizzas = 0;
    foreach ($_SESSION["orderPizzas"] as $pizzaId => $amount)
    {
        $amountPizzas += $amount;
    }

    echo $amountPizzas;
}
