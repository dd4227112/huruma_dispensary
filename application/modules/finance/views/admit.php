<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        
        <section class="panel">
            <header class="panel-heading">
                <?php echo lang('admit_patient'); ?>
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
                <form method="POST" action="finance/sendAdmission" id ="sendReferal">
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
                        <div class="form-group col-md-3">
                            <label for="cardNo">Card Number</label>
                            <input type="text" value="" required name="CardNo" readonly class="form-control" id="cardNo" placeholder="Card Number">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="patientFullName">Patient Full Name</label>
                            <input type="text" required readonly name="FullName"  class="form-control" id="patientFullName" placeholder="Patient Full Name">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="Age">Age</label>
                            <input type="number" required name="Age" readonly class="form-control" id="Age">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="gender"> Gender</label>
                            <select name="Gender" class="form-control" id="gender" readonly>
                                <option selected disabled></option>
                                <option value ="Male">Male</option>
                                <option value="Female">Female</option> 


                            </select>  
                           <input type="hidden" value="" required name="SchemeID" readonly class="form-control" id="SchemeID">
                           <input type="hidden" value="<?=date('Y-m-d')?>" required name="DateAdmitted" readonly class="form-control" id="DateAdmitted">
                           <input type="hidden" value="<?=ucwords(strtolower($this->ion_auth->user()->row()->username)); ?>" required name="CreatedBy" readonly class="form-control" id="CreatedBy">

                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="AuthorizationNo">Authorization Number</label>
                            <input type="text" value="" required name="AuthorizationNo" readonly class="form-control" id="AuthorizationNo" placeholder="Card Number">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="AdmissionTypeID"> Admission Type</label>
                            <select name="AdmissionTypeID" class="form-control" id="AdmissionTypeID">
                                <option selected disabled></option>
                                <option value ="21">Normal Admission</option>
                                <option value="22">ICU Admission</option>
                                <option value="23">HDU Admission</option>
                            </select>  
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="DiagnosisAtAdmission">Diagnosis code At Admission</label>
                            <input type="text" value="" required name="DiagnosisAtAdmission"  class="form-control" id="DiagnosisAtAdmission" placeholder="Patient disease code diagnosised at Admission">
                        </div>
                        <div class="form-group col-md-7">
                            <label for="ReasonsForAdmission">Reasons ForAdmission </label>
                            <textarea value="" required name="ReasonsForAdmission" rows="10" class="form-control" id="ReasonsForAdmission" placeholder=""> </textarea> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                        <label for="physicianMobileNo">Physician Mobile Number</label>
                            <input type="text" required name="PhysicianMobileNo" class="form-control" id="physicianMobileNo" placeholder="Physician Mobile Number">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="AdmittingPhysicianName">Admitting Physician Name</label>
                            <select name="AdmittingPhysicianName"  required name="AdmittingPhysicianName" class="form-control" id="AdmittingPhysicianName">
                                <option selected disabled>select.....</option>
                                <?php foreach($doctors as $doctor):?>
                                <option value ="<?=$doctor->name?>"><?=$doctor->name?></option>
                                <?php endforeach?>
                            </select>  
                        </div>
                        <div class="form-group col-md-4">
                            <label for="QualificationID">Physician Qualification </label>                     
                            <select name="QualificationID" required class=" form-control " id="QualificationID">
                            <option  disabled selected>Select your Qualification</option>
                            <?php foreach ($qualifications as $qualification) {?>
                                <option value="<?=$qualification->id?>"><?=$qualification->qualification?></option>
                            <?php }?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row col-md-12">                       
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
                    $('#AuthorizationNo').val(response.AuthorizationNo);
                    $('#patientFullName').val(response.FullName);
                    $('#gender').val(response.Gender);
                    $('#cardNo').val(response.CardNo);
                    $('.enter_auth_number').text('');
                    $('#SchemeID').val(response.SchemeID);
                    $('#Age').val(response.Age);

                    }
                    else {
                        var html = "<div class='alert alert-success alert-dismissible text-center'>";
                                html+="<button type='button' class='close' data-dismiss='alert'>&times;</button>";
                                html+="Authorization Number <b>"+authorizationNo+"</b> was not found in our database";
                                html+="</div>";
                    $('.not_found').html(html); 
                    $('#authorizationNo').val('');
                    $('#AuthorizationNo').val('');
                    $('#patientFullName').val('');
                    $('#SchemeID').val('');
                    $('#Age').val('');
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
