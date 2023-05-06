<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        <section class="">
            <header class="panel-heading">
                <?php              
                    echo lang('l_nhif_tariffs');
                ?>
            </header>
            <?php if ($this->session->flashdata('error')) { ?>
            <div class="alert alert-warning alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
            <?php } ?>

            <?php if ($this->session->flashdata('success')) { ?>
            <div class="alert alert-success alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
            <?php } ?>


        </section>
<p>
  <a class="btn btn-primary" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">Price Packages</a>
  <a class="btn btn-primary" data-toggle="collapse" href="#multiCollapseExample2" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">Excluded Services</a>
</p>  
        <div class="collapse multi-collapse" id="multiCollapseExample1">
            <div class="panel-body">
                <h3 class = "text-center">NHIF PRICE PACKAGES</h3>
            <a href="<?=base_url('finance/pricePackage')?>" class="btn btn-sm btn-success" type="button" id="download1">Download Update</a>            
            <br>
                <table id="nhif_packages" style="border-bottom:1px solid black;" class="table table-bordered table-hover ">
                    <thead  class ="bg-info">
                        <tr>
                            <th scope="col" style="width:10%;">Item Code</th>
                            <th scope="col" style="width:40%;">Item Name</th>
                            <th scope="col" style="width:10%;">Package ID</th>
                            <th scope="col"style="width:10%;">Scheme ID</th>
                            <th scope="col" style="width:15%;">Unit Price</th>
                            <th scope="col" style="width:15%;">Is Restricted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pricePackage)) {
                            
                            foreach ($pricePackage as $item) {
                                    echo
                                    '<tr>
                                        <td>'.$item->ItemCode.'</td>
                                        <td>'.$item->ItemName.'</td>
                                        <td>'.$item->PackageID.'</td>
                                        <td>'.$item->SchemeID.'</td>
                                        <td>'.number_format($item->UnitPrice,2).'</td>
                                        <td>'.($item->IsRestricted ? 'Yes' : 'No').'</td>
                                    </tr>';                             
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
    <div class="collapse multi-collapse" id="multiCollapseExample2">
    <div class="panel-body">
    <h3 class = "text-center">NHIF EXCLUDED SERVICES</h3>
            <a href="<?=base_url('finance/excludedServices')?>" class="btn btn-sm btn-success" type="button" id="download2">Download Update</a>            
            <br>
                <table id="nhif_services" style="border-bottom:1px solid black;" class="table table-bordered table-hover ">
                    <thead  class ="bg-info">
                        <tr>
                            <th scope="col" style="width:10%;">Item Code</th>
                            <th scope="col"style="width:10%;">Scheme ID</th>
                            <th scope="col" style="width:40%;">SchemeName</th>
                            <th scope="col" style="width:40%;">ExcludedForProducts</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($services)) {
                            
                            foreach ($services as $item) {
                                    echo
                                    '<tr>
                                        <td>'.$item->ItemCode.'</td>
                                        <td>'.$item->SchemeID.'</td>
                                        <td>'.$item->SchemeName.'</td>
                                        <td>'.$item->ExcludedForProducts .'</td>
                                    </tr>';                             
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
    </section>
</section>

<script src="common/js/codearistos.min.js"></script>

<script>
    $(document).ready(function () {
    $('#nhif_packages').DataTable();
     $('#nhif_services').DataTable();
    
});
   // Add an event listener to the link
document.getElementById("download1").addEventListener("click", function(event) {
  // Prevent the link from opening automatically
  event.preventDefault();

  // Show the confirmation dialog box using Swal.fire()
  Swal.fire({
    title: "Make sure you have a Strong Internet Connection",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Okay",
    cancelButtonText: "Cancel"
  }).then((result) => {
    // If the user clicks the "confirm" button, open the link
    if (result.isConfirmed) {
      window.location.href = event.target.href;
    }
  });
});
   // Add an event listener to the link
   document.getElementById("download2").addEventListener("click", function(event) {
  // Prevent the link from opening automatically
  event.preventDefault();

  // Show the confirmation dialog box using Swal.fire()
  Swal.fire({
    title: "Make sure you have a Strong Internet Connection",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Okay",
    cancelButtonText: "Cancel"
  }).then((result) => {
    // If the user clicks the "confirm" button, open the link
    if (result.isConfirmed) {
      window.location.href = event.target.href;
    }
  });
});

</script>

