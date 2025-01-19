$("#verifyBVN").on("click", function (event) {
    // Stop the button from submitting the form:
    event.preventDefault();

    let data = new FormData(this.form);
    let validationInfo = document.getElementById("validation-info");
    let download = document.getElementById("download");

    $.ajax({
        type: "post",
        url: "/bvnv2-retrieve",
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
            // <h4 style="background:#285e8e; color:white;   border-top-left-radius: 20px;border-top-right-radius: 20px;">Verified Information</h4>
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
               <th scope="row" style="text-align:right; border: none ! important;">BVN</th>
               <td style="text-align:left" ><span id="bvnno" >${result.data.data.bvn}</span>
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">FirstName</th>
               <td  style="text-align:left">${result.data.data.firstName}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Surname</th>
               <td  style="text-align:left">${result.data.data.lastName}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Middle Name</th>
               <td  style="text-align:left">${result.data.data.middleName}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Phone No</th>
               <td  style="text-align:left">${result.data.data.phoneNumber}
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
        },
        error: function (data) {
            $("#preloader").hide();
            $.each(data.responseJSON.errors, function (key, value) {
                $("#errorMsg").show();
                $("#message").html(value);
            });
            setTimeout(function () {
                $("#errorMsg").hide();
            }, 5000);
        },
    });
});

$("#freeSlip").on("click", function (event) {
    let getBVN = $("#bvnno").html();
    $.ajax({
        type: "get",
        url: "standardBVN/" + getBVN,
        dataType: "json",
        processData: false,
        contentType: false,
        cache: false,
        success: function (response) {
            if (response.view) {
                var newWindow = window.open("", "_blank");
                newWindow.document.write(response.view);
                newWindow.document.close();
            } else {
                console.error("No view content received");
            }
        },
        error: function (data) {
            $.each(data.responseJSON.errors, function (key, value) {
                $("#errorMsg2").show();
                $("#message2").html(value);
            });
            setTimeout(function () {
                $("#errorMsg2").hide();
            }, 5000);
        },
    });
});

$("#paidSlip").on("click", function (event) {
    let getBVN = $("#bvnno").html();
    $.ajax({
        type: "get",
        url: "premiumBVN/" + getBVN,
        dataType: "json",
        processData: false,
        contentType: false,
        cache: false,
        success: function (response) {
            if (response.view) {
                var newWindow = window.open("", "_blank");
                newWindow.document.write(response.view);
                newWindow.document.close();
            } else {
                console.error("No view content received");
            }
        },
        error: function (data) {
            $.each(data.responseJSON.errors, function (key, value) {
                $("#errorMsg2").show();
                $("#message2").html(value);
            });
            setTimeout(function () {
                $("#errorMsg2").hide();
            }, 5000);
        },
    });
});
