/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

var pizzaTableCells;

$(document).ready(function(){

    pizzaTableCells = $(".pizza-table tbody td");

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
            success: function(_pizzaDataJson){

                var pizza = $.parseJSON(_pizzaDataJson);

                updatePizzaTable(pizza);
                showSuccessMessage("Pizza \"" + pizza.Name + "\" wurde erfolgreich generiert");
            }
        });

    });

    $("button:not(.generate-pizza-button)").on("click", function(){

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

/**
 * Updates the pizza table with the information about the new generated pizza.
 *
 * @param _pizza JSON The pizza data
 */
function updatePizzaTable(_pizza)
{
    var price = parseFloat(_pizza.Price).toFixed(2);
    var ingredientsString = "";
    var isFirstEntry = true;

    $.each(_pizza.PizzaIngredients, function(index, pizzaIngredient){

        if (isFirstEntry) isFirstEntry = false;
        else ingredientsString += ", ";

        ingredientsString += pizzaIngredient.Name + " (" + pizzaIngredient.Grams + ")";
    });

    $(pizzaTableCells[0]).text(_pizza.OrderCode);
    $(pizzaTableCells[1]).text(_pizza.Name);
    $(pizzaTableCells[2]).text(price + " €");
    $(pizzaTableCells[3]).text(ingredientsString);
    $(pizzaTableCells[4]).find("button").data("pizza-id", _pizza.Id);
}
