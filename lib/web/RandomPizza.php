<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web;
use PizzaService\Propel\Models\Pizza;
use PizzaService\Propel\Models\PizzaIngredient;

/**
 * Saves and loads one random pizza from the session.
 */
class RandomPizza
{
    /**
     * The random pizza
     *
     * @var Pizza $randomPizza
     */
    private $randomPizza;

    /**
     * The session index for the random pizza
     *
     * @var String $randomPizzaSessionIndex
     */
    private $randomPizzaSessionIndex;


    /**
     * RandomPizza constructor.
     *
     * @param Pizza $_randomPizza
     */
    public function __construct(Pizza $_randomPizza = null)
    {
        if ($_randomPizza) $this->randomPizza = $_randomPizza;
        else $this->fromSession();
    }


    /**
     * Starts a session and initializes the order array if no session is running.
     */
    private function startSession()
    {
        if (session_id() == "") session_start();
        if (! $_SESSION[$this->randomPizzaSessionIndex]) $_SESSION[$this->randomPizzaSessionIndex] = array();
    }

    /**
     * Writes the random pizza to the session.
     *
     * @throws \PropelException
     */
    public function toSession()
    {
        $this->startSession();
        $_SESSION[$this->randomPizzaSessionIndex] = $this->pizzaToArray($this->randomPizza);
    }

    /**
     * Reads the random pizza from the session.
     */
    public function fromSession()
    {
        $this->startSession();

        if ($_SESSION[$this->randomPizzaSessionIndex])
        {
            $this->randomPizza = $this->pizzaFromArray($_SESSION[$this->randomPizzaSessionIndex]);
        }
    }

    /**
     * Converts a pizza and its ingredients to an array.
     *
     * @param Pizza $_pizza The order pizza
     *
     * @return array The pizza as an array
     *
     * @throws \PropelException
     */
    private function pizzaToArray(Pizza $_pizza): array
    {
        $pizzaIngredients = array();

        foreach ($_pizza->getPizzaIngredients() as $pizzaIngredient)
        {
            $pizzaIngredients[] = $pizzaIngredient->toJSON(true);
        }

        return array(
            "Pizza" => $_pizza->toJSON(true),
            "PizzaIngredients" => $pizzaIngredients
        );
    }

    /**
     * Creates a pizza object from an array of pizza data.
     *
     * @param array $_pizzaArray The pizza data
     *
     * @return Pizza The Pizza object
     */
    private function pizzaFromArray(array $_pizzaArray): Pizza
    {
        $pizza = new Pizza();
        $pizza->fromJSON($_pizzaArray["Pizza"]);

        foreach ($_pizzaArray["PizzaIngredients"] as $pizzaIngredientJSON)
        {
            $pizzaIngredient = new PizzaIngredient();
            $pizzaIngredient->fromJSON($pizzaIngredientJSON);
            $pizza->addPizzaIngredient($pizzaIngredient);
        }

        return $pizza;
    }

    /**
     * Returns the pizza.
     *
     * @return Pizza The pizza
     */
    public function pizza()
    {
        return $this->randomPizza;
    }

    /**
     * Sets the random pizza.
     *
     * @param Pizza $_pizza The pizza
     *
     * @throws \PropelException
     */
    public function setPizza(Pizza $_pizza)
    {
        $this->randomPizza = $_pizza;
        $this->toSession();
    }
}