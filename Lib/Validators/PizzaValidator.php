<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Validators;

use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\PizzaIngredient;
use PizzaService\Propel\Models\PizzaQuery;

/**
 * Checks whether a Pizza object is valid.
 */
class PizzaValidator
{
    /**
     * Checks whether a pizza with the name, order code or ingredients/grams combination already exists in the database.
     *
     * @param Pizza $_pizza The pizza
     * @param bool $_hasId Indicates whether the pizza object has an id
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    public function validatePizza(Pizza $_pizza, bool $_hasId)
    {
        if ($_hasId)
        {
            $error = $this->validateId($_pizza->getId());
            if ($error) return $error;
        }
        else
        {
            $error = $this->validateName($_pizza->getName());
            if ($error) return $error;

            $error = $this->validateOrderCode($_pizza->getOrderCode());
            if ($error) return $error;

            $error = $this->validatePizzaIngredients($_pizza->getPizzaIngredients());
            if ($error) return $error;
        }

        return false;
    }

    /**
     * Checks whether the pizza id exists in the database.
     *
     * @param int $_pizzaId The pizza id
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    private function validateId(int $_pizzaId = null)
    {
        if (! $_pizzaId) return "Fehler: Ungültige Pizza Id";

        $pizza = PizzaQuery::create()->findOneById($_pizzaId);
        if (! $pizza) return "Fehler: Es existiert keine Pizza mit dieser Id";
        else return false;
    }

    /**
     * Checks whether the pizza name is unique.
     *
     * @param String $_pizzaName The pizza name
     *
     * @return String|bool Error message or false
     */
    private function validateName(String $_pizzaName = null)
    {
        if (! $_pizzaName) return "Fehler: Ungültiger Pizza Name";

        $pizza = PizzaQuery::create()->findOneByName($_pizzaName);
        if ($pizza) return "Fehler: Eine Pizza mit dem Namen " . $_pizzaName . " existiert bereits in der Datenbank.";
        else return false;
    }

    /**
     * Checks whether the pizza order code is unique.
     *
     * @param String $_pizzaOrderCode The pizza order code
     *
     * @return String|bool Error message or false
     */
    private function validateOrderCode(String $_pizzaOrderCode = null)
    {
        if (! $_pizzaOrderCode) return "Fehler: Ungültige Pizza Bestellnummer";

        $pizza = PizzaQuery::create()->findOneByOrderCode($_pizzaOrderCode);
        if ($pizza) return "Fehler: Eine Pizza mit der Bestellnummer " . $_pizzaOrderCode . " existiert bereits in der Datenbank";
        else return false;
    }

    /**
     * Checks whether the pizza ingredient/grams combination is unique.
     *
     * @param PizzaIngredient[] $_pizzaIngredients The pizza ingredients
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    private function validatePizzaIngredients($_pizzaIngredients = null)
    {
        if (! $_pizzaIngredients) return "Fehler: Die Pizza hat keine Zutaten";

        $ingredientValidator = new IngredientValidator();

        $pizzaQuery = PizzaQuery::create();

        foreach ($_pizzaIngredients as $pizzaIngredient)
        {
            $error = $ingredientValidator->validateIngredient($pizzaIngredient->getIngredient());
            if ($error) return $error;

            $pizzaQuery->usePizzaIngredientQuery()
                           ->filterByIngredientId($pizzaIngredient->getIngredientId())
                           ->filterByGrams($pizzaIngredient->getGrams())
                       ->endUse();
        }

        $pizza = $pizzaQuery->findOne();
        if ($pizza) return "Fehler: Eine Pizza mit dieser Zutaten/Menge Kombination existiert bereits in der Datenbank";
        else return false;
    }
}
