$(document).ready(function () {
    //Create New Account Information
    $("#register").click(function (evt) {
        let email = $("#email").val();
        let password = $("#password").val();
        let cpassword = $("#password_confirmation").val();

        if (email !== "" && password !== "" && cpassword !== "") {
            if ($("#terms").is(":checked")) {
                $("#register").prop("disabled", true);
                $("#spinner").show();
                $("#error").empty();

                // Stop the button from submitting the form:
                evt.preventDefault();

                // Serialize the entire form:
                var data = new FormData(this.form);
                $.ajax({
                    url: "register",
                    type: "POST",
                    data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (dataResult) {
                        $("#register").html("Successful ...");
                        setTimeout(function () {
                            $("form").trigger("reset");
                            location.href = dataResult.redirect_url;
                        }, 2000);
                    },
                    error: function (data) {
                        $.each(data.responseJSON.errors, function (key, value) {
                            $("#error").empty();
                            $("#error").append(
                                "<strong>" + value + "</strong>"
                            );
                            $("#alert-danger").fadeIn();
                        });

                        setTimeout(function () {
                            $("#register").prop("disabled", false);
                            $("#spinner").hide();
                        }, 5000);
                    },
                });
            } else {
                $("#error").empty();
                $("#error").append(
                    "<strong>Please accept our privacy policy</strong>"
                );
                $("#alert-danger").fadeIn();
                setTimeout(function () {
                    $("#spinner").hide();
                    $("#register").prop("disabled", false);
                }, 2000);
            }
        } else {
            $("#error").empty();
            $("#error").append(
                "<strong> Email and password fields are required!</strong>"
            );
            $("#alert-danger").fadeIn();
            setTimeout(function () {
                $("#spinner").hide();
                $("#register").prop("disabled", false);
            }, 2000);
        }
    });
});
