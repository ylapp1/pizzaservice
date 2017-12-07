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

use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\Ingredient;
use PizzaService\Propel\Models\PizzaIngredient;

// Adds a test pizza with one ingredient to the database
$pizza = new Pizza();

$ingredient = new Ingredient();
$ingredient->setName("Teig");

$ingredientDough = new PizzaIngredient();
$ingredientDough->setGrams(1000)
                ->setIngredient($ingredient);

$pizza->setName("Pizzateig pur")
      ->setOrderCode(1)
      ->setPrice(4.50)
      ->addPizzaIngredient($ingredientDough)
      ->save();
