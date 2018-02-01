/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

var pizzaTableBody;
var resetOrderButton;
var totalPrice;

$(document).ready(function(){

    pizzaTableBody = $(".pizza-table tbody");
    resetOrderButton = $(".reset-order-button");
    totalPrice = $(".totalPrice span");

    // Event handler for the order button
    $(".address-input-field button").on("click", function(){

        var urlString = "web/order/process?";
        var isFirstEntry = true;

        $(this).parent().parent().find("input").each(function(index, input){

            if (isFirstEntry) isFirstEntry = false;
            else urlString += "&";

            var inputValue = $(input).val();

            if (inputValue === "")
            {
                showErrorMessage("Bitte alle Felder ausfüllen!");
                urlString = false;
                return;
            }

            urlString += $(input).attr("name") + "=\"" + inputValue + "\"";

        });

        if (! urlString) return;

        // Add pizza to order list
        $.ajax({url: urlString,
            type: "get",
            dataType: "text",
            success: function(_text){

                if (_text.indexOf("Fehler") === -1)
                {
                    showSuccessMessage("<strong>Vielen Dank!</strong> Die Bestellung wurde entgegengenommen!");
                    resetOrder();
                }
                else showErrorMessage(_text.replace("Fehler: ", ""));
            },
            error: function(xhr, status, error) {
                showErrorMessage("An AJAX error occured: " + status + ": " + error);
            }
        });

        // Prevent the form from being submitted by HTML
        return false;

    });


    pizzaTableBody.find("input").on("change", function(){

        var input = $(this);
        var deleteLink = input.parent().find("a").attr("href");

        var pizzaId = deleteLink.match(/.*delete=([0-9]).*/)[1];

        $.ajax({url: "web/order/changeamount.php?pizza-id=" + pizzaId + "&amount=" + input.val(),
            type: "get",
            dataType: "text",
            success: function(_error)
            {
                var previousValue = Number(input.data('val'));

                if (_error !== "")
                {
                    showErrorMessage(_error);
                    input.val(previousValue);
                }
                else
                {
                    hideMessage();
                    var currentValue = Number(input.val());
                    var difference = currentValue - previousValue;

                    // Update the pizza counter
                    setPizzaCount(getPizzaCount() + difference);

                    var price = parseFloat(input.parents().eq(2).find("td:nth-child(3)").text());
                    setTotalPrice(getTotalPrice() + (difference * price));

                    input.data("val", currentValue);
                }
            }
        });
    });

    resetOrderButton.on("click", function(){

        $.ajax({url: "web/order/reset-order",
            type: "get",
            dataType: "text",
            success: function(_error)
            {
                if (_error === "")
                {
                    showSuccessMessage("Bestellung zurückgesetzt");
                    resetOrder();
                }

            }
        })

    })

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

function resetOrder()
{
    pizzaTableBody.find("tr").remove();
    pizzaTableBody.append("<tr><td colspan=\"6\" class=\"text-center alert alert-info\">Die Bestellung ist leer</td></tr>");
    setPizzaCount(0);
    setTotalPrice(0);
    resetOrderButton.prop("disabled", true);
}
