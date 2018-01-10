<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib;

/**
 * Converts a list of pizza ingredients to a string.
 */
class IngredientListConverter
{
    /**
     * Creates a comma separated string from a list of pizza ingredients.
     *
     * @param \PizzaService\Propel\Models\PizzaIngredient[] $_pizzaIngredients List of ingredients
     * @param String $_separationString The string that will be printed between the ingredients
     *
     * @return String The list of ingredients as a string
     *
     * @throws \PropelException
     */
    public function pizzaIngredientsToString($_pizzaIngredients, String $_separationString): String
    {
        $ingredientsString = "";
        $isFirstEntry = true;

        foreach ($_pizzaIngredients as $pizzaIngredient)
        {
            if ($pizzaIngredient instanceOf \PizzaService\Propel\Models\PizzaIngredient)
            {
                $ingredient = $pizzaIngredient->getIngredient();

                if ($isFirstEntry) $isFirstEntry = false;
                else $ingredientsString .= $_separationString;

                $ingredientsString .= $ingredient->getName() . " (" . $pizzaIngredient->getGrams() . "g)";
            }
        }

        return $ingredientsString;
    }
}
