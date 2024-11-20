$("#verifyBank").on("click", function (event) {
    // Stop the button from submitting the form:
    event.preventDefault();

    let data = new FormData(this.form);
    let validationInfo = document.getElementById("validation-info");
    let download = document.getElementById("download");

    $.ajax({
        type: "post",
        url: "/retrieveBank",
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
               <th scope="row" rowspan="11">
                  <img class="rounded" src="assets/images/identity.png" alt="User Image" style="width: 250px; height: 250px;">
               </th>
            </tr>
             <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Account Type</th>
               <td style="text-align:left" ><span id="bvnno" >${result.data.data.account_type}</span>
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Identity Number</th>
               <td style="text-align:left" ><span id="bvnno" >${result.data.data.identity_number}</span>
               </td>
            </tr>
             <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Identity Type</th>
               <td style="text-align:left" ><span id="bvnno" >${result.data.data.identity_type}</span>
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">First Name</th>
               <td  style="text-align:left">${result.data.data.first_name}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Surname</th>
               <td  style="text-align:left">${result.data.data.last_name}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Middle Name</th>
               <td  style="text-align:left">${result.data.data.other_names}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Phone No</th>
               <td  style="text-align:left">${result.data.data.phone}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Gender</th>
               <td  style="text-align:left">${result.data.data.gender}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Email</th>
               <td  style="text-align:left">${result.data.data.email}
               </td>
            </tr>
            <tr>
               <th scope="row" style="text-align:right; border: none ! important;">Address</th>
               <td  style="text-align:left">${result.data.data.address_1}
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

$(document).ready(function () {
    $.ajax({
        //create an ajax request to get session data
        type: "get",
        url: "fetchBanks",
        dataType: "json", //expect json File to be returned
        success: function (response) {
            var len = response.length;
            $("#banks").empty();
            $("#banks").append(
                "<option value=''>" + "Choose Bank..." + "</option>"
            );

            for (var i = 0; i < len; i++) {
                var code = response[i]["code"];
                var bank = response[i]["name"];

                $("#banks").append(
                    "<option value='" + code + "'>" + bank + "</option>"
                );
            }
        },
        error: function (data) {
            //Do Nothing
        },
    });
});

function printDiv(divId) {
    var divContents = document.getElementById(divId).innerHTML;
    var a = window.open("", "", "height=700, width=700");
    a.document.write("<html>");
    a.document.write("<head><title>Print</title></head>");
    a.document.write("<body>");
    a.document.write(divContents);
    a.document.write("</body></html>");
    a.document.close();
    a.print();
}
