<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        <section class="">
            <header class="panel-heading">
                <?php              
                    echo lang('card_authorization');
                ?>
            </header>
            <?php if ($this->session->flashdata('error')) { ?>
            <div class="alert alert-warning alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
            <?php } ?>


        </section>
        <div class="panel-body">
                <div class="adv-table editable-table ">
                    <?php echo validation_errors(); ?>
                    <?php echo $this->session->flashdata('feedback'); ?>
                </div>
                <form role="form" action="finance/authorize" id= "myForm" class="clearfix row" method="post">
                
                    <div class="col-md-12 panel">
                        <div class="col-md-3 payment_label"> 
                            <label for="cardNo"> <?php echo lang('cardNo'); ?></label>
                        </div>
                        <div class="col-md-9"> 
                            <input type="text" class="form-control" name="cardNo" id="cardNo" placeholder="Enter  a valid card number">
                            <span class="text-small text-danger" id="card_note"></span>
                        </div>
                    </div>
                    <div class="col-md-12 panel">
                        <div class="col-md-3 payment_label"> 
                            <label for="visitType"> <?php echo lang('visity_type'); ?></label>
                        </div>
                        <div class="col-md-9"> 
                            <select name="visitType" id="visitType" autocomplete="off" class="form-control placeholder-no-fix">
                                    <option value="">Select.....</option>
                                    <option value="1">Normal Visit</option>
                                    <option  value="2"> Emergency</option>
                                    <option  value="3"> Referal</option>
                                    <option value="4"> Follow up visit</option>
                                    <option  value="5"> Investigation Only</option>
                                    <option  value="6"> Occupational Visit</option>
                                    
                            </select>
                            <span class="text-small text-danger" id="visit_note"></span>

                        </div>
                    </div>
                    <div class="col-md-12 panel hide_referal">
                        <div class="col-md-3 payment_label"> 
                            <label for="referal_no"> <?php echo lang('referal_no'); ?></label>
                        </div>
                        <div class="col-md-9"> 
                            <input type="text" class="form-control" name="referal_no" id="referal_no" placeholder="Enter Short remarks about the patient or visit">
                            <span class="text-small text-danger" id="referal_note"></span>
                        </div>
                    </div>
                    <div class="col-md-12 panel remark_referal">
                        <div class="col-md-3 payment_label"> 
                            <label for="Remarks"> Remarks</label>
                        </div>
                        <div class="col-md-9"> 
                            <input type="text" class="form-control" name="Remarks" id="Remarks" placeholder="Enter Your Remarks">
                            <span class="text-small text-danger" id="remarks_note"></span>
                        </div>
                    </div>
                    <div class="col-md-12 panel">
                        <div class="col-md-3 payment_label"> 
                        </div>
                        <div class="col-md-9"> 
                            <button type="submit" name="submit" class="btn btn-info pull-right"> <?php echo lang('submit'); ?></button>
                        </div>
                    </div>
                </form>
                

                <!--  Card details -->
                <?php
             
                $datas = json_decode($data);
                ?>
               
        </div>
        <?php if (isset($data) && !empty($data)) {?>
          
       
            <div class="panel-body">
            <?php if(gettype($datas) == 'NULL'){?>
        <!-- <h4 style=" border:1px solid black" class="text-center"></h4>  -->
        <!-- <div class="alert alert-info alert-dismissible "><?=$data?></div> -->

        <div id="show_alert">
            <div  class="alert alert-warning">
                <strong><?=$data?></strong> 
                </div>
        </div>

    <?php
     } else {?>
<input type="hidden" id="authorizedcard" name="card" value="<?=$datas->{'CardNo'}?>">
        <table class="table table-bordered table-hover ">

    <thead class ="bg-info">
    <tr >
        <th scope="col">Card No</th>
        <th scope="col">Membership No</th>
        <th scope="col">Full Name</th>
        <th scope="col">Gender</th>
        <th scope="col">Date Of Birth</th>
        <th scope="col">Age</th>
        <th scope="col">Employer No</th>
        <th scope="col">Employer Name</th>
        <th scope="col">Scheme ID</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td><?=$datas->{'CardNo'}?></td>
        <td><?=$datas->{'MembershipNo'}?></td>
        <td><?=$datas->{'FullName'}?></td>
        <td><?=$datas->{'Gender'}?></td>
        <td><?=$datas->{'DateOfBirth'}?></td>
        <td><?=$datas->{'Age'}?></td>
        <td><?=$datas->{'EmployerNo'}?></td>
        <td><?=$datas->{'EmployerName'}?></td>
        <td><?=$datas->{'SchemeID'}?></td>

        
    </tr>
    
</tbody>
</table> <table class="table table-bordered table-hover ">

<thead class ="bg-info">
<tr >
    <th scope="col">Scheme Name</th>
    <th scope="col">PFNumber</th>
    <th scope="col">Product Code</th>
    <th scope="col">Expiry Date</th>
    <th scope="col">Card Status</th>
    <th scope="col">Authorization Status</th>
    <th scope="col">Authorization No</th>
    <th scope="col">Remarks</th>
</tr>
</thead>
<tbody>
<tr>
    <td><?=$datas->{'SchemeName'}?></td>
    <td><?=$datas->{'PFNumber'}?></td>
    <td><?=$datas->{'ProductCode'}?></td>
    <td><?=$datas->{'ExpiryDate'}?></td>
    <td><b><?=$datas->{'CardStatus'}?></b></td>
    <td><b><?=$datas->{'AuthorizationStatus'}?></b></td>
    <td><?=$datas->{'AuthorizationNo'}?></td>
    <td><?=$datas->{'Remarks'}?></td>
</tr>

</tbody>
</table>
        <?php } ?>
        </div>
        <?php } ?>  
    </section>
</section>

<script src="common/js/codearistos.min.js"></script>

<script>
 $(document).ready(function () {
    $('.hide_referal').hide();
    $('.remark_referal').hide();

        $('#myForm').on('submit', function(e){
           var cardnumber = document.getElementById('cardNo').value;
           var visitType = document.getElementById('visitType').value;
           var referal_no = document.getElementById('referal_no').value;
           var Remarks = document.getElementById('Remarks').value;
           
           if ( cardnumber =='') {
            $('#card_note').html('<i>Please enter Card Number</i>');
            return false;
           }
           if ( visitType =='') {
            $('#visit_note').html('<i>Please select visit type</i>');
            return false;
           }

           if ( (visitType =='4' || visitType =='3') && referal_no =='') {
            $('#referal_note').html('<i>Please enter Referal Number</i>');
            return false;
           }
           if ( visitType =='2' && Remarks =='') {
            $('#remarks_note').html('<i>Please enter Short remarks about the patient or visit</i>');
            return false;
           }

        });
        $('#visitType').on('change', function(e){
           var visitType = document.getElementById('visitType').value;
           if ( (visitType =='4' || visitType =='3')) {
            $('.hide_referal').show();
           }else{
            $('.hide_referal').hide();
             $('#referal_no').val('');
           }
           if ( visitType =='2') {
            $('.remark_referal').show();
           }else{
            $('.remark_referal').hide();
            $('#Remarks').val('');
           }

        });
    });

</script>

