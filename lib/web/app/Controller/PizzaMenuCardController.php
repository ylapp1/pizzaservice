<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\App\Controller;

use PizzaService\Lib\Web\App\Controller\Traits\PizzaListConverter;
use PizzaService\Lib\Web\PizzaOrderSession\PizzaOrderHandler;
use PizzaService\Propel\Models\PizzaQuery;

/**
 * Controller for the pizza menu card page.
 */
class PizzaMenuCardController
{
    use PizzaListConverter;

    /**
     * The pizza order handler.
     *
     * @var PizzaOrderHandler $pizzaOrderHandler
     */
    private $pizzaOrderHandler;

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
     */
    public function __construct(\Twig_Environment $_twig)
    {
        $this->pizzaOrderHandler = new PizzaOrderHandler();
        $this->twig = $_twig;
    }


    /**
     * Returns the pizza menu card HTML code.
     *
     * @return String The pizza menu card HTML code
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showPizzaMenuCard(): String
    {
        $pizzas = PizzaQuery::create()->orderByOrderCode()
                                      ->find();

        return $this->twig->render("pizzaMenuCard.twig",
            array(
                "totalAmountPizzas" => $this->pizzaOrderHandler->getTotalAmountOrderPizzas(),
                "pizzas" => $this->getTemplateArray($pizzas)
            )
        );
    }

    /**
     * Adds an amount of pizzas to an order.
     *
     * @return String Error message or empty string
     */
    public function addPizzaToOrder(): String
    {
        $pizzaId = (int)$_GET["pizza-id"];
        $amount = (int)$_GET["amount"];

        $error = $this->pizzaOrderHandler->changeAmountPizzas($pizzaId, $amount);
        if ($error) return $error;
        else return "";
    }
}