$(document).ready(function () {
    $("#service_id").change(function () {
        service_id = $(this).val();
        $.ajax({
            //create an ajax request to get session data
            type: "get",
            url: "fetch-data-type",
            data: { id: service_id },
            dataType: "json", //expect json File to be returned
            success: function (response) {
                var len = response.length;
                $("#type").empty();
                $("#type").append(
                    "<option value=''>" + "Data Type" + "</option>"
                );

                for (var i = 0; i < len; i++) {
                    var plan_type = response[i]["plan_type"];

                    $("#type").append(
                        "<option value='" +
                            plan_type +
                            "'>" +
                            plan_type +
                            "</option>"
                    );
                }
            },
            error: function (data) {
                //Do Nothing
            },
        });
    });

    $("#type").change(function () {
        let service_id = $("#service_id").val();
        let type = $(this).val();
        $.ajax({
            //create an ajax request to get session data
            type: "get",
            url: "fetch-data-plan",
            data: { id: service_id, type: type },
            dataType: "json", //expect json File to be returned
            success: function (response) {
                var len = response.length;
                $("#plan").empty();
                $("#plan").append(
                    "<option value=''>" + "Data Plan" + "</option>"
                );

                for (var i = 0; i < len; i++) {
                    var plan_type =
                        response[i]["size"] +
                        " " +
                        response[i]["plan_type"] +
                        " (" +
                        response[i]["amount"] +
                        ")" +
                        " " +
                        response[i]["validity"];
                    var id = response[i]["data_id"];

                    $("#plan").append(
                        "<option value='" + id + "'>" + plan_type + "</option>"
                    );
                }
            },
            error: function (data) {
                //Do Nothing
            },
        });
    });

    //Get amount
    $("#plan").change(function () {
        $plan = $(this).val();
        $.ajax({
            //create an ajax request to get session data
            type: "get",
            url: "fetch-sme-data-bundles-price",
            data: { id: $plan },
            dataType: "json", //expect json File to be returned
            success: function (response) {
                $("#amountToPay").val("₦ " + response);
            },
            error: function (data) {
                //Do Nothing
            },
        });
    });
});
