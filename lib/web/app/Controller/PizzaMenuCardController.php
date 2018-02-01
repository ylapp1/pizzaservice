<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\App\Controller;

use Criteria;
use PizzaService\Lib\Web\App\Controller\Traits\PizzaListConverter;
use PizzaService\Lib\Web\PizzaOrder;
use PizzaService\Propel\Models\OrderPizza;
use PizzaService\Propel\Models\PizzaQuery;

/**
 * Controller for the pizza menu card page.
 */
class PizzaMenuCardController
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
     * Returns the pizza menu card HTML code.
     *
     * @return String The pizza menu card HTML code
     *
     * @throws \PropelException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showPizzaMenuCard(): String
    {
        $pizzas = PizzaQuery::create()->filterByOrderCode("G%", Criteria::NOT_LIKE)
                                      ->orderByOrderCode()
                                      ->find();

        return $this->twig->render("pizzaMenuCard.twig",
            array(
                "totalAmountPizzas" => $this->pizzaOrder->getTotalAmountOrderPizzas(),
                "pizzas" => $this->getTemplateArray($pizzas)
            )
        );
    }

    /**
     * Adds an amount of pizzas to an order.
     *
     * @return String Error message or empty string
     *
     * @throws \PropelException
     */
    public function addPizzaToOrder(): String
    {
        $pizzaOrderCode = (int)$_GET["pizza-order-code"];
        $amount = (int)$_GET["amount"];

        if ($amount < 0 || $amount > 50) return "Fehler: Die Anzahl je Pizza muss zwischen 1 und 50 liegen.";

        $pizza = PizzaQuery::create()->findOneByOrderCode($pizzaOrderCode);

        $orderPizza = new OrderPizza();
        $orderPizza->setPizza($pizza)
                   ->setAmount($amount);

        $error = $this->pizzaOrder->addOrderPizza($orderPizza);
        if ($error) return $error;
        else return "";
    }
}
