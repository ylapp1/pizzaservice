<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Cli\Commands;

use PizzaService\Propel\Models\IngredientQuery;
use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\PizzaIngredient;
use PizzaService\Propel\Models\PizzaQuery;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\Command;

/**
 * Creates a new pizza.
 */
class CreatePizzaCommand extends Command
{
    /**
     * Configures the command properties (name, description, help text).
     */
    protected function configure()
    {
        $this->setName("create:pizza")
             ->setDescription("Adds a new pizza to the database.")
             ->setHelp("This command allows you to add a new pizza to the database.");
    }

    /**
     * Asks the user for the pizza name and price, then it asks for the ingredients.
     * Finally it inserts the Pizza into the database if no pizza with that name exists.
     *
     * @param InputInterface $_input The input interface
     * @param OutputInterface $_output The output interface
     *
     * @throws \Exception
     * @throws \PropelException
     */
    protected function execute(InputInterface $_input, OutputInterface $_output)
    {
        $_output->writeln("\nCreate a new Pizza:\n");

        $pizzaName = $this->promptPizzaProperty($_input, $_output, "Name", "name", "String", true);
        if (! $pizzaName) return;

        $pizzaPrice = $this->promptPizzaProperty($_input, $_output, "Price", "price", "float",  false);
        if (! $pizzaPrice) return;

        $pizzaOrderCode = $this->promptPizzaProperty($_input, $_output, "OrderCode", "order code", "int", true);
        if (! $pizzaOrderCode) return;

        $pizza = new Pizza();
        $pizza->setName($pizzaName)
              ->setPrice($pizzaPrice)
              ->setOrderCode($pizzaOrderCode);


        $ingredientNames = (array)IngredientQuery::create()->select(array("name"))
                                                           ->orderByName()
                                                           ->find();

        $pizza = $this->promptIngredients($_input, $_output, $pizza, $ingredientNames);
        if (! $pizza) return;
        else
        {
            $pizza->save();
            $_output->writeln("Pizza '" . $pizzaName . "' was added to the database!");
        }
    }

    /**
     * Prompts the user to input a pizza property and returns the inputted value or false if the property was invalid.
     * Checks whether the input value is empty and whether an entry with that property value exists (if $_isUnique is true)
     *
     * @param InputInterface $_input The input interface
     * @param OutputInterface $_output The output interface
     * @param String $_columnName The column name of the property in the database
     * @param String $_displayProperty The property name that will be displayed
     * @param String $_propertyDataType Data type of the column
     * @param bool $_isUnique Indicates that this column is unique, the function will return false if a pizza with the inputted property value exists
     *
     * @return bool|String false or input value
     */
    private function promptPizzaProperty(InputInterface $_input, OutputInterface $_output, $_columnName, $_displayProperty, $_propertyDataType, $_isUnique)
    {
        $helper = $this->getHelper("question");
        $question = new Question(ucfirst($_displayProperty) . ": ");

        $propertyValue = $helper->ask($_input, $_output, $question);

        // Convert the input to the column data type
        settype($propertyValue, $_propertyDataType);

        if (! $propertyValue)
        {
            $_output->writeln("Error: Invalid value entered for the pizza " . $_displayProperty . "!");
            return false;
        }

        if ($_isUnique)
        {
            $pizza = PizzaQuery::create()->filterBy($_columnName, $propertyValue)
                                         ->findOne();
            if ($pizza)
            {
                $_output->writeln("Error: A pizza with the " . $_displayProperty . " '" . $propertyValue . "' already exists!");
                return false;
            }
        }

        return $propertyValue;
    }

    /**
     * Prompts the user to input the pizza ingredients and fills a Pizza object with user selected ingredients.
     *
     * @param InputInterface $_input The input interface
     * @param OutputInterface $_output The output interface
     * @param Pizza $_pizza The Pizza object to which the selected ingredients will be added
     * @param String[] $_ingredientNames The ingredient names
     *
     * @return Pizza|bool The updated Pizza object or false
     *
     * @throws \PropelException
     */
    private function promptIngredients(InputInterface $_input, OutputInterface $_output, Pizza $_pizza, array $_ingredientNames)
    {
        $helper = $this->getHelper("question");

        $questionIngredients = new ChoiceQuestion(
            "Select the pizza ingredients (separated by commas)",
            $_ingredientNames,
            0
        );
        $questionIngredients->setMultiselect(true);

        $selectedIngredients = $helper->ask($_input, $_output, $questionIngredients);
        if (! $selectedIngredients)
        {
            $_output->writeln("Error: A pizza must contain at least one ingredient!");
            return false;
        }

        // Removes any duplicated ingredients in the list of selected ingredients
        $selectedIngredients = array_unique($selectedIngredients);

        foreach ($selectedIngredients as $selectedIngredient)
        {
            $ingredient = IngredientQuery::create()->findOneByName($selectedIngredient);
            $amountGrams = 0;

            while (! $amountGrams)
            {
                $questionGrams = new Question("How many grams of '" . $selectedIngredient . "': ");
                $ingredientGrams = (float)$helper->ask($_input, $_output, $questionGrams);

                if (! $ingredientGrams) $_output->writeln("Error: Invalid amount of grams for '" . $selectedIngredient . "' entered!");
                else $amountGrams = $ingredientGrams;
            }

            $pizzaIngredient = new PizzaIngredient();
            $pizzaIngredient->setGrams($amountGrams)
                            ->setIngredient($ingredient);
            $_pizza->addPizzaIngredient($pizzaIngredient);
        }

        return $_pizza;
    }
}
