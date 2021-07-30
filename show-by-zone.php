<?php
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
  <META HTTP-EQUIV="Refresh" CONTENT="<?php echo $secRedirect; ?>;URL=?zone=<?php echo $nextZone; ?>">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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
  <div class="row">
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
      if ($index == 1) {
        echo '<div class="col-md-4" style="background-color:lavender; border:2px solid black;">';
      } else {
        echo '<div class="col-md-4" style="border:2px solid gray;">';
      }
      echo '<p><h3 class="text-center" style="margin:0;"> อันดับ ' . $index . '</h3>';
      echo '<h1 class="text-center" style="margin:0;"> เบอร์ ' . $detailLeader[$key][no_candidate] . " " . $splitName[0] . '</h1></p>';
      echo '<p><h1 class="text-center" style="margin:0;">' . number_format($val) . '</h1></p>';
      echo '</div>';
      $index++;
    }
#print_r($detailLeader);
     ?>
  </div>
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
      echo '<p><h3 class="text-center" style="margin:0;"> อันดับ ' . $index . '</h3>';
      echo '<h2 class="text-center" style="margin:0;"> เบอร์ ' . $detailAssistant[$key][no_candidate] . " " . $splitName[0] . '</h2></p>';
      echo '<p><h1 class="text-center" style="margin:0;">' . number_format($val) . '</h1></p>';
      echo '</div>';
    } else {
      echo '<div class="col-md-2" style="border:2px solid black;">';
      echo '<p><span class="text-center h3"> เบอร์ ' . $detailAssistant[$key][no_candidate] . '</span>';
      echo '<span class="text-center h4"> ' . $splitName[0] . '</span></p>';
      echo '<p class="text-center h3" style="margin:0;">' . number_format($val) . '</p>';
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
