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


    var inputFields = $(".pizza-table input");

    // Automatically reset all other input fields when a input field is focused or changed
    inputFields.on("focusin change", function(){

        // Backup the value of the changed/focused input field
        var value = $(this).val();

        // Change the value of all input fields to 0
        inputFields.val(0);

        // Change the value of this input field back to its original value
        $(this).val(value);

    });

});
