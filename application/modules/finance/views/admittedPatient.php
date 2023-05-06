<!--sidebar end-->
<!--main content start-->
<style>
    .change_cursor:hover {
  cursor: pointer;
}
</style>
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        
        <section class="panel">
            <header class="panel-heading">
                <?php echo lang('admitted_patient'); ?>
            </header>
            <div class="panel-body">
                <?php 
                if ($this->session->flashdata('success')) { ?>
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
                <a href="<?=base_url('finance/admit')?>" class="btn btn-sm btn-primary" type="button" id="add_referal">Admit</a>          
                <a href="<?=base_url('finance/discharge')?>" class="btn btn-sm btn-success" type="button" id="add_referal">Discharge</a>            
                <table id="nhif_referals" style="border-bottom:1px solid black;padding-top:25px;" class="table table-bordered table-hover ">
                    <thead  class ="bg-info">
                        <tr>
                            <th scope="col" >No.</th>
                            <th scope="col" >Full Name</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Card Number</th>
                            <th scope="col">Admission Type</th>
                            <th scope="col">DiagnosisAtAdmission</th>
                            <th scope="col">ReasonsForAdmission</th>
                            <th scope="col">AdmittingPhysicianName</th>
                            <th scope="col">PhysicianMobileNo</th>
                            <th scope="col">Is Discharged</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $admission_type =
                            [ 
                                '21'=>"Normal Admission",
                                '22'=>"ICU Admission",
                                '23'=>"HDU Admission"
                            ];
                        if (!empty($admissions)) {
                            $no= 1;
                            foreach ($admissions as $item) {?>
                                    
                                    <tr id="<?=$item->id?>" class="change_cursor">
                                        <td><?=$no?></td>
                                        <td><?=$item->FullName?></td>
                                        <td><?=$item->Gender?></td>
                                        <td><?=$item->CardNo?></td>
                                        <td><?=$admission_type[$item->AdmissionTypeID]?></td>
                                        <td><?=$item->DiagnosisAtAdmission?></td>
                                        <td><?=$item->ReasonsForAdmission?></td>
                                        <td><?=$item->AdmittingPhysicianName?></td>
                                        <td><?=$item->PhysicianMobileNo?></td>
                                        <td><?=$item->status?"Yes":"No"?></td>
                                    </tr> 
                                    <?php 
                                    $no++;                           
                                    }
                                   
                        }else{
                            echo
                            '<tr>
                                <td>'."No data found".'</td>
                            </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
               
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
    $(document).ready(function () {
    $('#nhif_referals').DataTable();
   
    });
// $(document).ready(function() {
//     $('.js-example-basic-multiple').select2();
// });
var rows = document.querySelectorAll("tr");

for (var i = 0; i < rows.length; i++) {
  rows[i].addEventListener("click", function() {
    var rowId = this.id;
    // alert("You clicked row " + rowId);
  });
}





</script>
