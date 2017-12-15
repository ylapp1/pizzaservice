<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$loader = require_once(__DIR__ . "/../../vendor/autoload.php");
$loader->addPsr4("PizzaService\\", __DIR__ . "/../..");

// Initialize Propel with the runtime configuration
Propel::init(__DIR__ . "/../../propel/conf/pizza_service-conf.php");


use PizzaService\Lib\Web\LayoutHelper;
use PizzaService\Lib\Web\PizzaListRenderers\PizzaListOrderRenderer;
use PizzaService\Propel\Models\PizzaQuery;


if (isset($_GET["delete"]))
{
    if (session_id() == "") session_start();

    unset($_SESSION["orderPizzas"][$_GET["delete"]]);
}


$layoutHelper = new LayoutHelper();
$pizzaListRenderer = new PizzaListOrderRenderer();

$layoutHelper->renderHead("pizzaOrder");
$layoutHelper->renderHeader("pizza-order");

$pizzaIds = array_keys($_SESSION["orderPizzas"]);

$pizzas = PizzaQuery::create()->orderByOrderCode()->findById($pizzaIds);

echo $pizzaListRenderer->renderPizzaList($pizzas, "Die Bestellung ist leer", $_SESSION["orderPizzas"]);
echo file_get_contents(__DIR__ . "/../../web/templates/order/addressInputField.html");

$layoutHelper->renderFooter();
