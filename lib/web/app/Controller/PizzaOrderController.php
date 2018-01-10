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

/**
 * Controller for the pizza order page.
 */
class PizzaOrderController
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
     * Returns the pizza order HTML code.
     *
     * @return String The pizza order HTML code
     *
     * @throws \PropelException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showPizzaOrder(): String
    {
        if ($_GET["delete"]) $this->pizzaOrderHandler->removePizza($_GET["delete"]);

        $templateData = array(
            "totalAmountPizzas" => $this->pizzaOrderHandler->getTotalAmountOrderPizzas(),
            "pizzas" => $this->getTemplateArray($this->pizzaOrderHandler->getPizzas(), $this->pizzaOrderHandler->getOrder()),
            "totalPrice" => $this->pizzaOrderHandler->getTotalPrice()
        );

        return $this->twig->render("pizzaOrder.twig", $templateData);
    }

    /**
     * Updates the amount of order pizzas for a single pizza.
     *
     * @return String Empty string or error message
     */
    public function changeAmount(): String
    {
        $pizzaId = (int)$_GET["pizza-id"];
        $amount = (int)$_GET["amount"];

        $difference = $amount - $this->pizzaOrderHandler->getAmountOrderPizzas($pizzaId);

        $error = $this->pizzaOrderHandler->changeAmountPizzas($pizzaId, $difference);

        if ($error) return $error;
        else return "";
    }
}
