<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$loader = require_once(__DIR__ . "/../vendor/autoload.php");
$loader->addPsr4("PizzaService\\", __DIR__ . "/..");

// Initialize Propel with the runtime configuration
Propel::init(__DIR__ . "/../propel/conf/pizza_service-conf.php");


use PizzaService\Lib\Web\LayoutHelper;
use PizzaService\Lib\Web\PizzaListRenderers\PizzaListMenuCardRenderer;
use PizzaService\Propel\Models\PizzaQuery;

$layoutHelper = new LayoutHelper();
$pizzaListRenderer = new PizzaListMenuCardRenderer();

$layoutHelper->renderHead("pizzaMenuCard");
$layoutHelper->renderHeader("pizza-menu");


$pizzas = PizzaQuery::create()->orderByOrderCode()
                              ->find();

echo $pizzaListRenderer->renderPizzaList($pizzas, "Es gibt noch keine Pizzas");

$layoutHelper->renderFooter();
