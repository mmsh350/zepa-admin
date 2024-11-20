$(document).ready(function () {
    $("#reason").on("shown.bs.modal", function (event) {
        var button = $(event.relatedTarget);

        var reason = button.data("reason");
        if (reason != "") $("#message").html(reason);
        else $("#message").html("No Message Yet.");
    });

    //Options
    hide();
    $("#options").change(function () {
        var selectedIndex = this.selectedIndex;
        var inputType = "text";
        var labelText = "";
        var value = "";

        switch (selectedIndex) {
            case 0:
                hide();
                break;
            case 1:
                show();
                inputType = "date";
                labelText = "New Date of Birth";
                break;
            case 2:
                show();
                inputType = "text";
                labelText = "Name to correct (e.g LastName: Muktar)";
                break;
            case 3:
                show();
                inputType = "text";
                labelText = "Phone Number to Update";
                break;
            case 4:
                show();
                inputType = "text";
                labelText = "Gender to update (e.g Male or Female)";
                break;
            case 5:
                show();
                inputType = "text";
                labelText = "New Name (e.g LastName MiddleName FirstName)";
                break;
            case 6:
                show();
                inputType = "text";
                labelText = "BVN Revalidation";
                value = "BVN No :" + $("#bvn_number").val();

                break;
            case 7:
                show();
                inputType = "text";
                labelText = "BVN Whitelisting:";
                value = "BVN No :" + $("#bvn_number").val();
                break;
            default:
                hide();
                inputType = "text";
                labelText = "Input:";
                break;
        }

        $("#data_to_modify").attr("type", inputType);
        $("#data_to_modify").val(value);
        $("#modify_lbl").text(labelText);
    });
});

function hide() {
    $("#data_to_modify").hide();
    $("#modify_lbl").hide();
}
function show() {
    $("#data_to_modify").show();
    $("#modify_lbl").show();
}
