<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\PizzaOrderSession;

use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\PizzaIngredient;

/**
 * Handles reading from and writing to the pizza order session field.
 *
 * The pizzas are saved like this:
 *
 * array( [PizzaId_1] => [amount_1], [PizzaId_2] )> [amount_2], ... )
 */
class PizzaOrderSessionHandler
{
    /**
     * The index of the pizza order in the $_SESSION variable
     *
     * @var String $sessionIndex
     */
    private $pizzaOrderSessionIndex = "orderPizzas";

    /**
     * The index of the random generated pizza in the $_SESSION variable
     *
     * @var String $randomPizzaSessionIndex
     */
    private $randomPizzaSessionIndex = "randomPizza";


    /**
     * Starts a session and initializes the order array if no session is running.
     */
    public function startSession()
    {
        if (session_id() == "") session_start();
        if (! $_SESSION[$this->pizzaOrderSessionIndex]) $_SESSION[$this->pizzaOrderSessionIndex] = array();
    }

    /**
     * Returns the current pizza order.
     *
     * @return int[] The pizza order
     */
    public function getPizzaOrder(): array
    {
        $this->startSession();
        return $_SESSION[$this->pizzaOrderSessionIndex];
    }

    /**
     * Sets the pizza order.
     *
     * @param int[] $_pizzaOrder The pizza order
     */
    public function setPizzaOrder(array $_pizzaOrder)
    {
        $this->startSession();
        $_SESSION[$this->pizzaOrderSessionIndex] = $_pizzaOrder;
    }

    /**
     * Returns data about a single pizza.
     *
     * @param int $_pizzaId The pizza id
     *
     * @return int The amount of pizzas
     */
    public function getPizzaAmount(int $_pizzaId)
    {
        $pizzaOrder = $this->getPizzaOrder();

        if (! $pizzaOrder[$_pizzaId]) return 0;
        else return $pizzaOrder[$_pizzaId];
    }

    /**
     * Sets data about a single pizza.
     *
     * @param int $_pizzaId The pizza id
     * @param int $_amount The amount of this pizza
     */
    public function setPizzaAmount(int $_pizzaId, int $_amount)
    {
        $pizzaOrder = $this->getPizzaOrder();

        if ($_amount == 0) unset($pizzaOrder[$_pizzaId]);
        else $pizzaOrder[$_pizzaId] = $_amount;

        $this->setPizzaOrder($pizzaOrder);
    }

    /**
     * Saves the current random pizza in the session variable.
     *
     * @param Pizza $_pizza The random pizza
     *
     * @throws \PropelException
     */
    public function setRandomPizza(Pizza $_pizza)
    {
        $this->startSession();

        $pizzaIngredients = array();
        foreach ($_pizza->getPizzaIngredients() as $pizzaIngredient)
        {
            $pizzaIngredients[] = $pizzaIngredient->toJSON(true);
        }

        $_SESSION[$this->randomPizzaSessionIndex] = array(
            "Pizza" => $_pizza->toJSON(true),
            "PizzaIngredients" => $pizzaIngredients
        );
    }

    /**
     * Returns the current saved random pizza from the session variable.
     *
     * @return Pizza[] The random pizza as sole entry of an array or an empty array
     */
    public function getRandomPizza():array
    {
        $this->startSession();

        if ($_SESSION[$this->randomPizzaSessionIndex])
        {
            $pizza = new Pizza();
            $pizza->fromJSON($_SESSION[$this->randomPizzaSessionIndex]["Pizza"]);

            foreach ($_SESSION[$this->randomPizzaSessionIndex]["PizzaIngredients"] as $pizzaIngredientJSON)
            {
                $pizzaIngredient = new PizzaIngredient();
                $pizzaIngredient->fromJSON($pizzaIngredientJSON);
                $pizza->addPizzaIngredient($pizzaIngredient);
            }

            return array($pizza);
        }
        else return array();
    }
}
