$(document).ready(function () {
    $("#photo").change(function () {
        $("#disk").html("Uploading...");
        var fd = new FormData();
        var files = $("#photo")[0].files;

        if (files.length > 0) {
            fd.append("file", files[0]);

            $.ajax({
                url: "verification/upload",
                type: "post",
                data: fd,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (res) {
                    $("#img").attr("src", "data:image/;base64," + res.img);

                    $("#photoUpload").html("Change Photo");
                    $("#imageUpload").modal("hide");
                    $(".image-tag").val(res.img);
                },
                error: function (data) {
                    $.each(data.responseJSON.errors, function (key, value) {
                        $("#message")
                            .removeClass("text-dark")
                            .addClass("text-danger");
                        $("#message").html(value);
                        $("#photoUpload").html("Upload Photo");
                        $("#imageUpload").modal("hide");
                    });
                },
            });
        }
    });
    $("#imageUpload").on("hidden.bs.modal", function (e) {
        $("#photo").val(null);
        Webcam.reset("#my_webcam");
    });
    $("#imageUpload").on("shown.bs.modal", function (e) {
        reset();
    });
    function reset() {
        $("#message").html(
            "Please provide your details below. Note that accurate information is required, as it will be verified before proceeding. Ensure the details you enter are correct to avoid any delays or issues with the verification process."
        );
        $("#message").removeClass("text-danger").addClass("text-dark");
        $("#webcam").hide();
        $("#disk").html('<i class="fa fa-upload"></i> Gallery');
        $(".image-tag").val(null);
    }
    $("#webcam_Capture").click(function () {
        $("#webcam").show();
        Webcam.set({
            width: 490,
            height: 350,
            image_format: "jpeg",
            jpeg_quality: 100,
        });

        Webcam.attach("#my_webcam");
    });
});

function take_snapshot() {
    Webcam.snap(function (data_uri) {
        $("#img").attr("src", data_uri);
        $("#imageUpload").modal("hide");

        //Remove the data part
        var strImage = data_uri.replace(/^data:image\/[a-z]+;base64,/, "");
        $(".image-tag").val(strImage);
    });
}
$("#lbdob").hide();

$("#dob").click(function (evt) {
    $("#lbdob").show();
});
