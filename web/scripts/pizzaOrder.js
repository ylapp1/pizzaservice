/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

/**
 * The order button
 */
var orderButton;

/**
 * The tbody element of the pizza table
 */
var pizzaTableBody;

/**
 * The reset order button
 */
var resetOrderButton;

/**
 * The total price span element
 */
var totalPrice;


$(document).ready(function(){

    orderButton = $(".order-button");
    pizzaTableBody = $(".pizza-table tbody");
    resetOrderButton = $(".reset-order-button");
    totalPrice = $(".totalPrice span");

    orderButton.on("click", function(){
        processOrder($(this));

        // Prevent the form from being submitted by HTML
        return false;
    });

    pizzaTableBody.find("input").on("change", function(){
        changeAmountOrderPizzas($(this));
    });

    resetOrderButton.on("click", resetOrder);

});


/**
 * Returns the current price in the total price paragraph.
 *
 * @returns int The current price in the total price paragraph
 */
function getTotalPrice()
{
    return parseFloat(totalPrice.text());
}

/**
 * Sets the current price in the total price paragraph.
 *
 * @param _totalPrice The new price in the total price paragraph
 */
function setTotalPrice(_totalPrice)
{
    totalPrice.text(_totalPrice.toFixed(2));
}

/**
 * Resets the pizzas of the current order.
 */
function resetOrder()
{
    $.ajax({url: "web/order/reset-order",
        type: "get",
        dataType: "text",
        success: function(_error)
        {
            if (_error === "") clearOrder("Bestellung zurückgesetzt");
            else showErrorMessage(_error);
        }
    })
}

/**
 * Clears the order that is displayed on the page.
 *
 * @param {String} _message The success message that will be displayed
 */
function clearOrder(_message)
{
    pizzaTableBody.find("tr").remove();
    pizzaTableBody.append("<tr><td colspan=\"6\" class=\"text-center alert alert-info\">Die Bestellung ist leer</td></tr>");
    setPizzaCount(0);
    setTotalPrice(0);
    orderButton.prop("disabled", true);
    resetOrderButton.prop("disabled", true);
    showSuccessMessage(_message);
}

/**
 * Returns the url string for order processing.
 *
 * @param _addressInputs
 *
 * @return {String|Boolean} The url string or false
 */
function getOrderProcessUrl(_addressInputs)
{
    var urlString = "web/order/process?";
    var isFirstEntry = true;

    _addressInputs.each(function(index, input){

        if (isFirstEntry) isFirstEntry = false;
        else urlString += "&";

        var inputValue = $(input).val();

        if (inputValue === "")
        {
            showErrorMessage("Bitte alle Felder ausfüllen!");
            return false;
        }

        urlString += $(input).attr("name") + "=" + inputValue;

    });

    return urlString;
}

/**
 * Tries to process the order by sending the customer address data to the php.
 *
 * @param _button The address input field button that was clicked
 */
function processOrder(_button)
{
    var addressInputs = _button.parents().eq(2).find("input");
    var orderProcessUrl = getOrderProcessUrl(addressInputs);
    if (! orderProcessUrl) return;

    // Add pizza to order list
    $.ajax({url: orderProcessUrl,
        type: "get",
        dataType: "text",
        success: function(_text){

            if (_text.indexOf("Fehler") === -1)
            {
                clearOrder("<strong>Vielen Dank!</strong> Die Bestellung wurde entgegengenommen!");
            }
            else showErrorMessage(_text.replace("Fehler: ", ""));
        },
        error: function(xhr, status, error) {
            showErrorMessage("An AJAX error occured: " + status + ": " + error);
        }
    });
}

/**
 * Changes the amount of order pizzas for a single pizza.
 *
 * @param _amountInput The input element that triggered the amount change
 */
function changeAmountOrderPizzas(_amountInput)
{
    var deleteLink = _amountInput.parent().find("a").attr("href");

    var pizzaOrderCode = deleteLink.match(/.*delete=(G?[0-9]).*/)[1];

    $.ajax({url: "web/order/changeamount.php?pizza-order-code=" + pizzaOrderCode + "&amount=" + _amountInput.val(),
        type: "get",
        dataType: "text",
        success: function(_error)
        {
            var previousValue = Number(_amountInput.data('val'));

            if (_error !== "")
            {
                showErrorMessage(_error);
                _amountInput.val(previousValue);
            }
            else
            {
                hideMessage();
                var currentValue = Number(_amountInput.val());
                var difference = currentValue - previousValue;

                // Update the pizza counter
                setPizzaCount(getPizzaCount() + difference);

                var price = parseFloat(_amountInput.parents().eq(2).find("td:nth-child(3)").text());
                setTotalPrice(getTotalPrice() + (difference * price));

                _amountInput.data("val", currentValue);
            }
        }
    });
}
