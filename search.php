<?php
include 'chklogin.php';
include 'config.php';
include 'dbconnect.php';
$output = '';
if(isset($_POST["query"]))
{
 $search = mysqli_real_escape_string($conn, $_POST["query"]);
 $sql = "
  SELECT * FROM `polling_station" . $table . "` WHERE tambon LIKE '%".$search."%'
  OR station LIKE '%".$search."%'
  ";
}
else
{
 $sql = "
  SELECT * FROM `polling_station" . $table . "` ORDER BY id
 ";
}

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0) {
 $output .= '
  <div class="table-responsive">
   <table class="table table-hover">
    <tr>
     <th>เขต</th>
     <th>หน่วยที่</th>
     <th>สถานที่</th>
     <th>ตำบล</th>
     <th>อำเภอ</th>
     <th>รายงาน</th>
    </tr>
 ';
 while($row = mysqli_fetch_array($result))
 {
  $output .= '
   <tr>
    <td>'.$row["zone"].'</td>
    <td>'.$row["no_station"].'</td>
    <td>'.$row["station"].'</td>
    <td>'.$row["tambon"].'</td>
    <td>'.$row["amper"].'</td>
    <td>
    ';

      if (empty($row["score"])) {
        $show_link = '<a href="survey-results.php?id_station='.$row["id"].'" ><i class="fa fa-edit fa-sm" style="color: SteelBlue;"></i></a>';
      } else {
        $show_link = '<a href="survey-results.php?id_station='.$row["id"].'" ><i class="fas fa-check fa-xs" style="color: green;"></i></a>';
      }

  $output .= $show_link . '
    </td>
   </tr>
  ';
 }
 echo $output;
}
else
{
 echo 'Data Not Found';
}

?>
