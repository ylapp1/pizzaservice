<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\App\Controller;

use PizzaService\Lib\Web\App\Controller\Traits\PizzaListConverter;
use PizzaService\Lib\Web\PizzaOrder;
use PizzaService\Propel\Models\OrderPizza;

/**
 * Controller for the pizza order page.
 */
class PizzaOrderController
{
    use PizzaListConverter;

    /**
     * The pizza order.
     *
     * @var PizzaOrder $pizzaOrder
     */
    private $pizzaOrder;

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
     *
     * @throws \PropelException
     */
    public function __construct(\Twig_Environment $_twig)
    {
        $this->pizzaOrder = new PizzaOrder();
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
        if ($_GET["delete"]) $this->pizzaOrder->removeOrderPizza($_GET["delete"]);

        $templateData = array(
            "totalAmountPizzas" => $this->pizzaOrder->getTotalAmountOrderPizzas(),
            "pizzas" => $this->getTemplateArray($this->pizzaOrder->getOrder()),
            "totalPrice" => $this->pizzaOrder->getTotalPrice()
        );

        return $this->twig->render("pizzaOrder.twig", $templateData);
    }

    /**
     * Updates the amount of order pizzas for a single pizza.
     *
     * @return String Empty string or error message
     *
     * @throws \PropelException
     */
    public function changeAmount(): String
    {
        $pizzaId = (int)$_GET["pizza-id"];
        $amount = (int)$_GET["amount"];

        $difference = $amount - $this->pizzaOrder->getAmountOrderPizzasById($pizzaId);

        $orderPizza = new OrderPizza();
        $orderPizza->setPizzaId($pizzaId)
                   ->setAmount($difference);

        $error = $this->pizzaOrder->changeAmountOrderPizzas($orderPizza);
        if ($error) return $error;
        else return "";
    }

    /**
     * Clears the order.
     *
     * @return String Empty string
     *
     * @throws \PropelException
     */
    public function resetOrder(): String
    {
        $this->pizzaOrder->resetOrder();

        return "";
    }
}