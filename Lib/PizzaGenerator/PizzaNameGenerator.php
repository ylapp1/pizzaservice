<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\PizzaGenerator;

use PizzaService\Propel\Models\IngredientTranslation;
use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\PizzaIngredient;
use PizzaService\Propel\Models\IngredientTranslationQuery;

/**
 * Generates and returns random pizza names partially based on the ingredients of the pizza.
 *
 */
class PizzaNameGenerator
{
    /**
     * Some italian adjectives for the random pizza name
     *
     * @var array $italianAdjectives
     */
    private $italianAdjectives = array(
        // delicious
        "delizioso", "buono", "ghiotto", "squisito",

        // food
        "alimentari",

        // outstanding
        "eccellente", "prelibato", "formidabile", "magnifico",

        // random
        "combinazione", "coincidenza", "caso",

        // eadable
        "godibile", "mangiabile", "commestibile"
    );

    /**
     * Some italian city names for the random pizza name
     * Found the names on https://de.wikipedia.org/wiki/Liste_der_St%C3%A4dte_in_Italien
     *
     * @var array $italianCityNames
     */
    private $italianCityNames = array(
        "Roma", "Milano", "Napoli", "Torino", "Palermo", "Genova", "Bologna", "Firenze", "Bari", "Catania",
        "Venezia", "Verona", "Messina", "Padova", "Trieste", "Taranto", "Brescia", "Parma", "Prato", "Modena"
    );

    /**
     * Some linking words for the random pizza name
     *
     * @var array
     */
    private $italianLinkingWords = array(

        // with
        "con",

        // under
        "sotto",

        // on
        "su",

        // behind
        "dietro",

        // beside, by
        "presso",

        // in
        "a",

        // sitting
        "sedentario",

        // standing
        "in piedi",

        // lying
        "antistante a"

    );


    /**
     * Returns a random italian city name.
     *
     * @return String The italian city name
     */
    private function getRandomItalianCityName(): String
    {
        $randomArrayIndex = rand(0, count($this->italianCityNames) - 1);

        return ($this->italianCityNames[$randomArrayIndex]);
    }

    /**
     * Returns a random italian adjective.
     *
     * @return String The random italian adjective
     */
    private function getRandomItalianAdjective(): String
    {
        $randomArrayIndex = rand(0, count($this->italianAdjectives) - 1);

        return ($this->italianAdjectives[$randomArrayIndex]);
    }

    /**
     * Returns a random italian linking word.
     *
     * @return String The random italian linking word
     */
    private function getRandomItalianLinkingWord(): String
    {
        $randomArrayIndex = rand(0, count($this->italianLinkingWords) - 1);

        return ($this->italianLinkingWords[$randomArrayIndex]);
    }

    /**
     * Returns a random ingredient name from the pizza ingredient list.
     *
     * @param PizzaIngredient[] $_pizzaIngredients The pizza ingredients
     *
     * @return IngredientTranslation|bool The random ingredient translation or false
     */
    private function getRandomIngredientTranslation($_pizzaIngredients): IngredientTranslation
    {
        $counter = 0;
        $randomIndex = rand(0, count($_pizzaIngredients) - 1);

        foreach ($_pizzaIngredients as $pizzaIngredient)
        {
            if ($counter == $randomIndex)
            {
                $ingredientId = $pizzaIngredient->getIngredientId();
                $ingredientTranslation = IngredientTranslationQuery::create()->filterByIngredientId($ingredientId)
                                                                             ->filterByLanguageCode("it")
                                                                             ->findOne();

                return $ingredientTranslation;
            }

            $counter++;
        }

        return false;
    }

    /**
     * Generates a random pizza name from the list of pizza ingredients.
     *
     * @param Pizza $_pizza The pizza object with pizza ingredients already added to it
     * @param array $_defaultIngredientsIds The ids of the default ingredients
     *
     * @return String The random pizza name
     *
     * @throws \PropelException
     */
    public function generatePizzaName(Pizza $_pizza, array $_defaultIngredientsIds)
    {
        $pizzaIngredients = $_pizza->getPizzaIngredients();

        if (count($pizzaIngredients) > count($_defaultIngredientsIds))
        { // When the pizza contains more ingredients than the default ones force the use of any ingredient that is not a default ingredient

            do
            {
                $ingredientTranslation = $this->getRandomIngredientTranslation($pizzaIngredients);
            } while(in_array($ingredientTranslation->getIngredientId(), $_defaultIngredientsIds));
        }
        else $ingredientTranslation = $this->getRandomIngredientTranslation($pizzaIngredients);

        $pizzaName = $this->getRandomItalianCityName() . " " .
                     $this->getRandomItalianLinkingWord() . " " .
                     $ingredientTranslation->getIngredientName() . " " .
                     $this->getRandomItalianAdjective();

        return $pizzaName;
    }
}
