$(document).ready(function () {
    $("#spinner2").hide();
    $("#update-pin-form").on("submit", function (e) {
        e.preventDefault(); // Prevent the default form submission
        $("#spinner").show();
        $("#send-otp").prop("disabled", true);
        $.ajax({
            url: pinVerifyRoute,
            method: "POST",
            data: $(this).serialize(),
            success: function (response) {
                $("#spinner").hide();
                $("#send-otp").prop("disabled", false);
                if (response.success) {
                    $("#otpModal").modal("hide"); // Close the modal
                    $("#update-pin-form")[0].reset(); // Reset the initial form
                    $("#otp-form")[0].reset(); // Reset the OTP form
                    $("#otpModal").modal("show"); // Show the OTP modal
                } else {
                    $("#errMsg").append(`
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
                }
            },
            error: function (xhr) {
                $("#spinner").hide();
                $("#send-otp").prop("disabled", false);
                // Clear previous error messages
                $("#errMsg").empty();

                // Handle specific error responses
                let errorMsg;
                if (xhr.status === 422) {
                    errorMsg =
                        xhr.responseJSON.error || "Validation error occurred.";
                } else if (xhr.status === 429) {
                    errorMsg =
                        xhr.responseJSON.error ||
                        "Too many requests. Please try again later.";
                } else {
                    errorMsg = "An unexpected error occurred.";
                }

                // Append the error message as a Bootstrap alert
                $("#errMsg").append(`
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${errorMsg}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
            },
        });
    });

    $("#verify-otp").on("click", function () {
        $("#spinner2").show();
        $("#verify-otp").prop("disabled", true);

        $.ajax({
            url: pinUpdateRoute, // URL to update the PIN
            method: "POST",
            data: $("#otp-form").serialize(), // Serialize OTP form data
            success: function (response) {
                $("#spinner2").hide();
                $("#verify-otp").prop("disabled", false);
                if (response.success) {
                    $("#modal_err").empty().append(`
    <div class="alert alert-success">${response.message}</div>
`);

                    // Set a timeout to refresh the page after 5 seconds
                    setTimeout(function () {
                        location.reload(); // Refresh the page
                    }, 5000); // 5000 milliseconds = 5 seconds
                } else {
                    // Show success message in modal if needed
                    $("#modal_err").empty().append(`
                    <div class="alert alert-success">${response.message}</div>
                `);
                }
            },
            error: function (xhr) {
                // Clear previous error messages
                $("#modal_err").empty();

                $("#spinner2").hide();
                $("#verify-otp").prop("disabled", false);

                // Handle error responses
                if (xhr.status === 422) {
                    // Display validation errors
                    if (xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            $("#modal_err").append(`
                    <div class="alert alert-danger">${value.join(", ")}</div>
                `);
                        });
                    } else {
                        // Fallback if errors structure is different
                        $("#modal_err").append(`
                <div class="alert alert-danger">${xhr.responseJSON.error}</div>
            `);
                    }
                } else {
                    // Handle other error statuses
                    let errorMsg =
                        xhr.responseJSON.error ||
                        "An error occurred. Please try again.";
                    $("#modal_err").append(`
            <div class="alert alert-danger">${errorMsg}</div>
        `);
                }
            },
        });
    });
});
