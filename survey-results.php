<?php
include 'chklogin.php';
include 'config.php';
include 'dbconnect.php';
$title = "กรอก".$title;
$subtitle = "คะแนนรวมของหน่วยเลือกตั้ง";
?>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo $title . "(" . $subtitle . ")"; ?></title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 </head>
 <body>
  <div class="container">
    <a href="index-x.php">ค้นหาหน่วยเลือกตั้ง</a>
   <br />
   <h2 align="center"><?php echo $title; ?></h2>
   <h4 align="center"><?php echo "(" . $subtitle . ")"; ?></h4>
<?php
   $output = '';
   if(isset($_GET["id_station"])) {
    $search = mysqli_real_escape_string($conn, $_GET["id_station"]);
    $sql = "
     SELECT * FROM `polling_station" . $table . "` WHERE id = '$search'
     ";
   } else {
     exit();
   }
   $result = mysqli_query($conn, $sql);

   if(mysqli_num_rows($result) > 0)
   {
    $output .= '
    <div class="table-responsive">
      <table class="table">
       <tr>
       <th>เขต</th>
       <th>หน่วยที่</th>
       <th>สถานที่</th>
       <th>ตำบล</th>
       <th>อำเภอ</th>
       </tr>
    ';
    while($row = mysqli_fetch_array($result)) {
      $score_array = json_decode($row["score"],TRUE);
      #print_r($score_array);

      $zone = $row["zone"];
      $no_station = $row["no_station"];
      echo '<h3 class="text-center">เขต ' . $zone . ' หน่วยที่ ' . $no_station . '</h3>';
     $output .= '
      <tr>
       <td class="center">'.$row["zone"].'</td>
       <td>'.$row["no_station"].'</td>
       <td>'.$row["station"].'</td>
       <td>'.$row["tambon"].'</td>
       <td>'.$row["amper"].'</td>
      </tr>
     ';
     $timestamp = $row["timestamp"];
     $jsonCardVote = $row["card_vote"];
    }
      $cardVote = json_decode($jsonCardVote, true);

      $output .= '</table>';
      $output .= '<p class="text-left">อัพเดทล่าสุด : '.$timestamp.'</p><br>';


    $sql = "SELECT * FROM `candidate" . $table . "` WHERE `zone` = 0 OR `zone` = $zone ORDER BY `zone`, `no_candidate` ASC";

    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
      $output .= '
      <form class="form-inline" action="update_score.php" method="POST">
      <input type="hidden" id="polling_station_id" name="polling_station_id" value="'.$_GET["id_station"].'">
      <table class="table table-hover">

      ';
      while($row = mysqli_fetch_array($result)) {
        if ($score_array[$row['id']] > 0) {
          $placeholder_score = $score_array[$row['id']];
          $value_score = $score_array[$row['id']];
        } else {
          $placeholder_score = "0";
          $value_score = "0";
        }
        if ($titleHead != $row["zone"]){
          if (isset($titleHead)) {
            $output .= '
            <tr>
            <td colspan="2">&nbsp;</td>
            <td>บัตรเสีย</td>
            <td><input type="text" class="form-control" id="cardVote[0]" name="cardVote[0]" placeholder="' . $cardVote[0] . '" value="' . $cardVote[0] . '" autocomplete="off"></td>
            </tr>
            <tr>
            <td colspan="2">&nbsp;</td>
            <td>ไม่เลือกผู้ใด</td>
            <td><input type="text" class="form-control" id="cardVote[1]" name="cardVote[1]" placeholder="' . $cardVote[1] . '" value="' . $cardVote[1] . '" autocomplete="off"></td>
            </tr>
            ';
          }

          $titleHead = $row["zone"];
          $output .= '
          <tr>
            <td colspan="4"><strong>';

            if ($row["zone"] == 0) {
              $output .= $titleLeader;
            } else {
              $output .= $titleAssistant . ' เขต ' . $zone . ' หน่วยที่ ' . $no_station;
            }


          $output .= '</strong></td>
          </tr>
          <tr>
           <th style="text-align: center">หมายเลข</th>
           <th>ชื่อ - สกุล</th>
           <th>พรรค</th>
           <th>คะแนนที่ได้</th>
          </tr>
          ';
        }
       $output .= '
        <tr>
         <td style="text-align: center">'.$row["no_candidate"].'</td>
         <td>'.$row["name"].'</td>
         <td>'.$row["political_party"].'</td>
         <td>
         <input type="text" class="form-control" id="candidate_score['.$row["id"].']" name="candidate_score['.$row["id"].']" placeholder="'.$placeholder_score.'" value="'.$value_score.'" autocomplete="off">
         </td>
        </tr>
       ';
      }
      $output .= '
      <tr>
      <td colspan="2">&nbsp;</td>
      <td>บัตรเสีย</td>
      <td><input type="text" class="form-control" id="cardVote[2]" name="cardVote[2]" placeholder="' . $cardVote[2] . '" value="' . $cardVote[2] . '" autocomplete="off"></td>
      </tr>
      <tr>
      <td colspan="2">&nbsp;</td>
      <td>ไม่เลือกผู้ใด</td>
      <td><input type="text" class="form-control" id="cardVote[3]" name="cardVote[3]" placeholder="' . $cardVote[3] . '" value="' . $cardVote[3] . '" autocomplete="off"></td>
      </tr>
      ';

      $output .= '
      <tr><td colspan="4" style="text-align: center"><input type="checkbox" name="clear_score" value="1"> ลบคะแนนในหน่วยนี้ทั้งหมด<br></td></tr>
      <tr><td colspan="4" style="text-align: center"><button type="submit" class="btn btn-default">บันทึก</button></td></tr>
      </table></form>';
    }

    echo $output;
   }
   else
   {
    echo 'Data Not Found';
   }
?>
   <br />
   <div id="result"></div>
  </div>
 </body>
</html>
