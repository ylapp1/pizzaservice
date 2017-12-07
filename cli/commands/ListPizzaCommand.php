<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Cli\Commands;

use PizzaService\Propel\Models\PizzaQuery;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Lists all pizzas.
 */
class ListPizzaCommand extends Command
{
    /**
     * Configures the command properties (name, description, help text).
     */
    protected function configure()
    {
        $this->setName('list:pizza')
             ->setDescription('Shows a list of all pizzas.')
             ->setHelp('This command shows a complete list of all pizzas that are currently stored in the database.');
    }

    /**
     * Shows a list of all pizzas including their ingredients.
     *
     * @param InputInterface $input The input interface
     * @param OutputInterface $output The output interface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pizzas = PizzaQuery::create()->orderByOrderCode()
                                      ->find();
        $amountPizzas = count($pizzas);

        if ($amountPizzas == 0) $output->writeln("\nThere are no pizzas yet\n");
        else
        {
            $output->writeln("\nThe available pizzas are:\n");

            foreach ($pizzas as $pizza)
            {
                $output->writeln(" " . $pizza->getOrderCode() . ". " . $pizza->getName());

                foreach ($pizza->getPizzaIngredients() as $pizzaIngredient)
                {
                    if ($pizzaIngredient instanceOf \PizzaService\Propel\Models\PizzaIngredient)
                    {
                        $ingredient = $pizzaIngredient->getIngredient();
                        $output->writeln("  * " . $ingredient->getName() . " (" . $pizzaIngredient->getGrams() . "g)");
                    }
                }

                $output->writeln("");
            }
        }
    }
}