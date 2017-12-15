/**
 * @file
 * @version 0.1
 * @copyright 2017 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$(document).ready(function(){

    $("button").on("click", function(){

        var button = $(this);
        var amount = button.parent().find("input").val();

        if (amount === "")
        {
            showErrorMessage("Die Anzahl an Pizzen muss zwischen 1 und 50 liegen");
            return;
        }

        amount = Number(amount);
        if (amount === 0) showErrorMessage("Die Anzahl Pizzen muss zwischen 1 und 50 liegen");
        else
        {
            // Update the pizza counter
            setPizzaCount(getPizzaCount() + amount);

            // Add pizza to order list
            $.ajax({url: "addpizzatoorder.php?pizza-id=" + button.data("pizza-id") + "&amount=" + amount,
                type: "get",
                dataType: "text",
                success: function(){
                    showSuccessMessage(amount + "x \"" + button.parents().eq(2).find("td:nth-child(2)").text() + "\" erfolgreich zur Bestellung hinzugefügt!");
                }
            });
        }

    });

});
