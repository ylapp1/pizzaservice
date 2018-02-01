<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$loader = require_once(__DIR__ . "/vendor/autoload.php");
$loader->addPsr4("PizzaService\\", __DIR__);

// Initialize Propel with the runtime configuration
Propel::init(__DIR__ . "/propel/conf/pizza_service-conf.php");

use PizzaService\Propel\Models\Customer;
use PizzaService\Propel\Models\Order;
use PizzaService\Propel\Models\PizzaOrder;
use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\Ingredient;
use PizzaService\Propel\Models\IngredientTranslation;
use PizzaService\Propel\Models\PizzaIngredient;

// Adds one entry for each table
$pizza = new Pizza();

$ingredientName = new IngredientTranslation();
$ingredientName->setLanguageCode("de");
$ingredientName->setIngredientName("Teig");

$ingredient = new Ingredient();
$ingredient->addIngredientTranslation($ingredientName);

$ingredientDough = new PizzaIngredient();
$ingredientDough->setGrams(1000)
                ->setIngredient($ingredient);

$pizza->setName("Pizzateig pur")
      ->setOrderCode(1)
      ->setPrice(4.50)
      ->addPizzaIngredient($ingredientDough);

$customer = new Customer();
$customer->setFirstName("Test")
         ->setLastName("Käufer")
         ->setCountry("Deutschland")
         ->setZip("11111")
         ->setCity("Einshausen")
         ->setStreetName("Einsstraße")
         ->setHouseNumber(1);

$pizzaOrder = new PizzaOrder();
$pizzaOrder->setPizza($pizza)
           ->setAmount(1);

$order = new Order();
$order->setCustomer($customer)
      ->addPizzaOrder($pizzaOrder)
      ->save();
