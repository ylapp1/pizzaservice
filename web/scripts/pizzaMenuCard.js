/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$(document).ready(function(){

    $("button").on("click", function(){

        var button = $(this);
        var amount = button.parent().find("input").val();

        amount = Number(amount);

        // Add pizza to order list
        $.ajax({url: "web/addpizzatoorder.php?pizza-id=" + button.data("pizza-id") + "&amount=" + amount,
            type: "get",
            dataType: "text",
            success: function(_error){

                if (_error !== "") showErrorMessage(_error);
                else
                {
                    // Update the pizza counter
                    setPizzaCount(getPizzaCount() + amount);
                    showSuccessMessage(amount + "x \"" + button.parents().eq(2).find("td:nth-child(2)").text() + "\" erfolgreich zur Bestellung hinzugefügt!");
                }
            }
        });

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
