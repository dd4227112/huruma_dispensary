

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous"> -->
<style>
    /* Apply border to the table itself */
.main-table {
  border-collapse: collapse; /* This ensures that adjacent cell borders merge into one */
  width: 100%; /* Optional: Make the table width 100% of its container */
  border: 1px solid #000; /* 1px solid black border around the entire table */
}

/* Apply border to table header cells (th) */
.main-th {
  border: 1px solid #000; /* 1px solid black border for header cells */
  padding: 8px; /* Optional: Add padding for spacing within header cells */
  background-color: #708090; /* Optional: Add a background color for header cells */
  color: #FFFFFF; 
}

.main-td {
  border: 1px solid #000; /* 1px solid black border for data cells */
  padding: 8px; /* Optional: Add padding for spacing within data cells */
}
.corporate-id {
    margin-bottom: 5px;
    /* text-align: center; */
}

</style>
</head>
<body>
<div style ="text-align: center;" class=" corporate-id">


<h3>
    <?php echo $settings->title ?>
</h3>
<h4>
    <?php echo $settings->address ?>
</h4>
<h4>
    Tel: <?php echo $settings->phone ?>
</h4>
<img alt="LOGO" src="uploads/favicon.png" width="200" height="100">
</div>
<h4 style ="text-align:center;"><u>PATIENT'S COMBINED REPORT</u></h4>
<br>
<!-- <table width="100%" class="table" border="1px black;" cellspacing ="0"> -->
<table class="table table-bordered main-table">
   <thead class="table-secondary">
       <tr> <th class="main-th">No.</th>
           <th class="main-th">Patient Name</th>
           <!-- <th class="main-th">Email</th>
           <th class="main-th">Phone</th>
           <th class="main-th">Address</th> -->
           <th class="main-th">Sex</th>
           <th class="main-th">Age(years)</th>
           <th class="main-th">Date</th>
           <th class="main-th">Case History</th> 
           <th class="main-th">Laboratory</th> 
           <th class="main-th">Prescription</th> 
          
       </tr>
   </thead>
   <tbody>
    <?php
$no =1;

   foreach ($patients as $patient) { 
    $case_history = $this->db->where('patient_id', $patient->id)->get('medical_history')->result();
    $prescriptions = $this->db->where('patient', $patient->id)->get('prescription')->result();
    $labs = $this->db->where('patient', $patient->id)->get('lab')->result();
    ?>

    <tr class="">
            <td class="main-td"><?=$no?></td>
            <td class="main-td"><?=$patient->name?></td>
            <!-- <td class="main-td"><?=$patient->email?></td> -->
            <!-- <td class="main-td"><?=$patient->phone?></td> -->
            <!-- <td class="main-td"><?=$patient->address?></td> -->
            <td class="main-td"><?=$patient->sex=='Female'?'F':'M'?></td>
            <?php $birthdate = new DateTime($patient->birthdate);

                // Get the current date
                $currentDate = new DateTime();

                // Calculate the difference in years
                $age = $currentDate->diff($birthdate)->y;
                ?>
            <td class="main-td"><?=$age?></td>
            <td class="main-td"><?=$patient->add_date?></td>
          
            <td class="main-td" style="text-align: left;">
            <?php if(!empty($case_history)){?>
                <table>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                    </tr>
                    <?php 
                    foreach ($case_history as $key => $history) {?>
                      <tr>
                    <td><?=$history->title?></td>
                    <td><?=$history->description?>
                </td>
                    </tr>
                    <?php } ?>
                </table>
            </td>
                <?php
        }else{
            echo "-";
            echo "</td>";
        } ?>


<td class="main-td" style="text-align: left;">
            <?php if(!empty($labs)){?>
                    <?php 
                    foreach ($labs as $key => $lab) {?>
                        <?=$lab->report?>
                    <?php } ?>
                <?php
                 echo "</td>";
        }else{
            echo "-";
            echo "</td>";
        } ?>

        <td class="main-td">
            <?php if(!empty($prescriptions)){?>
                    <?php 
                    foreach ($prescriptions as $key => $prescription) {?>
                    <?php
                        if (!empty($prescription->medicine)) {
                            $medicine = explode('###', $prescription->medicine);

                            foreach ($medicine as $key => $value) {
                                $medicine_id = explode('***', $value);
                                $medicine_details = $this->db->where('id', $medicine_id[0] )->get('medicine')->row();
                                if (!empty($medicine_details)) {
                                    $medicine_name_with_dosage = $medicine_details->name . ' -' . $medicine_id[1];
                                    $medicine_name_with_dosage = $medicine_name_with_dosage . ' | ' . $medicine_id[3] . '<br>';
                                    rtrim($medicine_name_with_dosage, ',');
                                    echo '<p>' . $medicine_name_with_dosage . '</p>';
                                }
                            }
                        }
                    ?>
                    <?php } ?>
                <?php
        }else{
            echo "-";
            echo "</td>";
        } ?>
    </tr>
    <?php
    $no++; 
   } ?>
   </tbody>
   </table>
   </body>
</html>