<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Cli\Commands\ListCommands;

use PizzaService\Propel\Models\Customer;
use PizzaService\Propel\Models\CustomerQuery;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Shows a list of all customers
 */
class ListCustomerCommand extends Command
{
    /**
     * Configures the command properties (name, description, help text).
     */
    protected function configure()
    {
        $this->setName("list:customer")
             ->setDescription("Shows a list of all customers.")
             ->setHelp("This command shows a complete list of all customers that are currently stored in the database.");
    }

    /**
     * Shows a list of all customers ordered by the customers last names.
     *
     * @param InputInterface $_input The input interface
     * @param OutputInterface $_output The output interface
     *
     * @throws \PropelException
     */
    protected function execute(InputInterface $_input, OutputInterface $_output)
    {
        /** @var Customer[] $customers */
        $customers = CustomerQuery::create()->orderById()->find();

        if (count($customers) == 0) $_output->writeln("\nThere are no customers yet\n");
        else
        {
            $_output->writeln("\nThe customers are:\n");

            $table = new Table($_output);
            $table->setHeaders(array("Id", "Last Name", "First name", "Country", "Zip", "City", "Street", "House number"));

            $isFirstRow = true;

            $customersData = array();

            foreach ($customers as $customer)
            {
                $customerData = array(
                    "id" => $customer->getId(),
                    "firstName" => $customer->getCustomerName()->getFirstName()->getName(),
                    "lastName" => $customer->getCustomerName()->getLastName()->getName(),
                    "country" => $customer->getCustomerAddress()->getCountry()->getName(),
                    "zip" => $customer->getCustomerAddress()->getCity()->getZip()->getZip(),
                    "city" => $customer->getCustomerAddress()->getCity()->getCityName()->getName(),
                    "street" => $customer->getCustomerAddress()->getStreet()->getStreetName()->getName(),
                    "houseNumber" => $customer->getCustomerAddress()->getStreet()->getHouseNumber()->getNumber(),
                );

                $customersData[] = $customerData;
            }

            usort($customersData, function(array $_customerA, array $_customerB){
                return strnatcmp($_customerA["lastName"], $_customerB["lastName"]);
            });

            foreach ($customersData as $customerData)
            {
                $row = array(
                    $customerData["id"],
                    $customerData["lastName"],
                    $customerData["firstName"],
                    $customerData["country"],
                    $customerData["zip"],
                    $customerData["city"],
                    $customerData["street"],
                    $customerData["houseNumber"]
                );

                if ($isFirstRow) $isFirstRow = false;
                else $table->addRow(new TableSeparator());

                $table->addRow($row);
            }

            $table->render();
        }
    }
}