/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

var totalPrice;

$(document).ready(function(){

    var pizzaTableBody = $(".pizza-table tbody");
    totalPrice = $(".totalPrice span");

    // Event handler for the order button
    $(".address-input-field button").on("click", function(){

        var urlString = "/web/order/process?";
        var isFirstEntry = true;

        $(this).parent().parent().find("input").each(function(index, input){

            if (isFirstEntry) isFirstEntry = false;
            else urlString += "&";

            urlString += $(input).attr("name") + "=\"" + $(input).val() + "\"";

        });


        // Add pizza to order list
        $.ajax({url: urlString,
            type: "get",
            dataType: "text",
            success: function(_text){

                if (_text.indexOf("Fehler") === -1)
                {
                    showSuccessMessage("<strong>Vielen Dank!</strong> Die Bestellung wurde entgegengenommen!");
                    pizzaTableBody.find("tr").remove();
                    pizzaTableBody.append("<tr><td colspan=\"6\" class=\"text-center\">Die Bestellung ist leer</td></tr>");
                    setPizzaCount(0);
                }
                else showErrorMessage(_text.replace("Fehler: ", ""));
            },
            error: function(xhr, status, error) {
                showErrorMessage("An AJAX error occured: " + status + ": " + error);
            }
        });

    });


    pizzaTableBody.find("input").on("click", function(){

        $(this).data('val', $(this).val());

    }).on("change", function(){

        var input = $(this);
        var deleteLink = input.parent().find("a").attr("href");

        var pizzaId = deleteLink.match(/.*delete=([0-9]).*/)[1];

        $.ajax({url: "/web/order/changeamount.php?pizza-id=" + pizzaId + "&amount=" + input.val(),
            type: "get",
            dataType: "text"
        });

        var previousValue = Number(input.data('val'));
        var currentValue = Number(input.val());
        var difference = currentValue - previousValue;

        // Update the pizza counter
        setPizzaCount(getPizzaCount() + difference);

        var price = parseFloat(input.parents().eq(2).find("td:nth-child(3)").text());
        setTotalPrice(getTotalPrice() + (difference * price));
    });

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
