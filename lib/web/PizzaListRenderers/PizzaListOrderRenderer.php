<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\PizzaListRenderers;

/**
 * Renders the pizza list for the page on which users can edit to their order.
 */
class PizzaListOrderRenderer extends PizzaListRenderer
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../../web/templates/PizzaList/Buttons/deleteFromOrderButton.html",
            "Anpassen");
    }
}
