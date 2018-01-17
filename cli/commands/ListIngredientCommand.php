<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Cli\Commands;

use PizzaService\Propel\Models\IngredientTranslation;
use PizzaService\Propel\Models\IngredientTranslationQuery;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
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
        /** @var IngredientTranslation[] $ingredients */
        $ingredients = IngredientTranslationQuery::create()->filterByLanguageCode("de")
                                                           ->orderByIngredientName()
                                                           ->find();

        if (count($ingredients) == 0) $_output->writeln("\nThere are no ingredients yet\n");
        else
        {
            $_output->writeln("\nThe available ingredients are:\n");

            $table = new Table($_output);
            $table->setHeaders(array("Id", "Ingredient name"));

            $isFirstRow = true;

            foreach ($ingredients as $ingredient)
            {
                $row = array(
                    $ingredient->getId(),
                    $ingredient->getIngredientName()
                );

                if ($isFirstRow) $isFirstRow = false;
                else $table->addRow(new TableSeparator());

                $table->addRow($row);
            }

            $table->render();
        }
    }
}
