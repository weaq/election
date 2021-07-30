<?php
include 'chklogin.php';
include 'config.php';
include 'dbconnect.php';

# Max Zone
$sql = "SELECT DISTINCT zone FROM `polling_station" . $table . "`";
$result = mysqli_query($conn, $sql);
$maxZone = mysqli_num_rows($result);

$zone = $_GET[zone];
if(empty($zone) OR $zone > $maxZone){
  $zone = 1;
}

$nextZone = $zone +1;
if ($nextZone > $maxZone) {
  $nextZone = 1;
}

#Station all row
$sql = "SELECT * FROM `polling_station" . $table . "`";
$result = mysqli_query($conn, $sql);
$allStation = mysqli_num_rows($result);
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
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <title><?php echo $title . "-" . $subtitle; ?></title>
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
  <h2 class="text-center"><?php echo $title . " - " . $subtitle; ?></h2>
  <h2><?php echo $titleLeader . " (" . $sendScore . " / " . $allStation . " หน่วย)"; ?></h2>
  <div class="row justify-content-md-center">
    <div class="col-md-1 h1 text-center">ลำดับ</div>
    <div class="col-md-2 h1 text-center">&nbsp;</div>
    <div class="col-md-2 h1 text-center">ชื่อ</div>
    <div class="col-md-4 h1 text-center">คะแนน</div>
  </div>
    <?php
    // Show Leader
    $sql = "SELECT * FROM `candidate" . $table . "` WHERE `zone` = 0 LIMIT 3";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
      while($row = mysqli_fetch_array($result)) {
        $scoreSumLeader[$row[id]] = $scoreSum[$row[id]];
        $detailLeader[$row[id]] = array(
          'name' => trim($row["name"]),
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
      echo '<div class="row justify-content-md-center">';
      echo '<div class="col-md-1 border border-right-0 border-left-0">';
      if($index == 1) { $cssColor = ' text-dark ';} else { $cssColor = ' text-muted '; }
      echo '<p class="text-center display-1 ' . $cssColor . '">' . $index . '</p>';
      echo '</div>';
      echo '<div class="col-md-2 text-center border border-right-0 border-left-0">';
      echo '<img src="img/no-image.jpg" alt="" class="rounded" width="128" height="128">';
      echo '</div>';
      echo '<div class="col-md-2 border border-right-0 border-left-0">';
      echo '<p class="display-4" style="margin:0;">เบอร์ ' . $detailLeader[$key][no_candidate] . " " . '</p>';
      echo '<p class="h3" style="margin:0;">' . $splitName[0] . '</p>';
      echo '<p class="h5" style="margin:0;">' . $splitName[1] . '</p>';

      echo '</div>';
      echo '<div class="col-md-4 border border-right-0 border-left-0">';
      echo '<p class="text-center display-1">' . number_format($val) . '</p>';
      echo '</div>';
      echo '</div>';
      $index++;
    }
#print_r($detailLeader);
     ?>

<h2><?php echo $titleAssistant . " เขต " . $zone  . " (" . $sendScoreZone . " / " . $allStationZone . " หน่วย)"; ?></h2>
<div class="row">
  <?php
  // Show assistant
  $sql = "SELECT * FROM `candidate" . $table . "` WHERE `zone` = $zone" ;
  $result = mysqli_query($conn, $sql);
  if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result)) {
      $scoreSumAssistant[$row[id]] = $scoreSum[$row[id]];
      $detailAssistant[$row[id]] = array(
        'name' => trim($row["name"]),
        'no_candidate' => $row["no_candidate"],
        'political_party' => $row["political_party"],
      );
    }
  }
  arsort($scoreSumAssistant);
  $index = 1;
  foreach($scoreSumAssistant as $key => $val) {
    #echo "Key=" . $key . ", Value=" . $val;
    $splitName = explode(" ", $detailAssistant[$key][name]);
    if ($index <= 8) {
      if ($index <= $cerrectAssistant) {
        echo '<div class="col-md-3" style="background-color:lavender; border:2px solid black;">';
      } else {
        echo '<div class="col-md-3" style="border:2px solid black;">';
      }
      echo '<p><h4 class="text-center" style="margin:0;"> อันดับ ' . $index . '</h4>';
      echo '<h3 class="text-center" style="margin:0;"> เบอร์ ' . $detailAssistant[$key][no_candidate] . " " . $splitName[0] . '</h3></p>';
      echo '<p><h2 class="text-center" style="margin:0;">' . number_format($val) . '</h2></p>';
      echo '</div>';
    } else {
      echo '<div class="col-md-2" style="border:2px solid black;">';
      echo '<p><h4 class="text-center" style="margin:0;">';
      echo 'เบอร์ ' . $detailAssistant[$key][no_candidate] . " " . $splitName[0] . '</h4></p>';
      echo '<p><h3 class="text-center" style="margin:0;">' . number_format($val) . '</h3></p>';
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
echo '<div class="col-sm-4" style="background-color:lavender;">';
echo '<p><h3 class="text-center"> เบอร์ ' . $row["no_candidate"] . '</p>';
echo '<p><h4 class="text-center">' . $row["name"] . '</p>';
echo '</div>';
*/


// Free result set
mysqli_free_result($result);

mysqli_close($con);
?>
