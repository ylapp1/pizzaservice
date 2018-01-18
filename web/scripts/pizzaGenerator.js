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

        var generatedPizzaId = "false";
        var button = $(this);

        // Save the generated pizza in the database
        $.ajax({url: "web/pizza-generator/save-generated-pizza",
            type: "get",
            dataType: "text",
            success: function(_returnValue){
              generatedPizzaId = _returnValue;
            },
            complete: function(){
              addGeneratedPizzaToOrder(generatedPizzaId, button);
            }
        });

    })

});

/**
 * Adds the generated pizza to the order.
 *
 * @param _generatedPizzaId
 * @param _button
 */
function addGeneratedPizzaToOrder(_generatedPizzaId, _button)
{
    if (_generatedPizzaId !== "false")
    {
        var amount = _button.parent().find("input").val();
        amount = Number(amount);

        addPizzaToOrder(_generatedPizzaId, _button.parents().eq(2).find("td:nth-child(2)").text(), amount);
    }
}
