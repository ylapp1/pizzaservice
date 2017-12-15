<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\PizzaListRenderers;

/**
 * Generates and returns an output string from a list of Pizza objects.
 * Parent class for PizzaListMenuCardRenderer and PizzaListOrderRenderer
 */
class PizzaListRenderer
{
    /**
     * Content of the pizza table template
     *
     * @var String $pizzaTableTemplate
     */
    private $pizzaTableTemplate;

    /**
     * Content of the pizza table row template
     *
     * @var String $pizzaTableRowTemplate
     */
    private $pizzaTableRowTemplate;

    /**
     * Content of the template for the button for each pizza.
     *
     * @var String $actionButtonTemplate
     */
    private $actionButtonTemplate;


    /**
     * PizzaListRenderer constructor.
     *
     * @param String $_actionButtonTemplatePath Path to the template file for the action button
     * @param String $_actionButtonColumnTitle Title of the column that contains the action buttons per pizza
     */
    protected function __construct(String $_actionButtonTemplatePath, String $_actionButtonColumnTitle)
    {
        $this->pizzaTableTemplate = str_replace("{%ACTION_BUTTON_COLUMN_TITLE%}",
            $_actionButtonColumnTitle,
            file_get_contents(__DIR__ . "/../../../web/templates/PizzaList/pizzaTable.html")
        );
        $this->pizzaTableRowTemplate = file_get_contents(__DIR__ . "/../../../web/templates/PizzaList/pizzaTableRow.html");
        $this->actionButtonTemplate = file_get_contents($_actionButtonTemplatePath);
    }


    /**
     * Generates and returns a table from a list of pizzas.
     *
     * @param \PizzaService\Propel\Models\Pizza[] $_pizzas List of pizzas
     * @param String $_listEmptyString String that will be printed if the list of pizzas is empty
     * @param int[] $_amounts List of amounts per pizza
     *
     * @return String HTML table
     */
    public function renderPizzaList($_pizzas, String $_listEmptyString, array $_amounts = null): String
    {
        $search = array("search", "{%ORDER_CODE%}", "{%PIZZA_NAME%}", "{%PIZZA_PRICE%}", "{%INGREDIENTS%}", "{%ACTION_BUTTON%}", "{%AMOUNT%}");
        $pizzaTableContent = "";

        if (count($_pizzas) == 0) $pizzaTableContent = "<tr><td colspan=\"5\" class=\"text-center\">" . $_listEmptyString . "</td></tr>";

        foreach ($_pizzas as $pizza)
        {
            $amount = 0;

            if ($_amounts) $amount = $_amounts[$pizza->getId()];

            $actionButton = str_replace("{%PIZZA_ID%}", $pizza->getId(), $this->actionButtonTemplate);

            $replace = array("replace",
                $pizza->getOrderCode(),
                $pizza->getName(),
                number_format($pizza->getPrice(), 2) . " €",
                $this->generateIngredientsString($pizza->getPizzaIngredients()),
                $actionButton,
                $amount);
            $pizzaTableContent .= str_replace($search, $replace, $this->pizzaTableRowTemplate);
        }

        return str_replace("{%CONTENT%}", $pizzaTableContent, $this->pizzaTableTemplate);
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
