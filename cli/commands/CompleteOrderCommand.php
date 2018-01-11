<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Cli\Commands;

use PizzaService\Propel\Models\OrderQuery;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Completes an order, that was not completed yet.
 */
class CompleteOrderCommand extends Command
{
    /**
     * Configures the command properties (name, description, help text).
     */
    protected function configure()
    {
        $this->setName("complete:order")
            ->setDescription("Completes an uncompleted order.")
            ->setHelp("This command completes an order if it is uncompleted.")

            ->addArgument("order-id", InputArgument::REQUIRED, "The id of the order");
    }

    /**
     * Completes an uncompleted order.
     *
     * @param InputInterface $_input The input interface
     * @param OutputInterface $_output The output interface
     *
     * @throws \Exception
     * @throws \PropelException
     */
    protected function execute(InputInterface $_input, OutputInterface $_output)
    {
        $orderId = $_input->getArgument("order-id");
        $order = OrderQuery::create()->findOneById($orderId);

        if (! $order)
        {
            $_output->writeln("\nError: No order with that order-id exists!\n");
            return;
        }

        if ($order->getCompletedAt() !== null)
        {
            $_output->writeln("\nError: This order has already been completed!\n");
            return;
        }

        $order->setCompletedAt(time())
              ->save();
        $_output->writeln("\nOrder successfully completed!\n");
    }
}
