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
                <?php echo lang('refered_patient'); ?>
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
                <a href="<?=base_url('finance/Referer')?>" class="btn btn-sm btn-success" type="button" id="add_referal">Add Referal</a>            
                <table id="nhif_referals" style="border-bottom:1px solid black;" class="table table-bordered table-hover ">
                    <thead  class ="bg-info">
                        <tr>
                            <th scope="col" >No.</th>
                            <th scope="col" >Full Name</th>
                            <th scope="col">Card Number</th>
                            <th scope="col">Referring Facility</th>
                            <th scope="col">Authorization Number</th>
                            <th scope="col">Referal Number</th>
                            <th scope="col">Referring Date</th>
                            <th scope="col">Physcian Name</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($referals)) {
                            $no= 1;
                            foreach ($referals as $item) {
                                    echo
                                    '<tr id="'.$item->id.'" class="change_cursor">
                                        <td>'.$no.'</td>
                                        <td>'.$item->PatientFullName.'</td>
                                        <td>'.$item->CardNo.'</td>
                                        <td>'.$item->name.' ('.$item->code.') ('.$item->class.') /<strong>'.strtoupper($item->region).'</td>
                                        <td>'.$item->AuthorizationNo.'</td>
                                        <td>'.$item->ReferralNo.'</td>
                                        <td>'.$item->DateCreated.'</td>
                                        <td>'.$item->PhysicianName.'</td>

                                    </tr>';  
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
