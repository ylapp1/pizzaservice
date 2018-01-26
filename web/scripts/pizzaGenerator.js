/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

$(document).ready(function(){

    $("button.generate-pizza-button").on("click", function(){

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

    });

    $(document).on("click", "button:not(.generate-pizza-button)", function(){

        var button = $(this);

        var amount = button.parent().find("input").val();
        amount = Number(amount);

        var tableCells = button.parents().eq(2);

        addGeneratedPizzaToOrder(tableCells.find("td:nth-child(2)").text(), amount);
    })

});

/**
 * Adds the generated pizza to the order.
 *
 * @param _pizzaName String The name of the pizza
 * @param _amount int The amount of pizzas
 */
function addGeneratedPizzaToOrder(_pizzaName, _amount)
{
    // Add pizza to order list
    $.ajax({url: "web/pizza-generator/addrandompizzatoorder.php?amount=" + _amount,
        type: "get",
        dataType: "text",
        success: function(_error){

            if (_error !== "") showErrorMessage(_error);
            else
            {
                // Update the pizza counter
                setPizzaCount(getPizzaCount() + _amount);
                showSuccessMessage(_amount + "x \"" + _pizzaName + "\" erfolgreich zur Bestellung hinzugefügt!");
            }
        }
    });
}
