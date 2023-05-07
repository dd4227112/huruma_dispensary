<style> 
    .mylist
    {
        list-style-type: none;
        border-bottom: 1px solid #eaeae1;
        border-left: 1px solid #eaeae1; 
        border-right: 1px solid #eaeae1;
        font-size: 18px;
        padding-top: 15px;
    }

    .select_product:hover
    {
        cursor:pointer;
        background-color:#66afe9;
    }
    #remove
    {
        cursor:pointer;
    }
    #sub_total 
    {
        border: 0;
        outline: none;
        background-color: inherit;
    }
    #product_price, #product_quantity{
        border: none;
    }

</style>
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        
        <section class="panel">
            <header class="panel-heading">
                <?php echo "Data Comparisons"; ?>
            </header>
             <div class="panel-body">
                <!-- success message start -->
                <?php if ($this->session->flashdata('success')) { ?>
                <div class="alert alert-success alert-dismissible text-center">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
                <?php } ?>
                <!-- success message end -->

                <!-- error message start -->
                <?php if ($this->session->flashdata('error')) { ?>
                <div class="alert alert-danger alert-dismissible text-center">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
                <?php } ?>
                <!-- error message end -->
                <!-- Folio Details start -->
                <a class="btn btn-sm btn-primary" href="<?=base_url('settings/nhif_setup')?>">Back</a>


                                <table  class="table table-bordered table-hover ">
                                <thead class ="bg-info">
                                <tr >
                                    <th scope="col">Table name</th>
                                    <th scope="col">New Database</th>
                                    <th scope="col">Old Database</th>                            
                                </tr>
                                </thead>
                                <?php
                                $tables =$this->db->query("select TABLE_NAME as table_name from information_schema.tables where table_schema ='huduma' and TABLE_NAME IN (select TABLE_NAME from information_schema.tables where table_schema ='hospital')")->result();
                                foreach($tables as $table){
                                    
                                    $data = $this->db->query("select count(*) as count1,  (select count(*) from hospital.".$table->table_name.") as count2 from ".$table->table_name)->row();
                                     ?>
                                     <tr>
                                        <td><?=$table->table_name?></td>
                                        <td><?=$data->count1?></td>
                                        <td><?=$data->count2?></td>
                                     </tr>
                                    <?php }?>
                                   
                                <tbody>
                                </tbody>
                            
                    <!-- Folio Details end -->
             </div>                  
        </section>
        <!-- page end-->
    </section>
</section>
<!--main content end-->
<!--footer start-->

<!-- <script src="common/js/codearistos.min.js"></script> -->

  


