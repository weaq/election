<?php
include 'config.php';
include 'dbconnect.php';

# Max Zone
$sql = "SELECT DISTINCT zone FROM `polling_station" . $table . "`";
$result = mysqli_query($conn, $sql);
$maxZone = mysqli_num_rows($result);

$zone = $_GET['zone'];
echo "aaa";
if(empty($zone) OR $zone > $maxZone){
  $zone = 1;
}

$nextZone = $zone +1;
if ($nextZone > $maxZone) {
  $nextZone = 1;
}

#Station all row by zone
$sql = "SELECT * FROM `polling_station" . $table . "` WHERE `zone` = $zone";
$result = mysqli_query($conn, $sql);
$allStationZone = mysqli_num_rows($result);
#Station send score by zone
$sql = "SELECT * FROM `polling_station" . $table . "` WHERE `zone` = $zone AND `score` != '' ";
$result = mysqli_query($conn, $sql);
$sendScoreZone = mysqli_num_rows($result);

## all score candidate to array
$sql = "SELECT * FROM `polling_station" . $table . "` WHERE `score` != '' ";

$result = mysqli_query($conn, $sql);
$sendScore = mysqli_num_rows($result);

if(mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_array($result)) {
    $tmpScore = json_decode($row["score"],TRUE);

    foreach($tmpScore as $key => $val) {
      $scoreSum[$key] = $scoreSum[$key] + $val;
    }
  }
}
/*
echo "== sum ==" . "<br>";
print_r($scoreSum);

echo "<br>==========<br>";
echo $scoreSum["20"];
*/
$showTitle = $show . $titleLeader . " " . $subtitle;
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <title><?php echo $showTitle; ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--
  <META HTTP-EQUIV="Refresh" CONTENT="15;URL=?zone=<?php echo $nextZone; ?>">
  -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Mitr:wght@600&display=swap" rel="stylesheet">
  <style>
  html * {
  font-family: 'Mitr', sans-serif;
  }

  </style>
</head>
<body>

<div class="container-fluid">
  <h2 class="text-center"><?php echo $showTitle . " (" . $sendScore . " / " . $allStationZone . " หน่วย)"; ?></h2>
  <div class="row">
    <?php
    // Show Leader
    $sql = "SELECT * FROM `candidate" . $table . "` WHERE `zone` = 2";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
      while($row = mysqli_fetch_array($result)) {
        $scoreSumLeader[$row[id]] = $scoreSum[$row[id]];
        $detailLeader[$row[id]] = array(
          'name' => trim($row["name"]),
          'zone' => $row["zone"],
          'no_candidate' => $row["no_candidate"],
          'political_party' => $row["political_party"],
        );
      }
    }
    arsort($scoreSumLeader);
    $index = 1;
    foreach($scoreSumLeader as $key => $val) {
      #echo "Key=" . $key . ", Value=" . $val;
      $splitName = explode(" ", $detailLeader[$key][name]);
      if($index <= 6) {
        $cssColor = ' text-dark ';
      } else {
        $cssColor = ' text-muted ';
      }
      if ($index == 1 OR $index == 7 ) {
        if ($index > 1) {
          echo '</div>';
        }
      echo '
      <div class="col-md-6">
      <div class="row justify-content-md-center">
        <div class="col-lg-2 h2 text-center d-none d-lg-block ">ลำดับ</div>
        <div class="col-lg-5 col-md-7 h2 text-center d-none d-md-block">ชื่อ</div>
        <div class="col-lg-4 col-md-4 h2 text-center d-none d-md-block">คะแนน</div>
      </div>
      ';
    }
    if ($index <= 6) {
      echo '<div class="row justify-content-md-center py-1">';
      echo '<div class="col-lg-2 d-none d-lg-block  border border-right-0 border-left-0 border-bottom-0">';
      echo '<p class="text-center display-4 ' . $cssColor . '">' . $index . '</p>';
      echo '</div>';
      echo '<div class="col-lg-5 col-md-7 border border-right-0 border-left-0 border-bottom-0">';
      echo '<p class="h1" style="margin:0;">เบอร์ ' . $detailLeader[$key][no_candidate] . " " . '</p>';
      echo '<p class="h3" style="margin:0;">' . $splitName[0] . '</p>';
      echo '<p class="h3 d-none d-md-block" style="margin:0;">' . $splitName[1] . '</p>';
      echo '</div>';
      echo '<div class="col-lg-4 col-md-4 border border-right-0 border-left-0 border-bottom-0">';
      echo '<p class="text-center h1" style="margin:0;">' . number_format($val) . '</p>';
      echo '</div>';
      echo '</div>';
    } else {
      echo '<div class="row justify-content-md-center py-1">';
      echo '<div class="col-lg-2 d-none d-lg-block  border border-right-0 border-left-0 border-bottom-0">';
      echo '<p class="text-center h1 ' . $cssColor . '" style="margin:0;">' . $index . '</p>';
      echo '</div>';
      echo '<div class="col-lg-5 col-md-7 border border-right-0 border-left-0 border-bottom-0">';
      echo '<p class="h3" style="margin:0;">เบอร์ ' . $detailLeader[$key][no_candidate] . " " . $splitName[0] . '</p>';
      echo '</div>';
      echo '<div class="col-lg-4 col-md-4 border border-right-0 border-left-0 border-bottom-0">';
      echo '<p class="text-center h2" style="margin:0;">' . number_format($val) . '</p>';
      echo '</div>';
      echo '</div>';
    }
      $index++;
    }
#print_r($detailLeader);
     ?>
</div>


</div>

</body>
</html>
<?php

/*
echo '<div class="col-lg-4" style="background-color:lavender;">';
echo '<p><h3 class="text-center"> เบอร์ ' . $row["no_candidate"] . '</p>';
echo '<p><h4 class="text-center">' . $row["name"] . '</p>';
echo '</div>';
*/


// Free result set
mysqli_free_result($result);

mysqli_close($con);
?>
