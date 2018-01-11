<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Cli\Commands;

use PizzaService\Propel\Models\Ingredient;
use PizzaService\Propel\Models\IngredientQuery;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\Command;

/**
 * Creates a new ingredient.
 */
class CreateIngredientCommand extends Command
{
    /**
     * Configures the command properties (name, description, help text).
     */
    protected function configure()
    {
        $this->setName("create:ingredient")
             ->setDescription("Adds a new ingredient to the database.")
             ->setHelp("This command allows you to add a new ingredient to the database.");
    }

    /**
     * Asks the user for the ingredient name and inserts it into the database if no entry with that name exists.
     *
     * @param InputInterface $_input The input interface
     * @param OutputInterface $_output The output interface
     *
     * @throws \Exception
     * @throws \PropelException
     */
    protected function execute(InputInterface $_input, OutputInterface $_output)
    {
        $helper = $this->getHelper("question");
        $questionIngredientName = new Question("Name of the new ingredient: ");

        $ingredientName = (String)$helper->ask($_input, $_output, $questionIngredientName);

        if (! $ingredientName) $_output->writeln("Error: No ingredient name entered!");
        else
        {
            $ingredient = IngredientQuery::create()->findOneByName($ingredientName);

            if ($ingredient) $_output->writeln("Error: An ingredient with that name already exists!");
            else
            {
                $ingredient = new Ingredient();
                $ingredient->setName($ingredientName)
                           ->save();

                $_output->writeln("Ingredient '" . $ingredientName . "' was added to the database!");
            }
        }
    }
}
