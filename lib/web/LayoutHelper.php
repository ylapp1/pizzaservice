<?php
/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web;

/**
 * Printer for the html parts that are the same for every page of the web interface.
 */
class LayoutHelper
{
    /**
     * The directory from which templates will be loaded
     *
     * @var String $templatePath
     */
    private $templatePath = __DIR__ . "/../../web/templates";


    /**
     * Returns the template path.
     *
     * @return String The template path
     */
    public function getTemplatePath(): String
    {
        return $this->templatePath;
    }

    /**
     * Sets the template path.
     *
     * @param String $_templatePath The template path
     */
    public function setTemplatePath(String $_templatePath)
    {
        $this->templatePath = $_templatePath;
    }


    /**
     * Prints the page up to the closing </head> tag.
     *
     * @param String $_pageJavascriptFile Name of the page specific java script file
     */
    public function renderHead(String $_pageJavascriptFile)
    {
        $head = file_get_contents($this->templatePath . "/head.html");
        echo str_replace("{%PAGE_JAVESCRIPT%}", $_pageJavascriptFile . ".js", $head);
    }

    /**
     * Prints the page navigation bar.
     * Starts with the opening <body> tag and ends with the header
     *
     * @param String $_activeMenu Identifier of the menu that will be highlighted
     */
    public function renderHeader(String $_activeMenu = null)
    {
        $headerTemplate = file_get_contents($this->templatePath . "/header.html");

        // Add the active menu to the header
        $menuName = "pizza-menu";
        if (stripos($headerTemplate, $_activeMenu) !== false) $menuName = $_activeMenu;

        $headerTemplate = str_replace($menuName, $menuName . " active", $headerTemplate);


        // Add amount pizzas to header
        if (session_id() == "") session_start();

        $amountPizzas = 0;
        if (isset($_SESSION["orderPizzas"]))
        {
            foreach ($_SESSION["orderPizzas"] as $pizzaId => $amount)
            {
                $amountPizzas += $amount;
            }
        }

        echo str_replace("{%TOTAL_AMOUNT_PIZZAS%}", $amountPizzas, $headerTemplate);
    }

    /**
     * Prints the footer of the page.
     * Starts with the footer followed by the closing </body> tag and ends with the closing </html> tag
     */
    public function renderFooter()
    {
        echo file_get_contents($this->templatePath . "/footer.html");
    }
}
