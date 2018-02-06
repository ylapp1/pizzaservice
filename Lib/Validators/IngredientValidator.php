<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Validators;

use PizzaService\Propel\Models\Ingredient;
use PizzaService\Propel\Models\IngredientQuery;

/**
 * Checks whether an ingredient is valid.
 */
class IngredientValidator
{
    /**
     * Checks whether an ingredient is valid.
     *
     * @param Ingredient $_ingredient The ingredient
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    public function validateIngredient(Ingredient $_ingredient)
    {
        $error = $this->validateId($_ingredient->getId());
        if ($error) return $error;

        return false;
    }

    /**
     * Checks whether the ingredient id is valid.
     *
     * @param int $_ingredientId The ingredient id
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    private function validateId(int $_ingredientId = null)
    {
        if (! $_ingredientId) return "Fehler: Ungültige Zutaten ID";

        $ingredient = IngredientQuery::create()->findOneById($_ingredientId);
        if (! $ingredient) return "Fehler: Es existiert keine Zutat mit dieser ID";
        else return false;
    }
}
