$(document).ready(() => {
   $("#dynamicButton").attr('disabled', true);
    fetch("/fetchBanks")
        .then((response) => response.json())
        .then((banks) => {
            const options = banks.map((bank) => ({
                id: bank.bank_code,
                text: bank.bank_name,
                image: bank.bank_url,
            }));

            $("#bankDropdown").select2({
                data: options,
                templateResult: formatBank,
                templateSelection: formatBank,
                placeholder: "Select Bank",
                allowClear: false,
            });

            const placeholderOption = new Option("Select Bank", "", true, true);
            $("#bankDropdown").append(placeholderOption);

            function formatBank(bank) {
                if (!bank.id) {
                    return $("<span>" + bank.text + "</span>");
                }
                return $(
                    '<span><img src="' +
                        bank.image +
                        '" style="width: 30px; height: 30px; margin:5px; margin-right: 5px;">' +
                        bank.text +
                        "</span>"
                );
            }
        })
        .catch((error) =>
            // console.error("Error fetching banks:", error)
            Swal.fire({
                icon: "error",
                title: "Validation Error",
                text: "Error fetching banks:" + error,
            })
        );

    let isWalletInitialized = false;

    if (!isWalletInitialized) {
        let data = "NGN Balance - " + bover;

        $("#wbalance").select2({
            data: [{ id: "ngn_balance", text: data }],
            templateResult: formatWallet,
            templateSelection: formatWallet,
        });

        function formatWallet(wallet) {
            if (!wallet.id) return wallet.text;

            return $(
                '<span><img src="https://upload.wikimedia.org/wikipedia/commons/archive/7/79/20100123092606%21Flag_of_Nigeria.svg" style="width: 30px; height: 30px; margin:5px; margin-right: 5px;">' +
                    wallet.text +
                    "</span>"
            );
        }

        $("#wbalance").prop("disabled", true);
        isWalletInitialized = true;
    }

    $("#bankDropdown").on("change", function () {
        let acctno = $("#acctno").val();
        let selectElement = document.getElementById("bankDropdown");
        let bankCode = selectElement.value;
        let initialText =
            selectElement.options[selectElement.selectedIndex].text;

        if (!bankCode && initialText != "Select Bank") {
            error_message("Please select a bank.");
            return;
        }

        if (acctno) {
            verifyAccount(acctno, bankCode);
        }
    });

    let reciever = document.getElementById("reciever");

    let eventHandled = false; // Flag to track event handling

$("#acctno").on("keyup blur paste", function (event) {
    if (eventHandled) return; // Exit if already handled

    eventHandled = true; // Set flag to true when an event is handled

    let acctno = $("#acctno").val();
    let selectElement = document.getElementById("bankDropdown");
    let bankCode = selectElement.value;

    if (!acctno) {
        error_message("Please Enter a valid Account Number");
        eventHandled = false;
        return;
    }

    if (!/^\d{10}$/.test(acctno)) {
        error_message("Account number must be 10 digits.");
        eventHandled = false;
        return;
    }

    if (!bankCode) {
        error_message("Please select a bank.");
        eventHandled = false;
        return;
    }

    if (acctno) {
        verifyAccount(acctno, bankCode);
    }

    // Reset flag if input field is focused again
    $(this).on("focus", function () {
        eventHandled = false;
    });
});


    function verifyAccount(acctno, bankCode) {
        $("#preloader").show();

        let params = new URLSearchParams({
            acctno: acctno,
            bankCode: bankCode,
        });

        fetch(`verifyBankAccount?${params.toString()}`)
            .then((response) => response.json())
            .then((data) => {
                $("#preloader").hide();
                if (data.data && data.data.Status === "Success") {
                    const accountName = data.data.accountName;
                    success_message(
                        "<small>Please verify account name before proceeding</small><br> " +
                            accountName
                    );
                } else if (data.data && data.data.Status === "Failed") {
                    const errorMessage =
                        data.data.errorMessage ||
                        "Account verification failed.";
                    error_message(errorMessage);
                } else {
                    const errorMessage =
                        "An unexpected error occurred while verifying account details.";
                    error_message(errorMessage);
                }
            })
            .catch((error) => {
                $("#preloader").hide();
                console.error("Fetch error:", error);
                // alert("Failed to verify account. Please try again later.");

                Swal.fire({
                    icon: "error",
                    title: "Verification Error",
                    text: "Failed to verify account. Please try again later.",
                });
            });
    }

    function error_message(msg) {
        if (msg) reciever.textContent = msg;
        else reciever.textContent = "Cannot Verify Account Details";
        reciever.classList.remove("bg-info", "border-info");
        reciever.classList.add(
            "bg-danger",
            "border-danger",
            "p-3",
            "bg-opacity-10",
            "border",
            "rounded",
            "rounded-top-0",
            "border-top-1",
            "mt-1"
        );
         $("#dynamicButton").attr('disabled', true);
    }

    function success_message(response) {
        reciever.innerHTML = response;
        reciever.classList.add(
            "bg-info",
            "border-info",
            "p-3",
            "bg-opacity-10",
            "border",
            "rounded",
            "rounded-top-0",
            "border-top-1",
            "mt-1"
        );
        reciever.classList.remove("bg-danger", "border-danger");
         $("#dynamicButton").attr('disabled', false);
    }
});

 const form = document.getElementById("payoutForm");
    form.reset();
    document.addEventListener("DOMContentLoaded", function() {
        const dynamicButton = document.getElementById("dynamicButton");
        const pinCheckModal = new bootstrap.Modal(document.getElementById("pinCheckModal"), {
            backdrop: "static",
            keyboard: false,
        });
        const verifyPinButton = document.getElementById("verifyPinButton");
        const pinInput = document.getElementById("pinInput");
        const pinErrorMessage = document.getElementById("pinErrorMessage");


        // Validate form entries
        function validateForm() {
            const inputField1 = document.getElementById("acctno");
            const inputField2 = document.getElementById("amount");
            const inputField3 = document.getElementById("bankDropdown"); // Select dropdown

            let isValid = true;

            // Validate inputField1
            if (!inputField1.value.trim()) {
                isValid = false;
                inputField1.classList.add("is-invalid");
            } else {
                inputField1.classList.remove("is-invalid");
            }

            // Validate inputField2
            if (!inputField2.value.trim()) {
                isValid = false;
                inputField2.classList.add("is-invalid");
            } else {
                inputField2.classList.remove("is-invalid");
            }

            // Validate inputField3
            if (!inputField3.value) {
                isValid = false;
                inputField3.classList.add("is-invalid");
            } else {
                inputField3.classList.remove("is-invalid");
            }

            if (!isValid) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Please fill in all required fields.",
                });
            }

            return isValid;
        }

        // Handle button click
        dynamicButton.addEventListener("click", function() {
            if (validateForm()) {
                pinCheckModal.show();
            }
        });

        // Handle PIN verification
        verifyPinButton.addEventListener("click", function() {
            const pin = pinInput.value.trim();
             $("#preloader").show();

            // Check if CSRF token is set
            if (!document.querySelector('meta[name="csrf-token"]')) {
                console.error("CSRF token not found.");
                return;
            }

            // Call verifyPin route via AJAX
            fetch("/validatePin", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        pin
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $("#preloader").hide();
                        pinCheckModal.hide();
                        form.requestSubmit();
                    } else {
                        // Show error message
                         $("#preloader").hide();
                        pinErrorMessage.style.display = "block";
                        pinErrorMessage.textContent = data.message ||
                            "Invalid PIN. Please try again.";
                    }
                })
                .catch(error => {
                     $("#preloader").hide();
                    console.error("Error verifying PIN:", error);
                    pinErrorMessage.style.display = "block";
                    pinErrorMessage.textContent = "An error occurred. Please try again.";
                });
        });
    });

    document.getElementById('amount').addEventListener('focusout', function() {
        const amountField = document.getElementById('amount');
        const notificationSpan = document.getElementById('amountNotification');

        // Get the entered amount
        const enteredAmount = parseFloat(amountField.value);

        // Service fee (default 20 Naira)
        const serviceFee = 20;

        if (!enteredAmount || enteredAmount <= 0) {
            notificationSpan.textContent = "Please enter a valid amount.";
            return;
        }

        // Calculate the total amount
        const totalAmount = enteredAmount + serviceFee;

        // Notify the user
        notificationSpan.textContent = `The total amount including the service fee is ${totalAmount} Naira.`;
    });
