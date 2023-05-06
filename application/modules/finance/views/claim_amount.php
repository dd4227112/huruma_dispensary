<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        
        <section class="panel">
            <header class="panel-heading">
                <?php echo lang('claim_amount'); ?>
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
                <form method="POST" action="finance/getClaimAmount" id ="sendReferal">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="FacilityCode">Facility Code</label>
                            <input type="text" value="<?=FACILITY_CODE?>" readonly required name="FacilityCode" readonly class="form-control" id="FacilityCode">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="ClaimYear">Claim Year</label>
                            <input type="text" required  name="ClaimYear"  class="form-control" id="ClaimYear">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="ClaimMonth">Claim Month</label>
                            <input type="text" required name="ClaimMonth" class="form-control" id="ClaimMonth">
                        </div>
                    <div class="form-row col-md-12">                       
                    <button type="submit" class="btn btn-info pull-right">Submit</button>
                    </div>
                </div>
                </form>
                <?php if(!empty($data)){
                    echo json_encode($data, true);
                    ?>
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
                        <?php if (!empty($referals)) {
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
                <?php }?>
           </div>
        </section>
        <!-- page end-->
    </section>
</section>
<!--main content end-->
<!--footer start-->

<script src="common/js/codearistos.min.js"></script>
