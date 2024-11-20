$(document).ready(function () {
    $(".view").on("shown.bs.modal", function (event) {
        $("#modal-preloader2").show();
        var button = $(event.relatedTarget); // Button that triggered the modal
        let id = button.data("id");
        $.ajax({
            url: "get-users",
            type: "GET",
            data: { id },
            dataType: "json",
            success: function (data) {
                $("#modal-preloader2").hide();
                $("#userid").html(id);
                //populate form
                if (data.profile_pic == "" || data.profile_pic == null)
                    $("#label_passport").attr({
                        src: "assets/images/identity.png",
                    });
                else
                    $("#label_passport").attr({
                        src: "data:image/;base64," + data.profile_pic,
                    });

                let middle = "";
                data.middle_name == null
                    ? (middle = "")
                    : (middle = data.middle_name);

                $("#username").html(
                    data.first_name + " " + middle + " " + data.last_name
                );
                $("#label_phoneno").html(data.phone_number);
                $("#label_email").html(data.email);
                $("#label_dob").html(data.dob);
                $("#label_identity").html(data.idType);
                $("#label_identity_no").html(data.idNumber);
                $("#label_type").html(toTitleCase(data.role));
            },
            error: function (data) {
                $(".view").scrollTop(0);
                setTimeout(function () {
                    $(".view").modal("toggle");
                }, 5000);
            },
        });
        //convert to title Case
        function toTitleCase(str) {
            return str.replace(/(?:^|\s)\w/g, function (match) {
                return match.toUpperCase();
            });
        }

        //Approve & Reject
        $("#Approve").click(function (evt) {
            $("#Approve").prop("disabled", true);
            $("#spinner2").show();

            var userid = $("#userid").html();
            var email = $("#label_email").html();

            $.ajax({
                //create an ajax request to get session data
                type: "POST",
                url: "approveKYC", //expect json File to be returned
                data: { userid: userid, email: email },
                success: function (response) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                },
                error: function (data) {},
            });
        });

        $("#Reject").click(function (evt) {
            $("#Reject").prop("disabled", true);
            $("#spinner3").show();

            var userid = $("#userid").html();
            var email = $("#label_email").html();

            $.ajax({
                //create an ajax request to get session data
                type: "POST",
                url: "rejectKYC", //expect json File to be returned
                data: { userid: userid, email: email },
                success: function (response) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                },
                error: function (data) {},
            });
        });
    });

    // Load the active tab from localStorage
    var activeTab = localStorage.getItem("activeTab");
    if (activeTab) {
        $('#myTab a[href="' + activeTab + '"]').tab("show");
    } else {
        // Set default tab if no active tab is stored
        $('#myTab a[href="#mtn"]').tab("show");
    }
    // Store the active tab in localStorage when clicked
    $('a[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        localStorage.setItem("activeTab", $(e.target).attr("href"));
    });
});
