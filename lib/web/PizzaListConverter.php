<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web;

/**
 * Converts a Propel Collection of Pizza Objects to an array that can be used to fill the twig template.
 */
class PizzaListConverter
{
    /**
     * Generates and returns the associative array for the twig template.
     *
     * @param \PizzaService\Propel\Models\Pizza[] $_pizzas List of pizzas
     * @param int[] $_amounts List of amounts per pizza
     *
     * @return array
     */
    public function getPizzaList($_pizzas, array $_amounts = null): array
    {
        $outputPizzas = array();

        foreach ($_pizzas as $pizza)
        {
            $amount = 0;
            if ($_amounts) $amount = $_amounts[$pizza->getId()];

            $outputPizza = array(
                "orderCode" => $pizza->getOrderCode(),
                "name" => $pizza->getName(),
                "price" => number_format($pizza->getPrice(), 2) . " €",
                "pizzaIngredients" => $this->generateIngredientsString($pizza->getPizzaIngredients()),
                "amount" => $amount,
                "id" => $pizza->getId()
            );

            $outputPizzas[] = $outputPizza;
        }

        return $outputPizzas;
    }

    /**
     * Creates a comma separated string from a list of pizza ingredients.
     *
     * @param \PizzaService\Propel\Models\PizzaIngredient[] $_ingredients List of ingredients
     *
     * @return String The list of ingredients as a string
     */
    private function generateIngredientsString($_ingredients): String
    {
        $ingredientsString = "";
        $isFirstEntry = true;

        foreach ($_ingredients as $pizzaIngredient)
        {
            if ($pizzaIngredient instanceOf \PizzaService\Propel\Models\PizzaIngredient)
            {
                $ingredient = $pizzaIngredient->getIngredient();

                if ($isFirstEntry) $isFirstEntry = false;
                else $ingredientsString .= ", ";

                $ingredientsString .= $ingredient->getName() . " (" . $pizzaIngredient->getGrams() . "g)";
            }
        }

        return $ingredientsString;
    }
}
