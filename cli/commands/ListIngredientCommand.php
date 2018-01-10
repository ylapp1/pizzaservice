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
use Symfony\Component\Console\Command\Command;

/**
 * Shows a list of all ingredients.
 */
class ListIngredientCommand extends Command
{
    /**
     * Configures the command properties (name, description, help text).
     */
    protected function configure()
    {
        $this->setName("list:ingredient")
             ->setDescription("Shows a list of all ingredients.")
             ->setHelp("This command shows a complete list of all ingredients that are currently stored in the database.");
    }

    /**
     * Shows a list of all ingredients.
     *
     * @param InputInterface $_input The input interface
     * @param OutputInterface $_output The output interface
     */
    protected function execute(InputInterface $_input, OutputInterface $_output)
    {
        /** @var Ingredient[] $ingredients */
        $ingredients = IngredientQuery::create()->orderByName()
                                                ->find();
        $amountIngredients = count($ingredients);

        if ($amountIngredients == 0) $_output->writeln("\nThere are no ingredients yet\n");
        else
        {
            $_output->writeln("\nThe available ingredients are:\n");

            for ($i = 0; $i < $amountIngredients; $i++)
            {
                $_output->writeln(" " . ($i + 1) . ". " . $ingredients[$i]->getName());
            }

            $_output->writeln("");
        }
    }
}
