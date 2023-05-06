<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        
        <section class="panel">
            <header class="panel-heading">
                <?php echo lang('nhif_setup'); ?>
            </header>
             <br>
            <div class="panel-body">
                <div class="alert alert-warning  text-center">         
                    <strong class="text-danger"> <i class= "fa fa-warning"></i> <?=lang('nhif_warning')?></strong>
                </div>                   
            </div>
            <div class="panel-body">
            <?php if ($this->session->flashdata('success')) { ?>
            <div class="alert alert-success alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
            <?php } ?>
            <form action="settings/save_setup" method="POST">
                <div class="row">
                    <div class="form-group col-md-3">
                    <label for="environment">Environment</label>
                    <select name="environment" required id="environment" class = "form-control">
                        <option selected value="<?=$setup->environment?>"><?=$setup->environment?></option>
                        <option value="Live">Live</option>
                        <option value="Test">Test</option>
                    </select>
                    </div>
                    <div class="form-group col-md-3">
                    <label for="facility_code">Facility Code</label>
                    <input type="text" name="facility_code" value="<?=$setup->facility_code?>" class="form-control" id="facility_code" required placeholder="facility code">
                    </div>
                    <div class="form-group col-md-3">
                    <label for="username">Username</label>
                    <input type="text" name="username" value="<?=$setup->username?>" class="form-control" id="username" required placeholder="Username">
                    </div>
                    <div class="form-group col-md-3">
                    <label for="password">Password</label>
                    <input type="text" name="password"  value="<?=$setup->password?>" class="form-control" id="password" required placeholder="Password">
                    </div>
                </div>
                <div class="form-group">
                    <label for="service_token_url">Service Server Token</label>
                    <input type="text" name="service_token_url" class="form-control" value="<?=$setup->service_token_url?>" id="service_token_url" required placeholder="Enter url for request of nhif service server token">
                </div>
                
                <div class="form-group">
                    <label for="claim_token_url">Claim Server Token</label>
                    <input type="text" name="claim_token_url" class="form-control" value="<?=$setup->claim_token_url?>" id="claim_token_url" required placeholder="Enter url for request of nhif claims server token">
                </div>
                <div class="form-group">
                    <label for="card_detail_url">Card Details URL</label>
                    <input type="text" name="card_detail_url" class="form-control" value="<?=$setup->card_detail_url?>" id="card_detail_url"required  placeholder="Enter url for requesting card details">
                </div>
                
                <div class="form-group">
                    <label for="authorize_card_url">Authorize Card URL</label>
                    <input type="text" class="form-control" name="authorize_card_url" value="<?=$setup->authorize_card_url?>" id="authorize_card_url" required placeholder="Enter url for card authorization">
                </div>
                <div class="form-group">
                    <label for="price_package_url">Price Package URL</label>
                    <input type="text" class="form-control" value="<?=$setup->price_package_url?>" name="price_package_url" id="price_package_url" required placeholder="Enter url for downloading price packages/excluded services">
                </div>
                
                <div class="form-group">
                    <label for="submit_claim_url">Submit Claim URL</label>
                    <input type="text" class="form-control" name="submit_claim_url" value="<?=$setup->submit_claim_url?>" id="submit_claim_url" required placeholder="Enter url for sendin claims to nhif">
                </div>

                <div class="form-group">
                    <label for="submited_claim_url">Submitted Claim URL </label>
                    <input type="text" class="form-control" value="<?=$setup->submited_claim_url?>" name="submited_claim_url" id="submited_claim_url" required placeholder="Enter url for request all submitted claims">
                </div>
                
                <div class="form-group">
                    <label for="refer_patient_url">Refer Patient URL</label>
                    <input type="text" class="form-control" name="refer_patient_url" value="<?=$setup->refer_patient_url?>" id="refer_patient_url" required placeholder="Enter url to refer patient to anoher hospital">
                </div>

                <div class="form-group">
                    <label for="admit_patient_url">Admit Patient URL</label>
                    <input type="text" class="form-control" name="admit_patient_url" value="<?=$setup->admit_patient_url?>" id="admit_patient_url" required placeholder="Enter url to refer patient to anoher hospital">
                </div>
                <div class="form-group">
                    <label for="discharge_patient_url">Discharge Patient URL</label>
                    <input type="text" class="form-control" name="discharge_patient_url" value="<?=$setup->discharge_patient_url?>" id="discharge_patient_url" required placeholder="Enter url to refer patient to anoher hospital">
                </div>
                <div class="form-group">
                    <label for="claim_reconciliation_url">Claim Reconciliation URL</label>
                    <input type="text" class="form-control" name="claim_reconciliation_url" value="<?=$setup->claim_reconciliation_url?>" id="claim_reconciliation_url" required placeholder="Enter url to refer patient to anoher hospital">
                </div>
                <div class="form-group">
                    <label for="claimed_amount_url">Claimed Amount URL</label>
                    <input type="text" class="form-control" name="claimed_amount_url" value="<?=$setup->claimed_amount_url?>" id="claimed_amount_url" required placeholder="Enter url to refer patient to anoher hospital">
                </div>
                <button type="submit" class="btn btn-primary"  id = "setup">Save</button>
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
// document.addEventListener("DOMContentLoaded", function() {
// // Add an event listener to the form submit button
// document.getElementById("setup").addEventListener("click", function(event) {
//   // Prevent the form from submitting automatically
//   event.preventDefault();
//   // Show the confirmation dialog box using swal.fire()
//   Swal.fire({
//     title: "Are you sure you want to make changes on these settings?",
//     text: "This may cause nhif integration not working. Make sure the information are correct",
//     icon: "warning",
//     showCancelButton: true,
//     confirmButtonColor: "#3085d6",
//     cancelButtonColor: "#d33",
//     confirmButtonText: "Save",
//     cancelButtonText: "Cancel"
//   }).then((result) => {
//     // If the user clicks the "confirm" button, submit the form
//     if (result.isConfirmed) {
//     //   document.getElementById("setup").submit();
//     return true;
//     }
//   });
// });
// });
$(document).ready(function() {
  $('form').submit(function(event) {
    event.preventDefault(); // Prevent the form from submitting automatically

    // Show the confirmation dialog using swal.fire()
    Swal.fire({
        title: "Are you sure you want to make changes on these settings?",
    text: "This may cause nhif integration not working. Make sure the information are correct",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, Save",
      cancelButtonText: "Cancel"
    }).then((result) => {
      // If the user clicks the "confirm" button, submit the form
      if (result.isConfirmed) {
        $(event.target).unbind('submit').submit(); // Unbind the submit event handler and submit the form
      }
    });
  });
});

</script>
