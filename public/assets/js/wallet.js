$(document).ready(function () {
    $("#Wallet_ID").on("blur", function () {
        let walletID = $("#Wallet_ID").val();
        let reciever = document.getElementById("reciever");

        if (walletID != "") {
            $.ajax({
                //create an ajax request to get session data
                type: "get",
                url: "getReciever",
                dataType: "json", //expect json File to be returned
                data: { walletID: walletID },
                success: function (response) {
                    if (response == 0) {
                        error_message(null);
                    } else if (response == "kyc") {
                        error_message("Reciever Pending KYC");
                    } else {
                        success_message(response);
                    }
                },
                error: function (data) {
                    error_message(null);
                },
            });
        } else {
            error_message("Please Enter a valid wallet ID");
        }
    });

    function error_message(msg) {
        if (msg) reciever.textContent = msg;
        else reciever.textContent = "Cannot Verify Reciever";
        reciever.classList.remove("bg-info", "border-info");
        reciever.classList.add(
            "bg-danger",
            "border-danger",
            "p-3",
            "bg-opacity-10",
            "border",
            "rounded",
            "rounded-top-0",
            "border-top-0",
            "mt-0"
        );
    }
    function success_message(response) {
        reciever.textContent = "Reciever: " + response;
        reciever.classList.add(
            "bg-info",
            "border-info",
            "p-3",
            "bg-opacity-10",
            "border",
            "rounded",
            "rounded-top-0",
            "border-top-0",
            "mt-0"
        );
        reciever.classList.remove("bg-danger", "border-danger");
    }
});
