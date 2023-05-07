<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        
        <section class="panel">
            <header class="panel-heading">
                <?php echo lang('claim_consl'); ?>
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
                <form method="POST" action="finance/Reconsiliation" id ="sendReferal">
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
                    ?>
                    <table id="nhif_referals" style="border-bottom:1px solid black;padding-top:25px;" class="table table-bordered table-hover ">
                    <thead  class ="bg-info">
                        <tr>
                            <th scope="col" >No.</th>
                            <th scope="col" >SubmissionNo</th>
                            <th scope="col">DateSubmitted</th>
                            <th scope="col">ClaimYear</th>
                            <th scope="col">ClaimMonth</th>                           
                            <th scope="col">SubmittedBy</th>
                            <th scope="col">CardNo</th>
                            <th scope="col">AuthorizationNo</th>
                            <th scope="col">AmountClaimed</th>
                            <th scope="col">Remarks</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no= 1;
                            $sum =0;
                            foreach ($data as $item) {
                                    echo
                                    '<tr>
                                        <td>'.$no.'</td>
                                        <td>'.$item->SubmissionNo.'</td>
                                        <td>'.$item->DateSubmitted.'</td>
                                        <td>'.$item->ClaimYear.'</td>
                                        <td>'.$item->ClaimMonth.'</td>
                                        <td>'.$item->SubmittedBy.'</td>
                                        <td>'.$item->CardNo.'</td>
                                        <td>'.$item->AuthorizationNo.'</td>
                                        <td  style="text-align:right">'.number_format($item->AmountClaimed,1).'</td>
                                        <td>'.$item->Remarks.'</td>

                                    </tr>';  
                                    $no++; 
                                    $sum+= $item->AmountClaimed;                         
                                    }
                                    echo
                                    '<tr>
                                        <th style ="">Total</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align:right">'.number_format($sum,1).'</th>
                                        <th></th>

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
<script>
   $(document).ready(function () {
    $('#nhif_referals').DataTable();
   
    });
</script>

