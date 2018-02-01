<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\App\Controller;

use Criteria;
use PizzaService\Lib\ConfigLoader;
use PizzaService\Lib\PizzaGenerator\PizzaGenerator;
use PizzaService\Lib\Web\App\Controller\Traits\PizzaListConverter;
use PizzaService\Lib\Web\PizzaOrder;
use PizzaService\Lib\Web\RandomPizza;
use PizzaService\Propel\Models\IngredientQuery;
use PizzaService\Propel\Models\IngredientTranslationQuery;
use PizzaService\Propel\Models\OrderPizza;

/**
 * Controller for the pizza generator page.
 */
class PizzaGeneratorController
{
    use PizzaListConverter;

    /**
     * The config loader
     *
     * @var ConfigLoader $configLoader
     */
    private $configLoader;

    /**
     * The random pizza generator
     *
     * @var PizzaGenerator $pizzaGenerator
     */
    private $pizzaGenerator;

    /**
     * The pizza order session handler
     *
     * @var PizzaOrder $pizzaOrder
     */
    private $pizzaOrder;

    /**
     * The current random pizza
     *
     * @var RandomPizza $randomPizza
     */
    private $randomPizza;

    /**
     * The template renderer
     *
     * @var \Twig_Environment $twig
     */
    private $twig;


    /**
     * PizzaMenuCardController constructor.
     *
     * @param \Twig_Environment $_twig The template renderer
     * @param ConfigLoader $_configLoader
     *
     * @throws \PropelException
     */
    public function __construct(\Twig_Environment $_twig, ConfigLoader $_configLoader)
    {
        $this->configLoader = $_configLoader;

        $pizzaGeneratorConfig = $_configLoader->getConfigValue("randomPizza");
        $defaultIngredientsData = $pizzaGeneratorConfig["defaultIngredients"];
        $maxTotalWeight = (float)$pizzaGeneratorConfig["maxTotalWeight"];
        $minPrice = (float)$pizzaGeneratorConfig["pizzaPriceRange"]["minPrice"];
        $maxPrice = (float)$pizzaGeneratorConfig["pizzaPriceRange"]["maxPrice"];
        $minGrams = (int)$pizzaGeneratorConfig["ingredientsGramsRange"]["minGrams"];
        $maxGrams = (int)$pizzaGeneratorConfig["ingredientsGramsRange"]["maxGrams"];

        $this->pizzaGenerator = new PizzaGenerator($defaultIngredientsData, $maxTotalWeight, $minPrice, $maxPrice, $minGrams, $maxGrams);
        $this->pizzaOrder = new PizzaOrder();
        $this->randomPizza = new RandomPizza();
        $this->twig = $_twig;
    }


    /**
     * Returns the pizza generator HTML code.
     *
     * @return String The pizza generator HTML code
     *
     * @throws \PropelException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showPizzaGenerator(): String
    {
        $pizzaGeneratorConfig = $this->configLoader->getConfigValue("randomPizza");

        $ingredientTranslationQuery = IngredientTranslationQuery::create()->filterByLanguageCode("de");
        foreach ($pizzaGeneratorConfig["defaultIngredients"] as $defaultIngredientData)
        {
            $ingredientTranslationQuery->filterByIngredientId($defaultIngredientData["id"], Criteria::NOT_EQUAL);
        }
        $ingredients = $ingredientTranslationQuery->find();

        $this->randomPizza->fromSession();

        $templateData = array(
            "ingredients" => $ingredients,
            "totalAmountPizzas" => $this->pizzaOrder->getTotalAmountOrderPizzas(),
            "pizzas" => $this->getTemplateArray(array($this->randomPizza->pizza()))
        );

        return $this->twig->render("pizzaGenerator.twig", $templateData);
    }

    /**
     * Generates a random pizza and stores it in the $_SESSION variable.
     *
     * @return String The random pizza data table HTML
     *
     * @throws \PropelException
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function generatePizza(): String
    {
        $ingredientIds = json_decode($_GET["ingredientIds"]);
        $ingredients = IngredientQuery::create()->findById($ingredientIds);

        $pizza = $this->pizzaGenerator->generatePizza($ingredients, $this->pizzaOrder);
        $this->randomPizza->setPizza($pizza);

        // Return only the rendered pizza table from the pizzaGenerator page template
        $templateData = array(
            "pizzas" => $this->getTemplateArray(array($pizza)),
        );
        $template = $this->twig->load("pizzaGenerator.twig");

        return $template->renderBlock("pizzaData", $templateData);
    }

    /**
     * Adds an amount of pizzas to an order.
     *
     * @return String Error message or empty string
     *
     * @throws \PropelException
     */
    public function addRandomPizzaToOrder()
    {
        $amount = (int)$_GET["amount"];

        $orderPizza = new OrderPizza();
        $orderPizza->setPizza($this->randomPizza->pizza())
                   ->setAmount($amount);

        $error = $this->pizzaOrder->addOrderPizza($orderPizza);
        if ($error) return $error;
        else return "";
    }
}
