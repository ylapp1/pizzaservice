/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

// This file includes functions that are shared across all pages

var message;
var messageText;
var pizzaCounter;

$(document).ready(function(){

    message = $("#message");
    messageText = message.find("p");
    pizzaCounter = $(".pizza-counter");

    $(".close").on("click", hideMessage);

});


/**
 * Hides the message above the list of pizzas.
 */
function hideMessage()
{
    message.toggleClass("invisible");
}

/**
 * Shows a message above the list of pizzas.
 *
 * @param _text The message text
 * @param _type The message type
 */
function showMessage(_text, _type)
{
    message.attr("class", "alert alert-" + _type);

    // Reset the text
    messageText.text("");

    // Add the new text
    messageText.append(_text);

    message.show();
}

/**
 * Shows an error message above the list of pizzas.
 *
 * @param _text The error text
 */
function showErrorMessage(_text)
{
    showMessage("<strong>Fehler!</strong> " + _text, "danger");
}

/**
 * Shows an success message above the list of pizzas.
 *
 * @param _text The success text
 */
function showSuccessMessage(_text)
{
    showMessage(_text, "success");
}

/**
 * Returns the current number of pizzas in the pizza counter.
 *
 * @returns int The current number of pizzas in the pizza counter
 */
function getPizzaCount()
{
    return Number(pizzaCounter.text());
}

/**
 * Sets the number of pizzas in the pizza counter.
 *
 * @param _amountPizzas The new amount of pizzas
 */
function setPizzaCount(_amountPizzas)
{
    pizzaCounter.text(_amountPizzas);
}
