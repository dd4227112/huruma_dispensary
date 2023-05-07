<style> 
    .mylist
    {
        list-style-type: none;
        border-bottom: 1px solid #eaeae1;
        border-left: 1px solid #eaeae1; 
        border-right: 1px solid #eaeae1;
        font-size: 18px;
        padding-top: 15px;
    }

    .select_product:hover
    {
        cursor:pointer;
        background-color:#66afe9;
    }
    #remove
    {
        cursor:pointer;
    }
    #sub_total 
    {
        border: 0;
        outline: none;
        background-color: inherit;
    }
    #product_price, #product_quantity{
        border: none;
    }

</style>
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        
        <section class="panel">
            <header class="panel-heading">
                <?php echo lang('claim_submit'); ?>
            </header>
             <div class="panel-body">
                <!-- success message start -->
                <?php if ($this->session->flashdata('success')) { ?>
                <div class="alert alert-success alert-dismissible text-center">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
                <?php } ?>
                <!-- success message end -->

                <!-- error message start -->
                <?php if ($this->session->flashdata('error')) { ?>
                <div class="alert alert-danger alert-dismissible text-center">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
                <?php } ?>
                <!-- error message end -->
                <!-- Folio Details start -->
                    <div class="not_found">
                    </div>
                    <form method="POST" action="finance/claimSubmission" id ="claimSubmission" enctype="multipart/form-data">
                        <div class="form-row col-md-12">
                        <div class="form-group">
                            <label for="authorization">Authorization Number</label>
                            <div class="input-group">
                            <input type="text" value="" class="form-control" id="authorization" placeholder="Authorization Number" aria-label="Authorization Number" aria-describedby="searchButton">
                            <span class="small text-danger enter_auth_number"></span>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">Search</button>
                            </div>
                            </div>
                            <hr style="border: 0.5px solid blue;">
                        </div>
                        </div>
                       
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="SerialNo">Serial Number</label>
                                <input type="text" value="" required name="SerialNo"  class="form-control" id="SerialNo" placeholder="Serial Number">
                            </div>
                            <div class="form-group col-md-6">
                            <label for="AuthorizationNo">Authorization Number</label>
                                <input type="text" required readonly name="AuthorizationNo" value="" class="form-control" id="authorizationNo" placeholder="Authorization Number" aria-label="Authorization Number">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="CardNo">Card Number</label>
                                <input type="text" value="" required name="CardNo" readonly class="form-control" id="CardNo" placeholder="Card Number">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="FirstName">Patient First Name</label>
                                <input type="text" required readonly name="FirstName"  class="form-control" id="FirstName" placeholder="Patient Full Name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="LastName">Patient Last Name</label>
                                <input type="text" required readonly name="LastName"  class="form-control" id="LastName" placeholder="Patient Full Name">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="Gender"> Gender</label>
                                <select name="Gender" class="form-control" id="gender" readonly>
                                    <option selected disabled></option>
                                    <option value ="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>  
                            </div>
                            <div class="form-group col-md-4">
                                <label for="DateOfBirth">Date Of Birth</label>
                                <input type="date" value="" readonly required name="DateOfBirth"  class="form-control" id="DateOfBirth" placeholder="Date Of Birth">
                            </div>
                            <div class="form-group col-md-4">
                            <label for="TelephoneNo">Telephone Number</label>
                                <input type="text" required  name="TelephoneNo" value="" class="form-control" id="TelephoneNo" placeholder="Telephone Number">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="PatientFileNo">Patient File Number</label>
                                <input type="text" value="" required name="PatientFileNo"  class="form-control" id="PatientFileNo" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="PatientFile">Patient File</label>
                                <input type="file" required  name="PatientFile" value="" class="form-control" id="PatientFile" placeholder="">
                                <span class="text-danger PatientFile"></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="ClaimFile">Claim File</label>
                                <input type="file" required  name="ClaimFile" value="" class="form-control" id="ClaimFile" placeholder="">
                                <span class="text-danger ClaimFile"></span>

                            </div>

                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="PatientTypeCode">Patient Type </label>
                                <select name="PatientTypeCode" class="form-control" id="PatientTypeCode">
                                    <option selected disabled>select.....</option>
                                    <option value ="OUT">OUT PATIENT</option>
                                    <option value="IN">IN PATIENT</option>
                                </select>  
                            </div>
                            <div class="form-group col-md-3">
                                <label for="AttendanceDate">Attendance Date</label>
                                <input type="date" required  name="AttendanceDate" value="" class="form-control" id="AttendanceDate" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="DateAdmitted">Date Admitted</label>
                                <input type="date" name="DateAdmitted" value="" class="form-control" id="DateAdmitted" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="DateDischarged">Date Discharged</label>
                                <input type="date"  name="DateDischarged" value="" class="form-control" id="DateDischarged" placeholder="">
                            </div>
                           
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="PractitionerNo">Practitioner Name </label>
                                <select name="PractitionerNo" class="form-control" id="PractitionerNo">
                                    <option selected disabled>select.....</option>
                                    <?php foreach($doctors as $doctor):?>
                                    <option value ="<?=$doctor->regNo?>"><?=$doctor->name?></option>
                                  <?php endforeach?>
                                </select>  
                            </div>
                            <div class="form-group col-md-6">
                                <label for="CreatedBy">Created By</label>
                                <input type="text" required readonly  name="CreatedBy" value="<?php echo ucwords(strtolower($this->ion_auth->user()->row()->username)); ?>" class="form-control" id="CreatedBy" placeholder="">
                            </div>                          
                        </div>
                        <input type="hidden" name="SchemeID" id="SchemeID" value="">
                        <div class="form-row">
                        <div class="form-group col-md-6">
                                <label for="DiseaseCode">Diagnosis/Desease code</label>
                                <table id="diseasecodetable" class="table table-bordered table-hover ">
                                <thead class ="bg-info">
                                <tr >
                                    <th scope="col">Disease Code</th>
                                    <th scope="col">Status</th>
                                    <th scope="col"><button id="adddiseasecode">+</button></th>                            
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            </div> 
                            <div class="form-group col-md-6">
                                <label for="ItemCode">Items</label>

                                <?php echo form_input('ItemCode', '', 'class="form-control " id="ItemCode" placeholder="Type at least three characters"'); ?>
                                <div class="clearfix"></div>                                
                                <div id="result" class="col-lg-12">
                                </div>

                            </div>                                             
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                            <table id="Items_selected" class="table table-bordered table-hover ">
                                <thead class ="bg-info">
                                <tr >
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Unit Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Amount Claimed</th>
                                    <th scope="col"></th>                            
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                        <button type="submit" class="btn btn-info pull-right">Submit</button>
                        </div>
                    </form>
                    <!-- Folio Details end -->
             </div>                  
            </div>
        </section>
        <!-- page end-->
    </section>
