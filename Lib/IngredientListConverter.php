<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib;

use PizzaService\Propel\Models\IngredientTranslationQuery;
use PizzaService\Propel\Models\PizzaIngredient;

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
     */
    public function pizzaIngredientsToString($_pizzaIngredients, String $_separationString): String
    {
        $ingredientStrings = array();

        foreach ($_pizzaIngredients as $pizzaIngredient)
        {
            if ($pizzaIngredient instanceOf PizzaIngredient)
            {
                $ingredientId = $pizzaIngredient->getIngredientId();
                $ingredientName = IngredientTranslationQuery::create()->filterByIngredientId($ingredientId)
                                                                      ->filterByLanguageCode("de")
                                                                      ->findOne()
                                                                      ->getIngredientName();

                $ingredientStrings[] = $ingredientName . " (" . $pizzaIngredient->getGrams() . "g)";
            }
        }

        // Sort the ingredients by names
        natsort($ingredientStrings);

        return implode($_separationString, $ingredientStrings);
    }
}
