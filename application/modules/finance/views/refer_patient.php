<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        
        <section class="panel">
            <header class="panel-heading">
                <?php echo lang('patient_ref'); ?>
            </header>
             <div class="panel-body">
                <?php if ($this->session->flashdata('success')) { ?>
                <div class="alert alert-success alert-dismissible text-center">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
                <?php } 
                if ($this->session->flashdata('error')) { ?>
                <div class="alert alert-danger alert-dismissible text-center">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
                <?php } ?>
                <div class="not_found">

                </div>
                <form method="POST" action="finance/sendReferal" id ="sendReferal">
                    <div class="form-row col-md-12">
                    <div class="form-group">
                        <label for="authorizationNo">Authorization Number</label>
                        <div class="input-group">
                        <input type="text" required  name="authorizationNo" value="" class="form-control" id="authorizationNo" placeholder="Authorization Number" aria-label="Authorization Number" aria-describedby="searchButton">
                        <span class="small text-danger enter_auth_number"></span>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="searchButton">Search</button>
                        </div>
                        </div>
                    </div>
                    </div>
                    
                    <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="cardNo">Card Number</label>
                        <input type="text" value="" required name="cardNo" readonly class="form-control" id="cardNo" placeholder="Card Number">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="patientFullName">Patient Full Name</label>
                        <input type="text" required name="patientFullName"  class="form-control" id="patientFullName" placeholder="Patient Full Name">
                    </div>
                    </div>
                    <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gender">Patient Gender</label>
                        <select name="gender" class="form-control" id="gender" readonly>
                            <option selected disabled></option>
                            <option value ="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>  
                    </div>
                    <div class="form-group col-md-6">
                    <label for="physicianMobileNo">Physician Mobile Number</label>
                        <input type="text" required name="physicianMobileNo" class="form-control" id="physicianMobileNo" placeholder="Physician Mobile Number">
                    </div>
                    </div>
                    <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="physicianName">Physician Name</label>
                        <select name="physicianName"  required name="physicianName" class="form-control" id="physicianName">
                            <option selected disabled>select.....</option>
                            <?php foreach($doctors as $doctor):?>
                            <option value ="<?=$doctor->name?>"><?=$doctor->name?></option>
                            <?php endforeach?>
                        </select>  
                    </div>
                    <div class="form-group col-md-6">
                        <label for="physicianQualificationID">Physician Qualification </label>                     
                        <select name="physicianQualificationID" required class=" form-control js-example-basic-multiple" placeholder="Select your Qualification" id="physicianQualificationID">
                        <option  disabled selected>Select your Qualification</option>
                        <?php foreach ($qualifications as $qualification) {?>
                            <option value="<?=$qualification->id?>"><?=$qualification->qualification?></option>
                        <?php }?>
                        </select>
                    </div>
                    </div>
                    <div class="form-row">
                    <div class="form-group col-md-6">
                    <label for="serviceIssuingFacilityCode">Service Issuing Facility </label>
                    <select name="serviceIssuingFacilityCode" required  class=" form-control js-example-basic-multiple" id="serviceIssuingFacilityCode">
                        <option  disabled selected>Select...........</option>
                        <?php foreach ($facilities as $facility) {?>
                            <option value="<?=$facility->code?>"><?php echo $facility->name." (".$facility->code.") (".$facility->class.") /<strong>".strtoupper($facility->region)."</strong>";?></option>
                        <?php }?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                    <label for="referringDiagnosis">Referring Diagnosis</label>
                    <input type="text" required name="referringDiagnosis" class="form-control" id="referringDiagnosis" placeholder="Referring Diagnosis">
                    </div>
                    </div>
                    <div class="form-row col-md-12">
                        <div class="form-group">
                        <label for="reasonsForReferral">Reasons for Referral</label>
                        <textarea class="form-control" required name="reasonsForReferral" id="reasonsForReferral" rows="5"></textarea>
                        </div>
                    <button type="submit" class="btn btn-info pull-right">Submit</button>
                    </div>
                </form>
           </div>
        </section>
        <!-- page end-->
    </section>
</section>
<!--main content end-->
<!--footer start-->

<script src="common/js/codearistos.min.js"></script>
<script>
        // document.getElementById('searchButton').addEventListener('click',  function(event){
            $(document.body).on('click', '#searchButton', function () {
            var authorizationNo = document.getElementById('authorizationNo').value;
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
                    $('#patientFullName').val(response.FullName);
                    $('#gender').val(response.Gender);
                    $('#cardNo').val(response.CardNo);
                    $('.enter_auth_number').text('');

                    }
                    else {
                        var html = "<div class='alert alert-success alert-dismissible text-center'>";
                                html+="<button type='button' class='close' data-dismiss='alert'>&times;</button>";
                                html+="Authorization Number <b>"+authorizationNo+"</b> was not found in our database";
                                html+="</div>";
                    $('.not_found').html(html);
                    $('#authorizationNo').val('');
                    $('#patientFullName').val('');
                    $('#gender').val('');
                    $('#cardNo').val('');
                    $('.enter_auth_number').text('');

                    }
                    
                }
            });
        });
// $(document).ready(function() {
//     $('.js-example-basic-multiple').select2();
// });

  </script>