</section>
<!--main content end-->
<!--footer start-->

<script src="common/js/codearistos.min.js"></script>

<script src="common/js/codearistos.min.js"></script>
<script>
        // document.getElementById('searchButton').addEventListener('click',  function(event){
            $(document.body).on('click', '#searchButton', function () {
            var authorizationNo = document.getElementById('authorization').value;
            if (authorizationNo =='') {
                $('.enter_auth_number').text('Please enter Patient Authorization Number');
                return false;
            }
            
        $.ajax({
                url: 'finance/seachByAthorizationNumber?authorizationNo=' + authorizationNo,
                method: 'GET',
                data: '',
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    if(response != ''){
                    $('#authorizationNo').val(response.AuthorizationNo);
                    $('#LastName').val(response.LastName);
                    $('#FirstName').val(response.FirstName);
                    $('#gender').val(response.Gender);
                    $('#CardNo').val(response.CardNo);
                    $('#DateOfBirth').val(response.DateOfBirth);
                    $('.enter_auth_number').text('');
                    $('#authorization').val('');
                    $('#SchemeID').val(response.SchemeID);

                    


                    }
                    else {
                        var html = "<div class='alert alert-success alert-dismissible text-center'>";
                                html+="<button type='button' class='close' data-dismiss='alert'>&times;</button>";
                                html+="Authorization Number <b>"+authorizationNo+"</b> was not found in our database";
                                html+="</div>";
                    $('.not_found').html(html);
                    $('#authorizationNo').val('');
                    $('#LastName').val('');
                    $('#FirstName').val('');
                    $('#gender').val('');
                    $('#CardNo').val('');
                    $('.enter_auth_number').text('');
                    $('#authorization').val('');
                    $('#DateOfBirth').val('');
                    $('#SchemeID').val('');




                    }
                    
                }
            });
        });
    $(document.body).on('keyup', '#ItemCode', function () {
        var searchValue = $("#ItemCode").val();
        if (searchValue == '' || searchValue.length <=3) {
            $('#result').html(" ");
        }
        if (searchValue != '' && searchValue.length >=3) {
            $.ajax({
                method: "GET",
                url: "finance/getPriceList",
                data: {search: searchValue, schemeId:$('#SchemeID').val()},
                success: function(data) {
                    $('#result').html(data);  
                }
            });
        }
    }); 
    $(document).on("click", "#adddiseasecode", function(event){
        event.preventDefault();
        
        var data =" <tr>"+
                    "<td><input class ='text-center form-control'  type ='text' id ='UnitPrice' name ='DiseaseCode[]'></td>"+
                                    "<td>"+
                                        "<select name='Status[]' class='form-control' id='Status'>"+
                                            "<option selected disabled>select.....</option>"+
                                            "<option value ='Provisional'>Preliminary diagnosis</option>"+
                                            "<option value='Final'>Confirmed diagnosis</option>"+
                                        "</select> "+
                                   "</td>"+
                                    "<td  class = 'text-center' title ='delete' id='remove'><i  class='fa fa-minus' aria-hidden='true'></i></td>"+
                                "</tr>";
        $("#diseasecodetable").append(data); 

    });
    
    $(document).on("click", ".select_product", function() {
        var itemId = $(this).attr("id");
        //   var summaryRow = $('<tr><td colspan ="3">Total</td><td class="text-center" colspan="2">0.00</td></tr>');
        $.ajax({
            method: "GET",
            url: "finance/getItemSelected",
            data: {itemId: itemId},
            success: function(data) {
                console.table(data);
                $('#result').html(''); 
                $("#Items_selected").append(data); 
                // $("#quTable tbody").append(summaryRow); 
                // getTotalItems();
                // calculteTotalAmount();
            }
        });
    });
    $(document).on("click", "#remove", function() {
    $(this).closest('tr').remove();
    // getTotalItems();
    // calculteTotalAmount();
});
$(document).on("change", "#ItemQuantity", function() {
    var row = $(this).closest('tr');
    var a = row.children().children('#UnitPrice').val();
    var b = row.children().children('#ItemQuantity').val();
    var sub_total = a*b;
    row.children().children('#AmountClaimed').val(sub_total);
    // calculteTotalAmount();
    
});


