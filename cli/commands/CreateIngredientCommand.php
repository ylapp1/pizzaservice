<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
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
        $this->setName('create:ingredient')
             ->setDescription('Adds a new ingredient to the database.')
             ->setHelp('This command allows you to add a new ingredient to the database.');
    }

    /**
     * Asks the user for the ingredient name and inserts it into the database if no entry with that name exists.
     *
     * @param InputInterface $input The input interface
     * @param OutputInterface $output The output interface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper("question");
        $questionIngredientName = new Question("Name of the new ingredient: ");

        $ingredientName = (String)$helper->ask($input, $output, $questionIngredientName);

        if (! $ingredientName) $output->writeln("Error: No ingredient name entered!");
        else
        {
            $ingredient = IngredientQuery::create()->findOneByName($ingredientName);

            if ($ingredient) $output->writeln("Error: An ingredient with that name already exists!");
            else
            {
                $ingredient = new Ingredient();
                $ingredient->setName($ingredientName)
                           ->save();

                $output->writeln("Ingredient '" . $ingredientName . "' was added to the database!");
            }
        }
    }
}