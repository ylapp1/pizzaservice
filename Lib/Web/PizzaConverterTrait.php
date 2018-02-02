<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web;
use PizzaService\Propel\Models\OrderPizza;
use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\PizzaIngredient;

/**
 * Converts a Pizza object to an array and creates a pizza from an array.
 */
trait PizzaConverterTrait
{
    /**
     * Converts a pizza and its ingredients to an array.
     *
     * @param OrderPizza|Pizza $_pizza The order pizza or pizza
     *
     * @return array The pizza as an array
     *
     * @throws \PropelException
     */
    private function pizzaToArray($_pizza): array
    {
        $pizzaIngredients = array();

        if ($_pizza instanceof Pizza) $pizza = $_pizza;
        elseif ($_pizza instanceof OrderPizza) $pizza = $_pizza->getPizza();

        foreach ($pizza->getPizzaIngredients() as $pizzaIngredient)
        {
            $pizzaIngredients[] = $pizzaIngredient->toJSON(true);
        }

        $pizzaArray = array(
            "Pizza" => $pizza->toJSON(true),
            "PizzaIngredients" => $pizzaIngredients
        );

        if ($_pizza instanceof OrderPizza) $pizzaArray["Amount"] = $_pizza->getAmount();

        return $pizzaArray;
    }

    /**
     * Creates a pizza object from an array of pizza data.
     *
     * @param array $_pizzaArray The pizza data
     *
     * @return Pizza The Pizza object
     */
    private function pizzaFromArray(array $_pizzaArray): Pizza
    {
        $pizza = new Pizza();
        $pizza->fromJSON($_pizzaArray["Pizza"]);

        foreach ($_pizzaArray["PizzaIngredients"] as $pizzaIngredientJSON)
        {
            $pizzaIngredient = new PizzaIngredient();
            $pizzaIngredient->fromJSON($pizzaIngredientJSON);
            $pizza->addPizzaIngredient($pizzaIngredient);
        }

        return $pizza;
    }
}