$(document).on("change", "#PatientTypeCode", function() {
    var PatientTypeCode = $(this).val();
    // var a = row.children().children('#UnitPrice').val();
    // var b = row.children().children('#ItemQuantity').val();
    // var sub_total = a*b;
    // row.children().children('#AmountClaimed').val(sub_total);
    // calculteTotalAmount();
    if (PatientTypeCode =='OUT') {
        $('#DateAdmitted').attr('disabled', 'disabled');
        $('#DateDischarged').attr('disabled', 'disabled');
    }else{
        $('#DateAdmitted').removeAttr('disabled');
        $('#DateDischarged').removeAttr('disabled');
    }
   
    
});

$(document).ready(function() {
  $('#claimSubmission').submit(function(e) {
    var fileInput1 = $('#PatientFile');
    var fileInput2 = $('#ClaimFile');

    
    var filePath1 = fileInput1.val();
    var filePath2 = fileInput2.val();

    var extension1 = filePath1.substring(filePath1.lastIndexOf('.') + 1).toLowerCase();
    var extension2 = filePath2.substring(filePath2.lastIndexOf('.') + 1).toLowerCase();


    if (extension1 !== 'pdf') {
      e.preventDefault();
      $('.PatientFile').text('Please upload a PDF file.');
      return false;
    }
    if (extension2 !== 'pdf') {
      e.preventDefault();
      $('.ClaimFile').text('Please upload a PDF file.');
      return false;
     
    }  
    return true;
  });
});

</script>

