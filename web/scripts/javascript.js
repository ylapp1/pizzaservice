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
    message.addClass("invisible");
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

/**
 * Adds a pizza to the order.
 *
 * @param _pizzaId int The id of the pizza
 * @param _pizzaName String The name of the pizza
 * @param _amount int The amount of pizzas
 */
function addPizzaToOrder(_pizzaId, _pizzaName, _amount)
{
    // Add pizza to order list
    $.ajax({url: "web/addpizzatoorder.php?pizza-id=" + _pizzaId + "&amount=" + _amount,
        type: "get",
        dataType: "text",
        success: function(_error){

            if (_error !== "") showErrorMessage(_error);
            else
            {
                // Update the pizza counter
                setPizzaCount(getPizzaCount() + _amount);
                showSuccessMessage(_amount + "x \"" + _pizzaName + "\" erfolgreich zur Bestellung hinzugefügt!");
            }
        }
    });
}
