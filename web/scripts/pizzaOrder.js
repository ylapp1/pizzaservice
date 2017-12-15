/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$(document).ready(function(){

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
                    showSuccessMessage("<strong>Vielen Dank!</strong> Die Bestellung wurde entgegengenommen!")
                }
                else showErrorMessage(_text.replace("Fehler: ", ""));
            },
            error: function(xhr, status, error) {
                showErrorMessage("An AJAX error occured: " + status + ": " + error);
            }
        });

    });


    $(".pizza-table input").on("click", function(){

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

        // Update the pizza counter
        setPizzaCount(getPizzaCount() + currentValue - previousValue);

    });

});
