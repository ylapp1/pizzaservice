<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\App\Controller\Traits;

use PizzaService\Lib\IngredientListConverter;

/**
 * Converts a Propel Collection of Pizza Objects to an array that can be used to fill the twig templates.
 */
trait PizzaListConverter
{
    /**
     * Generates and returns an array of pizza data for the twig templates.
     *
     * @param \PizzaService\Propel\Models\Pizza[] $_pizzas List of pizzas
     * @param int[] $_amounts List of amounts per pizza
     *
     * @return array The template array
     *
     * @throws \PropelException
     */
    public function getTemplateArray($_pizzas, array $_amounts = null): array
    {
        $ingredientListConverter = new IngredientListConverter();
        $templateArray = array();

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

            $templateArray[] = $outputPizza;
        }

        return $templateArray;
    }
}
