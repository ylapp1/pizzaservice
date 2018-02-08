<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Cli\Commands\ListCommands;

use PizzaService\Propel\Models\Order;
use PizzaService\Propel\Models\OrderQuery;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;

/**
 * Outputs a list of all orders.
 */
class ListOrderCommand extends Command
{
    /**
     * Configures the command properties (name, description, help text).
     */
    protected function configure()
    {
        $this->setName("list:order")
             ->setDescription("Shows a list of all orders.")
             ->setHelp("This command shows a complete list of all orders that are currently stored in the database.")

            ->addOption("include-completed", "a", InputOption::VALUE_NONE, "Shows all orders including completed ones");
    }

    /**
     * Shows a list of all orders.
     *
     * @param InputInterface $_input The input interface
     * @param OutputInterface $_output The output interface
     *
     * @throws \PropelException
     */
    protected function execute(InputInterface $_input, OutputInterface $_output)
    {
        $orderQuery = OrderQuery::create();

        if (! $_input->getOption("include-completed"))
        {
            $orderQuery->filterByCompletedAt(null, \Criteria::EQUAL);
        }

        /** @var Order[] $orders */
        $orders = $orderQuery->orderById()
                             ->find();

        if (count($orders) == 0) $_output->writeln("\nThere are no orders\n");
        else
        {
            $_output->writeln("\nThe current orders are:\n");

            $table = new Table($_output);
            $table->setHeaders(array("Order ID", "Customer Id", "Created at", "Completed at", "Pizzas", "Total"));

            $isFirstRow = true;

            foreach ($orders as $order)
            {
                $pizzaListString = "";
                $totalPrice = 0;

                // Generate the pizza list string
                $isFirstEntry = true;
                foreach ($order->getOrderPizzas() as $orderPizza)
                {
                    if ($isFirstEntry) $isFirstEntry = false;
                    else $pizzaListString .= "\n";

                    $pizzaListString .= $orderPizza->getAmount() . "x " . $orderPizza->getPizza()->getName();

                    $totalPrice += $orderPizza->getPizza()->getPrice() * $orderPizza->getAmount();
                }

                // Initialize the row data
                $row = array(
                    $order->getId(),
                    $order->getCustomerId(),
                    $order->getCreatedAt(),
                    $order->getCompletedAt(),
                    $pizzaListString,
                    number_format($totalPrice, 2) . " €"
                );

                // Insert the table separator
                if ($isFirstRow) $isFirstRow = false;
                else $table->addRow(new TableSeparator());

                // Insert the row into the table
                $table->addRow($row);
            }

            $table->render();
        }
    }
}
