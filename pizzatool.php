<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$loader = require_once(__DIR__ . "/vendor/autoload.php");
$loader->addPsr4("PizzaService\\", __DIR__);

// Initialize Propel with the runtime configuration
Propel::init(__DIR__ . "/Propel/Conf/pizza_service-conf.php");

use PizzaService\Cli\Commands\CompleteOrderCommand;
use PizzaService\Cli\Commands\CreateCommands\CreateIngredientCommand;
use PizzaService\Cli\Commands\CreateCommands\CreatePizzaCommand;
use PizzaService\Cli\Commands\ListCommands\ListCustomerCommand;
use PizzaService\Cli\Commands\ListCommands\ListIngredientCommand;
use PizzaService\Cli\Commands\ListCommands\ListOrderCommand;
use PizzaService\Cli\Commands\ListCommands\ListPizzaCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->addCommands(
    array(
        new CompleteOrderCommand(),
        new CreateIngredientCommand(),
        new CreatePizzaCommand(),
        new ListCustomerCommand(),
        new ListIngredientCommand(),
        new ListOrderCommand(),
        new ListPizzaCommand()
    )
);
$application->run();
