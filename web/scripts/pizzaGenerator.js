/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$(document).ready(function(){

    $("button.generate-pizza-button").on("click", generatePizza);

    $(document).on("click", "button:not(.generate-pizza-button)", function(){
        addGeneratedPizzaToOrder($(this));
    })

});


/**
 * Adds the generated pizza to the order.
 *
 * @param _button The order button that was clicked
 */
function addGeneratedPizzaToOrder(_button)
{
    var amount = _button.parent().find("input").val();
    amount = Number(amount);

    var pizzaName = _button.parents().eq(2).find("td:nth-child(2)").text();

    // Add pizza to order list
    $.ajax({url: "web/pizza-generator/addrandompizzatoorder.php?amount=" + amount,
        type: "get",
        dataType: "text",
        success: function(_error){

            if (_error !== "") showErrorMessage(_error);
            else
            {
                // Update the pizza counter
                setPizzaCount(getPizzaCount() + amount);
                showSuccessMessage(amount + "x \"" + pizzaName + "\" erfolgreich zur Bestellung hinzugefügt!");
            }
        }
    });
}

/**
 * Generates a random pizza from the ingredients whose checkboxes are checked.
 */
function generatePizza()
{
    var ingredientIds = [];

    $("input:checked:not([disabled])").each( function(index, input) {
        ingredientIds.push($(input).data("id"));
    });

    // Generate random pizza
    $.ajax({url: "web/pizza-generator/generate-pizza",
        type: "get",
        dataType: "text",
        data: {
            ingredientIds: JSON.stringify(ingredientIds)
        },
        success: function(_pizzaTableHTML){

            $(".pizza-table").replaceWith(_pizzaTableHTML);
            showSuccessMessage("Pizza wurde erfolgreich generiert");
        }
    });
}
