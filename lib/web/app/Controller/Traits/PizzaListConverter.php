<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\App\Controller\Traits;

use PizzaService\Lib\IngredientListConverter;
use PizzaService\Propel\Models\OrderPizza;
use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\PizzaIngredientQuery;

/**
 * Converts a Propel Collection of Pizza Objects to an array that can be used to fill the twig templates.
 */
trait PizzaListConverter
{
    /**
     * Generates and returns an array of pizza data for the twig templates.
     *
     * @param Pizza[]|OrderPizza[] $_pizzas List of pizzas
     *
     * @return array The template array
     *
     * @throws \PropelException
     */
    public function getTemplateArray($_pizzas): array
    {
        $templateArray = array();

        foreach ($_pizzas as $pizza)
        {
            if ($pizza instanceof OrderPizza) $templateArray[] = $this->orderPizzaToTemplateArray($pizza);
            elseif ($pizza instanceof Pizza) $templateArray[] = $this->pizzaToTemplateArray($pizza);
        }

        return $templateArray;
    }

    /**
     * Converts a pizza to an array with template data.
     *
     * @param Pizza $_pizza The pizza
     * @param int $_amount The amount
     *
     * @return array The pizza data
     *
     * @throws \PropelException
     */
    private function pizzaToTemplateArray(Pizza $_pizza, int $_amount = 0)
    {
        $ingredientListConverter = new IngredientListConverter();


        $pizzaIngredientIds = array();
        foreach ($_pizza->getIngredients() as $pizzaIngredient)
        {
            $pizzaIngredientIds[] = $pizzaIngredient->getId();
        }

        $pizzaIngredients = PizzaIngredientQuery::create()->filterById($pizzaIngredientIds)
                                                          ->useIngredientQuery()
                                                          ->useIngredientTranslationQuery()
                                                          ->filterByLanguageCode("de")
                                                          ->orderByIngredientName()
                                                          ->endUse()
                                                          ->endUse()
                                                          ->find();

        $outputPizza = array(
            "orderCode" => $_pizza->getOrderCode(),
            "name" => $_pizza->getName(),
            "price" => $_pizza->getPrice(),
            "pizzaIngredients" => $ingredientListConverter->pizzaIngredientsToString($pizzaIngredients, "\n"),
            "amount" => $_amount,
            "id" => $_pizza->getId()
        );

        return $outputPizza;
    }

    /**
     * Converts an order pizza to an array with template data.
     *
     * @param OrderPizza $_orderPizza The order pizza
     *
     * @return array The pizza data
     *
     * @throws \PropelException
     */
    private function orderPizzaToTemplateArray(OrderPizza $_orderPizza)
    {
        return $this->pizzaToTemplateArray($_orderPizza->getPizza(), $_orderPizza->getAmount());
    }
}
