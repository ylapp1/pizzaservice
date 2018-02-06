<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Cli\Commands;

use Criteria;
use PizzaService\Lib\IngredientListConverter;
use PizzaService\Lib\PizzaListConverterTrait;
use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\PizzaQuery;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Lists all pizzas.
 */
class ListPizzaCommand extends Command
{
    use PizzaListConverterTrait;

    /**
     * Configures the command properties (name, description, help text).
     */
    protected function configure()
    {
        $this->setName("list:pizza")
             ->setDescription("Shows a list of all pizzas.")
             ->setHelp("This command shows a complete list of all pizzas that are currently stored in the database.")

             ->addOption("include-generated", "a", InputOption::VALUE_NONE, "Shows all pizzas including generated ones");
    }

    /**
     * Shows a list of all pizzas including their ingredients.
     *
     * @param InputInterface $_input The input interface
     * @param OutputInterface $_output The output interface
     *
     * @throws \PropelException
     */
    protected function execute(InputInterface $_input, OutputInterface $_output)
    {
        /** @var Pizza[] $pizzas */
        $pizzaQuery = PizzaQuery::create();

        if (! $_input->getOption("include-generated"))
        {
            $pizzaQuery->filterByOrderCode("G%", Criteria::NOT_LIKE);
        }

        $pizzas = $pizzaQuery->orderByOrderCode()
                             ->find();

        if (count($pizzas) == 0) $_output->writeln("\nThere are no pizzas yet\n");
        else
        {
            $pizzaDataArrays = $this->getTemplateArray($pizzas);

            $_output->writeln("\nThe available pizzas are:\n");

            $table = new Table($_output);
            $table->setHeaders(array("Order Code", "Pizza Name", "Price", "Ingredients"));

            $isFirstRow = true;

            foreach ($pizzaDataArrays as $pizzaDataArray)
            {
                $row = array(
                    $pizzaDataArray["orderCode"],
                    $pizzaDataArray["name"],
                    number_format($pizzaDataArray["price"], 2) . " €",
                    $pizzaDataArray["pizzaIngredients"]
                );

                if ($isFirstRow) $isFirstRow = false;
                else $table->addRow(new TableSeparator());

                $table->addRow($row);
            }

            $table->render();
        }
    }
}
