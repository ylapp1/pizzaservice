<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\PizzaGenerator;

use PizzaService\Lib\Web\PizzaOrder;
use PizzaService\Propel\Models\Ingredient;
use PizzaService\Propel\Models\IngredientQuery;
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
     * The default pizza ingredients data in the format ["id" => id, "name" => name, "grams" => grams]
     *
     * @var array  $defaultPizzaIngredientsData
     */
    private $defaultPizzaIngredientsData;

    /**
     * The maximum total weight of a random pizza
     *
     * @var int  $maxTotalWeight
     */
    private $maxTotalWeight;

    /**
     * The minimum price of a random pizza
     *
     * @var float  $minPrice
     */
    private $minPrice;

    /**
     * The maximum price of a random pizza
     *
     * @var float $maxPrice
     */
    private $maxPrice;

    /**
     * The minimum amount of grams per random ingredient
     *
     * @var int $minGrams
     */
    private $minGrams;

    /**
     * The maximum amount of grams per random ingredient
     *
     * @var int $maxGrams
     */
    private $maxGrams;


    /**
     * PizzaGenerator constructor.
     *
     * @param array $_defaultPizzaIngredientsData The default pizza ingredients data in the format ["id" => id, "name" => name, "grams" => grams]
     * @param int $_maxTotalWeight The maximum total weight of a random pizza
     * @param float $_minPrice The minimum price of a random pizza
     * @param float $_maxPrice The maximum price of a random pizza
     * @param int $_minGrams The minimum amount of grams per random ingredient
     * @param int $_maxGrams The maximum amount of grams per random ingredient
     */
    public function __construct(array $_defaultPizzaIngredientsData, int $_maxTotalWeight, float $_minPrice, float $_maxPrice, int $_minGrams, int $_maxGrams)
    {
        $this->pizzaNameGenerator = new PizzaNameGenerator();
        $this->defaultPizzaIngredientsData = $_defaultPizzaIngredientsData;
        $this->maxTotalWeight = $_maxTotalWeight;
        $this->minPrice = $_minPrice;
        $this->maxPrice = $_maxPrice;
        $this->minGrams = $_minGrams;
        $this->maxGrams = $_maxGrams;
    }


    /**
     * Generates a random pizza.
     *
     * @param Ingredient[] $_allowedIngredients The ingredients that may be on the pizza
     * @param PizzaOrder $_pizzaOrder The pizza order
     *
     * @return Pizza The random pizza
     *
     * @throws \PropelException
     */
    public function generatePizza($_allowedIngredients, PizzaOrder $_pizzaOrder): Pizza
    {
        do
        {
            $pizza = new Pizza();
            $this->addRandomIngredients($pizza, $_allowedIngredients);

            $existingPizza = $this->generatedPizzaExists($pizza, count($pizza->getPizzaIngredients()));
            if ($existingPizza) return $existingPizza;

        } while ($this->generatedPizzaExistsInDatabase($pizza, count($pizza->getPizzaIngredients()),false));

        $defaultPizzaIngredientsIds = array_map(function($data){
            return $data["id"];
        }, $this->defaultPizzaIngredientsData);


        // Generate unique pizza name
        do
        {
            $pizzaName = $this->pizzaNameGenerator->generatePizzaName($pizza, $defaultPizzaIngredientsIds);
            $pizzaExists = PizzaQuery::create()->findOneByName($pizzaName);
        } while($pizzaExists);

        $pizza->setName($pizzaName);
        $pizza->setOrderCode($this->getNewOrderCode($_pizzaOrder));
        $pizza->setPrice($this->getRandomPrice());

        return $pizza;
    }

    /**
     * Checks whether a generated pizza ingredient grams/type combination already exists in the current order or the database.
     *
     * @param Pizza $_generatedPizza The generated Pizza
     * @param int $_numberOfIngredients The number of ingredients on the generated pizza
     *
     * @return Pizza|bool The existing pizza or false
     *
     * @throws \PropelException
     */
    private function generatedPizzaExists(Pizza $_generatedPizza, int $_numberOfIngredients)
    {
        $existingPizza = $this->generatedPizzaExistsInDatabase($_generatedPizza, $_numberOfIngredients, true);
        if ($existingPizza) return $existingPizza;

        $existingPizza = $this->generatedPizzaExistsInOrder($_generatedPizza, $_numberOfIngredients);
        if ($existingPizza) return $existingPizza;

        return false;
    }

    /**
     * Checks whether a generated pizza ingredient grams/type combination already exists in the database.
     *
     * @param Pizza $_generatedPizza The generated pizza
     * @param int $_numberOfIngredients The number of ingredients on the generated pizza
     * @param bool $_checkOnlyGenerated Indicates that only generated pizzas will be compared
     *
     * @return Pizza|boolean The existing pizza or false
     *
     * @throws \PropelException
     */
    private function generatedPizzaExistsInDatabase(Pizza $_generatedPizza, int $_numberOfIngredients, bool $_checkOnlyGenerated)
    {
        // Get pizzas with the same amount of ingredients
        $existingPizzasQuery = PizzaQuery::create()->joinpizzaIngredient();
        if ($_checkOnlyGenerated) $existingPizzasQuery->filterByOrderCode("G*");

        $existingPizzas = $existingPizzasQuery->withColumn("COUNT(*)", "amount_ingredients")
                                              ->groupBy("id")
                                              ->having("amount_ingredients = " . $_numberOfIngredients)
                                              ->find();

        return $this->generatedPizzaExistsInList($_generatedPizza, $existingPizzas, $_numberOfIngredients);
    }

    /**
     * Checks whether a generated pizza ingredient grams/type combination already exists in the current order.
     *
     * @param Pizza $_generatedPizza The generated pizza
     * @param int $_numberOfIngredients The number of ingredients on the generated pizza
     *
     * @return bool|Pizza The existing pizza or false
     *
     * @throws \PropelException
     */
    private function generatedPizzaExistsInOrder(Pizza $_generatedPizza, int $_numberOfIngredients)
    {
        // Check whether a generated pizza in the order has the same ingredients combination like the generated one
        $pizzaOrder = new PizzaOrder();
        $pizzasWithSameNumberOfIngredients = array();

        foreach ($pizzaOrder->getOrder() as $orderCode => $orderPizza)
        {
            // If order code starts with "G"
            if (substr($orderCode, 0, 1) == "G")
            {
                if (count($orderPizza->getPizza()->getPizzaIngredients()) == $_numberOfIngredients)
                {
                    $pizzasWithSameNumberOfIngredients[] = $orderPizza->getPizza();
                }
            }
        }

        return $this->generatedPizzaExistsInList($_generatedPizza, $pizzasWithSameNumberOfIngredients, $_numberOfIngredients);
    }

    /**
     * Checks whether a generated pizza ingredient grams/type combination already exists in a list of Pizza objects.
     *
     * @param Pizza $_generatedPizza The generated pizza
     * @param Pizza[] $_pizzasWithSameNumberOfIngredients The list of pizzas with the same number of ingredients
     * @param int $_numberOfIngredients The number of ingredients on the generated pizza
     *
     * @return Pizza|bool The existing pizza or false
     *
     * @throws \PropelException
     */
    private function generatedPizzaExistsInList(Pizza $_generatedPizza, $_pizzasWithSameNumberOfIngredients, int $_numberOfIngredients)
    {
        foreach ($_pizzasWithSameNumberOfIngredients as $pizzaWithSameNumberOfIngredients)
        {
            $numberOfMatchingIngredients = 0;
            $counter = 0;

            foreach ($pizzaWithSameNumberOfIngredients->getPizzaIngredients() as $pizzaIngredient)
            {
                $counter++;

                foreach ($_generatedPizza->getPizzaIngredients() as $generatedPizzaIngredient)
                {
                    if ($pizzaIngredient->getIngredientId() == $generatedPizzaIngredient->getIngredientId() &&
                        $pizzaIngredient->getGrams() == $generatedPizzaIngredient->getGrams())
                    {
                        $numberOfMatchingIngredients++;
                        break;
                    }
                }

                if ($numberOfMatchingIngredients != $counter) break;
            }

            if ($numberOfMatchingIngredients == $_numberOfIngredients) return $pizzaWithSameNumberOfIngredients;
        }

        return false;
    }

    /**
     * Returns a random price between 5€ and 10€.
     *
     * @return float The random price
     */
    private function getRandomPrice(): float
    {
        $randomCents = rand($this->minPrice * 100, $this->maxPrice * 100);
        $randomEuros = (float)$randomCents/100;

        return $randomEuros;
    }

    /**
     * Returns a new order code for the random pizza.
     * Generated pizzas have the prefix G in order to be able to distinguish between pizzas that were entered with
     * the pizzatool and random pizzas
     *
     * @param PizzaOrder $_pizzaOrder The pizza order
     *
     * @return String The order code
     */
    private function getNewOrderCode(PizzaOrder $_pizzaOrder): String
    {
        /*
         * Find the random pizza with the highest order code
         *
         * The "withColumn" is necessary to additionally order the results by the length of the order code. This results
         * in a natural order despite the leading "G".
         */
        $pizza = PizzaQuery::create()->withColumn("LENGTH(order_code)", "order_code_length")
                                     ->filterByOrderCode("G*")
                                     ->orderBy("order_code_length", "desc")
                                     ->orderByOrderCode("desc")
                                     ->findOne();

        if ($pizza)
        {
            $highestOrderCode = $pizza->getOrderCode();
            $highestOrderCodeNumber = (int)str_replace("G", "", $highestOrderCode);
        }
        else $highestOrderCodeNumber = 0;

        // Check the pizzas order codes that are in the order but not saved in the database yet
        $newOrderCodeNumber = $highestOrderCodeNumber + 1;
        while ($_pizzaOrder->getOrderPizza("G" . $newOrderCodeNumber))
        {
            $newOrderCodeNumber++;
        }

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
        $remainingWeight = $this->maxTotalWeight;

        $defaultIngredientIds = array_map(function($_data){
            return (int)$_data["id"];
        }, $this->defaultPizzaIngredientsData);

        // Add default ingredients
        foreach ($this->defaultPizzaIngredientsData as $defaultPizzaIngredientData)
        {
            $ingredientId = (int)$defaultPizzaIngredientData["id"];
            $ingredientGrams = (int)$defaultPizzaIngredientData["grams"];

            $ingredient = IngredientQuery::create()->findOneById($ingredientId);

            $remainingWeight = $this->addRandomIngredient($_pizza, $ingredient, $ingredientGrams, $remainingWeight);
        }

        // Add random ingredients
        $_allowedIngredients = (array)$_allowedIngredients;
        shuffle($_allowedIngredients);

        foreach ($_allowedIngredients as $ingredient)
        {
            if (in_array($ingredient->getId(), $defaultIngredientIds)) continue;

            // Add random ingredient
            $randomGrams = rand($this->minGrams, $this->maxGrams);
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
