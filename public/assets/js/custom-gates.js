$(document).ready(function () {
    //Default
    $("#ptm11").prop("checked", false);
    $("#ptm44").prop("checked", true);

    $("#amount").val(null);

    $($("input[name=radio1]")).change(function () {
        if ($("#ptm11").is(":checked")) {
            $("#topup").html(
                '<i class="icofont icofont-pay">&nbsp;</i> Pay With Paystack'
            );
        } else {
            $("#topup").html(
                '<i class="icofont icofont-pay">&nbsp;</i> Pay With Monnify'
            );
        }
    });

    $("#topup").on("click", function () {
        let pmt1 = $("#ptm11").val();
        let pmt2 = $("#ptm44").val();
        let pmethod = "";
        let amt = document.getElementById("amount").value;

        //get selected option
        if ($("#ptm11").is(":checked")) {
            pmethod = pmt1;
        } else if ($("#ptm44").is(":checked")) pmethod = pmt2;
        else {
            $("#error").show();
            $("#error").html("Please select a payment method first");
            setTimeout(function () {
                $("#error").empty();
                $("#error").hide();
            }, 2000);
            return;
        }

        if (amt <= 0 || amt < 500 || amt > 20000) {
            $("#error").show();
            $("#error").html(
                "Invalid amount. Please enter an amount between 500 and 20,000 Naira."
            );
            setTimeout(function () {
                $("#error").empty();
                $("#error").hide();
            }, 2000);

            return;
        }

        if (pmethod == pmt1) {
            $("#error").show();
            $("#error").html("Paystack is comming soon! use monify for now.");
            setTimeout(function () {
                $("#error").empty();
                $("#error").hide();
            }, 2000);
        } else {
            let fn = document.getElementById("first-name").value;
            let ln = document.getElementById("last-name").value;
            let fullname = fn + " " + ln;

            let email = document.getElementById("email").value;
            let amt = document.getElementById("amount").value;

            let desc = document.getElementById("desc").value;
            var _token = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            MonnifySDK.initialize({
                amount: amt,
                currency: "NGN",
                reference: new String(new Date().getTime()),
                customerFullName: fullname,
                customerEmail: email,
                apiKey: window.APP_ENV.MONNIFYAPI,
                contractCode: window.APP_ENV.MONNIFYCONTRACT,
                paymentDescription: desc,

                onLoadStart: () => {},
                onLoadComplete: () => {},
                onComplete: function (response) {
                    if (response.status == "SUCCESS") {
                        $.ajax({
                            type: "POST",
                            url: "verifyPayments",
                            data: {
                                ref: response.transactionReference,
                                pmethod,
                                _token,
                                desc,
                                amt: response.authorizedAmount,
                            },
                            dataType: "json",
                            success: function (dataResult) {
                                if (dataResult.code == 200) {
                                    Swal.fire({
                                        title: "Payment Confirmation",
                                        html:
                                            'Funds Received! Confirmation in Progress. Please Check Your Wallet for Update. <br/><br/><a href="' +
                                            dataResult.link +
                                            '" target="_blank"><i class="bi bi-download"></i> Download Receipt</a>.',
                                        icon: "success",
                                        confirmButtonColor: "#2e73b4",
                                        confirmButtonText: "Continue",
                                        allowOutsideClick: false,
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                } else if (dataResult.code == 201) {
                                    Swal.fire({
                                        title: "Error",
                                        text:
                                            dataResult.err +
                                            ", Contact the administrator if you are debitted",
                                        icon: "error",
                                        confirmButtonColor: "#2e73b4",
                                        allowOutsideClick: false,
                                    });
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 10000);
                                }
                            },
                            error: function (data) {
                                Swal.fire({
                                    title: "Something Weird Happened",
                                    text: "Sorry Error Occured while making Transaction.Try again...",
                                    icon: "error",
                                    confirmButtonColor: "#2e73b4",
                                    allowOutsideClick: false,
                                });
                                setTimeout(function () {
                                    // window.location.reload();
                                }, 10000);
                            },
                        });
                    }
                },
                onClose: function (response) {
                    if (response.paymentStatus == "USER_CANCELLED") {
                        Swal.fire({
                            title: "Payment Cancelled",
                            text: "Wallet Top up request was cancelled.",
                            icon: "error",
                            confirmButtonColor: "#aaa",
                            confirmButtonText: "Close",
                            allowOutsideClick: false,
                        }).then(function () {
                            window.location.reload();
                        });
                        setTimeout(function () {
                            window.location.reload();
                        }, 5000);
                    }
                },
            });
        }
    });
});

function isNumberKey(evt) {
    var e = evt || window.event; //window.event is safer, thanks @ThiefMaster
    var charCode = e.which || e.keyCode;
    if (charCode > 31 && (charCode < 47 || charCode > 57)) return false;
    if (e.shiftKey) return false;
    return true;
}
