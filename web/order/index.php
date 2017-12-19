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


use PizzaService\Lib\Web\PizzaListConverter;
use PizzaService\Propel\Models\PizzaQuery;

$loader = new Twig_Loader_Filesystem(__DIR__ . "/../templates");
$twig = new Twig_Environment($loader);

$pizzaListConverter = new PizzaListConverter();

if (session_id() == "") session_start();
if (! $_SESSION["orderPizzas"]) $_SESSION["orderPizzas"] = array();
if ($_GET["delete"]) unset($_SESSION["orderPizzas"][$_GET["delete"]]);

$pizzaIds = array_keys($_SESSION["orderPizzas"]);
$pizzas = PizzaQuery::create()->orderByOrderCode()
                              ->findById($pizzaIds);

$template = $twig->load("pizzaOrder.twig");
echo $template->render(
    array(
        "totalAmountPizzas" => array_sum($_SESSION["orderPizzas"]),
        "pizzas" => $pizzaListConverter->getPizzaList($pizzas, $_SESSION["orderPizzas"])
    )
);
