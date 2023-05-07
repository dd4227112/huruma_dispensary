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
                <?php if(!empty($amountClaimed)){
                    ?>
                    <table id="nhif_referals" style="border-bottom:1px solid black;" class="table table-bordered table-hover ">
                    <thead  class ="bg-info">
                        <tr>
                            <th scope="col" >FacilityCode</th>
                            <th scope="col" >ClaimYear</th>
                            <th scope="col">ClaimMonth</th>
                            <th scope="col">Folios</th>
                            <th scope="col">AmountClaimed</th>

                        </tr>
                    </thead>
                    <tbody><?php
                            $sum= 0;
                            foreach ($amountClaimed as $item) {
                                    echo
                                    '<tr class="change_cursor">
                                        <td>'.$item->FacilityCode.'</td>
                                        <td>'.$item->ClaimYear.'</td>
                                        <td>'.$item->ClaimMonth.'</td>
                                        <td>'.$item->Folios.'</td>
                                        <td>'.$item->AmountClaimed.'</td>
                                    </tr>';  
                                    $no+=$item->AmountClaimed;                           
                                    }
                                    echo
                                    '<tr class="change_cursor">
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>'.$sum.'</td>
                                    </tr>';  
                                   
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
        </section>
        <!-- page end-->
    </section>
</section>
<!--main content end-->
<!--footer start-->

<script src="common/js/codearistos.min.js"></script>
