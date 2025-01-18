$("#verifyNIN").on("click", function (event) {

    event.preventDefault();

    let data = new FormData(this.form);
    let validationInfo = document.getElementById("validation-info");
    let download = document.getElementById("download");
    $("#errorMsg").hide();

    $.ajax({
        type: "post",
        url: "/nin2-retrieve",
        dataType: "json",
        data,
        processData: false,
        contentType: false,
        cache: false,
        beforeSend: function () {
            // Show loading indicator
            $("#preloader").show();
            $("#download").hide();
            validationInfo.innerHTML = ` <center>
                                            <img src="assets/images/search.png" width="20%" alt="Search Icon">
                                                <p class="mt-5">This section will display search results </p>
                                                </center>`;
        },
        success: function (result) {
            $("#preloader").hide();

            if (result.data && result.data.respCode) {
                validationInfo.innerHTML = `
            <div class="border border-light">
   <div class="table-responsive">
      <table class="table">
         <thead >
            <tr>
               <th style="border: none ! important;" width="20%"></th>
               <th style="border: none ! important;"></th>
               <th style="border: none ! important;"></th>
               <th style="border: none ! important;"></th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <th scope="row" rowspan="9">
                  <img class="rounded" src="data:image/;base64, ${result.data.data.photo}" alt="User Image" style="width: 250px; height: 250px;">
               </th>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">NIN</th>
               <td style="text-align:left" ><span id="nin_no" >${result.data.data.nin}</span>
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">FirstName</th>
               <td  style="text-align:left">${result.data.data.firstName}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Surname</th>
               <td  style="text-align:left">${result.data.data.surname}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Middle Name</th>
               <td  style="text-align:left">${result.data.data.middleName}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Phone No</th>
               <td  style="text-align:left">${result.data.data.telephoneNo}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Gender</th>
               <td  style="text-align:left">${result.data.data.gender}
               </td>
            </tr>
         </tbody>
      </table>
   </div>
</div>
            `;
                $("#download").show();
            } else {
                $("#preloader").hide();
                $("#errorMsg").show();
                $("#message").html("Invalid Response");

                setTimeout(function () {
                    $("#errorMsg").fadeOut();
                }, 30000);
            }
        },
        error: function (data) {
            $("#preloader").hide();
            $.each(data.responseJSON.errors, function (key, value) {
                $("#errorMsg").show();
                $("#message").html(value);
            });
            setTimeout(function () {
                $("#errorMsg").fadeOut();
            }, 30000);
        },
    });
});

$("#regularSlip").on("click", function (event) {
    let getNIN = $("#nin_no").html();

    fetch("regularSlip/" + getNIN, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
    })
        .then((response) => {
            if (response.ok) {
                // Extract filename from Content-Disposition header
                const contentDisposition = response.headers.get(
                    "Content-Disposition"
                );
                let filename = "document.pdf";
                if (
                    contentDisposition &&
                    contentDisposition.indexOf("attachment") !== -1
                ) {
                    const filenameRegex =
                        /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    const matches = filenameRegex.exec(contentDisposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, "");
                    }
                }
                return response.blob().then((blob) => ({ blob, filename }));
            } else {
                return response.json().then((data) => {
                    // Handle errors
                    $.each(data.errors, function (key, value) {
                        $("#errorMsg2").show();
                        $("#message2").html(value);
                    });
                    setTimeout(function () {
                        $("#errorMsg2").hide();
                    }, 5000);
                });
            }
        })
        .then(({ blob, filename }) => {
            if (blob) {
                // Create a link element, use it to download the blob with the extracted filename
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = filename; // Use the extracted filename
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            // Handle errors
            $.each(data.errors, function (key, value) {
                $("#errorMsg2").show();
                $("#message2").html(value);
            });
            setTimeout(function () {
                $("#errorMsg2").hide();
            }, 5000);
        });
});

$("#standardSlip").on("click", function (event) {
    let getNIN = $("#nin_no").html();

    fetch("standardSlip/" + getNIN, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
    })
        .then((response) => {
            if (response.ok) {
                // Extract filename from Content-Disposition header
                const contentDisposition = response.headers.get(
                    "Content-Disposition"
                );
                let filename = "document.pdf"; // Default filename if not found in headers
                if (
                    contentDisposition &&
                    contentDisposition.indexOf("attachment") !== -1
                ) {
                    const filenameRegex =
                        /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    const matches = filenameRegex.exec(contentDisposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, "");
                    }
                }
                return response.blob().then((blob) => ({ blob, filename }));
            } else {
                return response.json().then((data) => {
                    // Handle errors
                    $.each(data.errors, function (key, value) {
                        $("#errorMsg2").show();
                        $("#message2").html(value);
                    });
                    setTimeout(function () {
                        $("#errorMsg2").hide();
                    }, 5000);
                });
            }
        })
        .then(({ blob, filename }) => {
            if (blob) {
                // Create a link element, use it to download the blob with the extracted filename
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = filename; // Use the extracted filename
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            // Handle errors
            $.each(data.errors, function (key, value) {
                $("#errorMsg2").show();
                $("#message2").html(value);
            });
            setTimeout(function () {
                $("#errorMsg2").hide();
            }, 5000);
        });
});

$("#premiumSlip").on("click", function (event) {
    let getNIN = $("#nin_no").html();

    fetch("premiumSlip/" + getNIN, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
    })
        .then((response) => {
            if (response.ok) {
                // Extract filename from Content-Disposition header
                const contentDisposition = response.headers.get(
                    "Content-Disposition"
                );
                let filename = "document.pdf"; // Default filename if not found in headers
                if (
                    contentDisposition &&
                    contentDisposition.indexOf("attachment") !== -1
                ) {
                    const filenameRegex =
                        /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    const matches = filenameRegex.exec(contentDisposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, "");
                    }
                }
                return response.blob().then((blob) => ({ blob, filename }));
            } else {
                return response.json().then((data) => {
                    // Handle errors
                    $.each(data.errors, function (key, value) {
                        $("#errorMsg2").show();
                        $("#message2").html(value);
                    });
                    setTimeout(function () {
                        $("#errorMsg2").hide();
                    }, 5000);
                });
            }
        })
        .then(({ blob, filename }) => {
            if (blob) {
                // Create a link element, use it to download the blob with the extracted filename
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = filename; // Use the extracted filename
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            // Handle errors
            $.each(data.errors, function (key, value) {
                $("#errorMsg2").show();
                $("#message2").html(value);
            });
            setTimeout(function () {
                $("#errorMsg2").hide();
            }, 5000);
        });
});
