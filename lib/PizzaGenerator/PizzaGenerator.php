<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\PizzaGenerator;

use PizzaService\Propel\Models\Ingredient;
use PizzaService\Propel\Models\IngredientTranslationQuery;
use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\PizzaIngredient;
use PizzaService\Propel\Models\PizzaQuery;

/**
 * Generates pizzas with random names and random ingredients.
 */
class PizzaGenerator
{
    /**
     * The random pizza name generator
     *
     * @var PizzaNameGenerator $pizzaNameGenerator
     */
    private $pizzaNameGenerator;


    /**
     * PizzaGenerator constructor.
     */
    public function __construct()
    {
        $this->pizzaNameGenerator = new PizzaNameGenerator();
    }


    /**
     * Generates a random pizza.
     *
     * @param Ingredient[] $_allowedIngredients The ingredients that may be on the pizza
     *
     * @return Pizza The random pizza
     *
     * @throws \PropelException
     */
    public function generatePizza($_allowedIngredients): Pizza
    {
        $pizza = new Pizza();
        $this->addRandomIngredients($pizza, $_allowedIngredients);

        $pizza->setOrderCode($this->getNewOrderCode());
        $pizza->setPrice($this->getRandomPrice());
        $pizza->setName($this->pizzaNameGenerator->generatePizzaName($pizza));

        return $pizza;
    }

    /**
     * Returns a random price between 5€ and 10€.
     *
     * @return float The random price
     */
    private function getRandomPrice(): float
    {
        $randomCents = rand(500, 1000);
        $randomEuros = (float)$randomCents/100;

        return $randomEuros;
    }

    /**
     * Returns a new order code for the random pizza.
     * Generated pizzas have the prefix G in order to be able to distinguish between pizzas that were entered with
     * the pizzatool and random pizzas
     *
     * @return String The order code
     */
    private function getNewOrderCode(): String
    {
        // Find the random pizza with the highest order code
        $pizza = PizzaQuery::create()->filterByOrderCode("G*")
                                     ->orderByOrderCode("desc")
                                     ->findOne();

        if ($pizza)
        {
            $highestOrderCode = $pizza->getOrderCode();
            $highestOrderCodeNumber = (int)str_replace("G", "", $highestOrderCode);
        }
        else $highestOrderCodeNumber = 0;

        $newOrderCodeNumber = $highestOrderCodeNumber + 1;

        return "G" . $newOrderCodeNumber;
    }

    /**
     * Adds random ingredients to a pizza until its total weight is 400 grams.
     *
     * @param Pizza $_pizza The pizza
     * @param Ingredient[] $_allowedIngredients The list of allowed ingredients
     *
     * @throws \PropelException
     */
    private function addRandomIngredients(Pizza $_pizza, $_allowedIngredients)
    {
        // Maximum allowed weight per pizza
        $remainingWeight = 400;

        // Add 100 grams of dough for every pizza
        $ingredientDough = IngredientTranslationQuery::create()->filterByLanguageCode("it")
                                                               ->filterByIngredientName("Pasta")
                                                               ->findOne()
                                                               ->getIngredient();

        $remainingWeight = $this->addRandomIngredient($_pizza, $ingredientDough, 100, $remainingWeight);

        foreach ($_allowedIngredients as $ingredient)
        {
            $ingredientName = IngredientTranslationQuery::create()->filterByLanguageCode("it")
                                                                  ->filterByIngredient($ingredient)
                                                                  ->findOne()
                                                                  ->getIngredientName();

            if ($ingredientName == "Pasta") continue;

            // Add random ingredient
            $randomGrams = rand(0, 200);
            $remainingWeight = $this->addRandomIngredient($_pizza, $ingredient, $randomGrams, $remainingWeight);

            if ($remainingWeight == 0) break;
        }
    }

    /**
     * Adds a single ingredient to a pizza.
     *
     * @param Pizza $_pizza The pizza
     * @param Ingredient $_ingredient The ingredient that shall be added to the pizza
     * @param int $_grams The amount of grams for this ingredient
     * @param int $_remainingWeight The remaining weight
     *
     * @return int The remaining weight
     *
     * @throws \PropelException
     */
    private function addRandomIngredient(Pizza $_pizza, Ingredient $_ingredient, int $_grams, int $_remainingWeight): int
    {
        // Update remaining weight
        if ($_remainingWeight - $_grams < 0)
        {
            $_grams = $_remainingWeight;
            $_remainingWeight = 0;
        }
        else $_remainingWeight -= $_grams;


        // Add ingredient to pizza
        $pizzaIngredient = new PizzaIngredient();
        $pizzaIngredient->setIngredient($_ingredient);
        $pizzaIngredient->setGrams($_grams);

        $_pizza->addPizzaIngredient($pizzaIngredient);

        // Return remaining weight
        return $_remainingWeight;
    }
}