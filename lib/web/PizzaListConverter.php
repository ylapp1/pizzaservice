<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web;

use PizzaService\Lib\IngredientListConverter;

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
        $ingredientListConverter = new IngredientListConverter();
        $outputPizzas = array();

        foreach ($_pizzas as $pizza)
        {
            $amount = 0;
            if ($_amounts) $amount = $_amounts[$pizza->getId()];

            $outputPizza = array(
                "orderCode" => $pizza->getOrderCode(),
                "name" => $pizza->getName(),
                "price" => $pizza->getPrice(),
                "pizzaIngredients" => $ingredientListConverter->pizzaIngredientsToString($pizza->getPizzaIngredients()),
                "amount" => $amount,
                "id" => $pizza->getId()
            );

            $outputPizzas[] = $outputPizza;
        }

        return $outputPizzas;
    }
}
