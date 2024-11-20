function logout() {
    let _token = $("#_token").val();
    $.ajax({
        type: "POST",
        data: { _token },
        url: "logout",
        success: function (data) {
            window.location.reload();
        },
    });
}
