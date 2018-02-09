/**
 * @file
 * @version 0.1
 * @copyright 2017-2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

/**
 * The pizza table input fields (amount fields)
 */
var inputFields;


$(document).ready(function(){

    inputFields = $(".pizza-table input");

    $("button").on("click", function() {
        addOrderPizza($(this))
    });

    inputFields.on("focusin change", function() {
        // Automatically reset all other input fields when an input field is focused or changed
        resetInputFields($(this))
    });

});


/**
 * Resets all pizza table input fields except for the one that was focused or changed.
 *
 * @param _inputField The input field that was focused or changed
 */
function resetInputFields (_inputField)
{
    // Backup the value of the changed/focused input field
    var value = _inputField.val();

    // Change the value of all input fields to 0
    inputFields.val(0);

    // Change the value of this input field back to its original value
    _inputField.val(value);
}

/**
 * Adds an order pizza to the order.
 *
 * @param _button The button that was pressed
 */
function addOrderPizza (_button)
{
    var pizzaName =  _button.parents().eq(2).find("td:nth-child(2)").text();
    var amount = _button.parent().find("input").val();
    amount = Number(amount);

    addPizzaToOrder(_button.data("pizza-order-code"), pizzaName, amount);
}
