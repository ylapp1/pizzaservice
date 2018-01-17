<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\PizzaGenerator;

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
     * @return String The random ingredient name
     *
     * @throws \PropelException
     */
    private function getRandomIngredientName($_pizzaIngredients): String
    {
        $counter = 0;
        $randomIndex = rand(0, count($_pizzaIngredients) - 1);
        $ingredientName = "";

        foreach ($_pizzaIngredients as $pizzaIngredient)
        {
            if ($counter == $randomIndex)
            {
                $ingredient = $pizzaIngredient->getIngredient();
                $ingredientName = IngredientTranslationQuery::create()->filterByIngredient($ingredient)
                    ->filterByLanguageCode("it")
                    ->findOne()
                    ->getIngredientName();

                break;
            }

            $counter++;
        }

        return $ingredientName;
    }

    /**
     * Generates a random pizza name from the list of pizza ingredients.
     *
     * @param Pizza $_pizza The pizza object with pizza ingredients already added to it
     *
     * @return String The random pizza name
     *
     * @throws \PropelException
     */
    public function generatePizzaName(Pizza $_pizza)
    {
        $pizzaIngredients = $_pizza->getPizzaIngredients();
        $ingredientName = "Pasta";

        if (count($pizzaIngredients) > 1)
        { // When the pizza contains more ingredients than dough force the use of any ingredient except for "Pasta"

            while ($ingredientName == "Pasta")
            {
                $ingredientName = $this->getRandomIngredientName($pizzaIngredients);
            }
        }

        $pizzaName = $this->getRandomItalianCityName() . " " .
                     $this->getRandomItalianLinkingWord() . " " .
                     $ingredientName . " " .
                     $this->getRandomItalianAdjective();

        return $pizzaName;
    }
}
