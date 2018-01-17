<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$loader = require_once(__DIR__ . "/../vendor/autoload.php");
$loader->addPsr4("PizzaService\\", __DIR__ . "/..");

// Initialize Propel with the runtime configuration
Propel::init(__DIR__ . "/../propel/conf/pizza_service-conf.php");

use Silex\Application;
use PizzaService\Lib\Web\App\Controller\PizzaGeneratorController;
use PizzaService\Lib\Web\App\Controller\PizzaMenuCardController;
use PizzaService\Lib\Web\App\Controller\PizzaOrderController;
use PizzaService\Lib\Web\App\Controller\PizzaOrderProcessController;

$app = new Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    "twig.path" => __DIR__ . "/templates",
));

$pizzaGeneratorController = new PizzaGeneratorController($app["twig"]);
$pizzaMenuCardController = new PizzaMenuCardController($app["twig"]);
$pizzaOrderController = new PizzaorderController($app["twig"]);
$pizzaOrderProcessController = new PizzaOrderProcessController();

// Register routes

// Pizza menu card page
$pizzaMenuCard = $app["controllers_factory"];
$pizzaMenuCard->get("/", function() use($pizzaMenuCardController) {
    return $pizzaMenuCardController->showPizzaMenuCard();
});
$pizzaMenuCard->get("/addpizzatoorder.php", function() use($pizzaMenuCardController) {
    return $pizzaMenuCardController->addPizzaToOrder();
});

// Pizza order page
$pizzaOrder = $app["controllers_factory"];
$pizzaOrder->get("/", function() use($pizzaOrderController) {
    return $pizzaOrderController->showPizzaOrder();
});
$pizzaOrder->get("/changeamount.php", function() use($pizzaOrderController) {
    return $pizzaOrderController->changeAmount();
});
$pizzaOrder->get("/process/", function() use($pizzaOrderProcessController){
    return $pizzaOrderProcessController->addOrder();
});

// Pizza generator page
$pizzaGenerator = $app["controllers_factory"];
$pizzaGenerator->get("/", function() use($pizzaGeneratorController) {
    return $pizzaGeneratorController->showPizzaGenerator();
});
$pizzaGenerator->get("/generate-pizza/", function() use($pizzaGeneratorController) {
    return $pizzaGeneratorController->generatePizza();
});
$pizzaGenerator->get("/save-generated-pizza/", function() use($pizzaGeneratorController) {
    return $pizzaGeneratorController->saveGeneratedPizza();
});

$app->mount("/", $pizzaMenuCard);
$app->mount("/order/", $pizzaOrder);
$app->mount("/pizza-generator/", $pizzaGenerator);

$app->run();
