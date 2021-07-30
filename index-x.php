<?php
include 'chklogin.php';
include 'config.php';
include 'dbconnect.php';
?>
<?php
if ($_GET[report] == 1) {
  $search_url = "search-1.php";
  $subtitle = "หน่วยเลือกตั้งที่รายงานผลแล้ว";
} elseif ($_GET[report] == 2) {
  $search_url = "search-2.php";
  $subtitle = "หน่วยเลือกตั้งที่ยังไม่รายงานผล";
} else {
  $search_url = "search.php";
  $subtitle = "หน่วยเลือกตั้งทั้งหมด";
}
$title = "กรอก" . $title;


$sql = " SELECT * FROM `polling_station" . $table . "`";
$result = mysqli_query($conn, $sql);
$unit[0]=mysqli_num_rows($result);

$sql = " SELECT * FROM `polling_station" . $table . "` WHERE `score` != '' ";
$result = mysqli_query($conn, $sql);
$unit[1]=mysqli_num_rows($result);

$sql = " SELECT * FROM `polling_station" . $table . "` WHERE `score` = '' ";
$result = mysqli_query($conn, $sql);
$unit[2]=mysqli_num_rows($result);



?>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo $title . "(" . $subtitle . ")"; ?></title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
<!--
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
-->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
 </head>
 <body>
  <div class="container">
    ค้นหาหน่วยเลือกตั้ง : <a href="index-x.php">ทั้งหมด [<?php echo $unit[0]; ?>]</a> | <a href="index-x.php?report=1">รายงานแล้ว [<?php echo $unit[1]; ?>]</a> | <a href="index-x.php?report=2">ยังไม่รายงาน [<?php echo $unit[2]; ?>]</a>
&nbsp; | &nbsp; <a href="show-by-zone.php" target="_blank">ลำดับคะแนน</a> &nbsp; | &nbsp; <a href="logout.php">Logout</a>
   <br />
   <h2 align="center"><?php echo $title; ?></h2>
   <h3 align="center"><?php echo "(" . $subtitle . ")"; ?></h3>
   <br />
   <div class="form-group">
    <div class="input-group">
     <span class="input-group-addon">ค้นหา</span>
     <input type="text" name="search_text" id="search_text" placeholder="ชื่อสถานที่ตั้งหน่วยเลือกตั้ง" class="form-control" />
   </div>
   </div>
   <br />
   <div id="result"></div>
  </div>
 </body>
</html>

<script>
$(document).ready(function(){

 load_data();

 function load_data(query)
 {
  $.ajax({
   url:"<?php echo $search_url; ?>",
   method:"POST",
   data:{query:query},
   success:function(data)
   {
    $('#result').html(data);
   }
  });
 }
 $('#search_text').keyup(function(){
  var search = $(this).val();
  if(search != '')
  {
   load_data(search);
  }
  else
  {
   load_data();
  }
 });
});
</script>
