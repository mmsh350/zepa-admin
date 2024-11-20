$("#start-again").on("click", function () {
    $.post("/kyc-start-over", { _token: $("#_token").val() }, function (data) {
        location.href = data.redirect_url;
    });
});
