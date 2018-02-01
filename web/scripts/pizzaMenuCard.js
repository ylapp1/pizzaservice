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

        addPizzaToOrder(button.data("pizza-order-code"), button.parents().eq(2).find("td:nth-child(2)").text(), amount);

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
