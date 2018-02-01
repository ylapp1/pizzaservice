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
use PizzaService\Cli\Commands\CreateIngredientCommand;
use PizzaService\Cli\Commands\CreatePizzaCommand;
use PizzaService\Cli\Commands\ListIngredientCommand;
use PizzaService\Cli\Commands\ListOrderCommand;
use PizzaService\Cli\Commands\ListPizzaCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->addCommands(
    array(
        new CompleteOrderCommand(),
        new CreateIngredientCommand(),
        new CreatePizzaCommand(),
        new ListIngredientCommand(),
        new ListOrderCommand(),
        new ListPizzaCommand()
    )
);
$application->run();
