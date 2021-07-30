<?php
include 'notlogin.php';
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

// sum leader voter
$sumLeaderVoter = 0;
if(mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_array($result)) {
    $sumLeaderVoter += $row["voters_leader"];
  }
}

## all score candidate to array
$sql = "SELECT * FROM `polling_station" . $table . "` WHERE `score` != '' ";

$result = mysqli_query($conn, $sql);
$sendScore = mysqli_num_rows($result);

if(mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_array($result)) {
    # Score
    $tmpScore = json_decode($row["score"],TRUE);

    foreach($tmpScore as $key => $val) {
      $scoreSum[$key] = $scoreSum[$key] + $val;
    }
    # card vote
    $tmpCardVote = json_decode($row["card_vote"],TRUE);

    foreach($tmpCardVote as $key => $val) {
      $arrCardVote[$key] = $arrCardVote[$key] + $val;
    }

  }
}
# incerrect and no vote
#print_r($arrCardVote);


/*
echo "== sum ==" . "<br>";
print_r($scoreSum);

echo "<br>==========<br>";
echo $scoreSum["20"];
*/
$showTitle = $show . $titleLeader . $place;
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <title><?php echo $showTitle; ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<?php
if (empty($_GET['f'])) {
 ?>
  <META HTTP-EQUIV="Refresh" CONTENT="<?php echo $secRedirect+10; ?>;URL=show-assistant-v1.php">
<?php
} else {
?>
  <META HTTP-EQUIV="Refresh" CONTENT="<?php echo $secRedirect; ?>;URL=index.php?f=1">
<?php
}
?>
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
  <?php
  // Show Leader
  $sql = "SELECT * FROM `candidate" . $table . "` WHERE `zone` = 0";
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
  $voter = 0; // voter sent
  foreach($scoreSumLeader as $key => $val) {
    #echo "Key=" . $key . ", Value=" . $val;
    $voter += $val;
  }

# %

$votePercent = round(100 / $sumLeaderVoter * ($voter + $txtCardVote[0] + $txtCardVote[1]));
/*
$showVoter = "ผู้มีสิทธิ์ " . number_format($sumLeaderVoter) . " คน (" . $votePercent . "%)" ." บัตรดี : " . number_format($voter)
              . "&nbsp;"
              . $txtCardVote[0] . " : " . number_format($arrCardVote[0])
              . "&nbsp;"
              . $txtCardVote[1] . " : " . number_format($arrCardVote[1]);
*/

  ?>
<div class="container-fluid">
  <p class="text-center mt-5"><span class="h1"><?php echo $showTitle; ?></span>
    &nbsp;<br> <span class="h1"><?php echo $sendScore . " / " . $allStation . " หน่วย"; ?></span>
  &nbsp; <span class="h3">(<?php echo $subtitle; ?>)</span></p>
  <p class="text-center h3"><?php echo $showVoter; ?></p>
  <div class="row justify-content-md-center">
    <div class="col-lg-1 h2 text-center d-none d-lg-block ">ลำดับ</div>
    <div class="col-lg-2 h2 text-center d-none d-lg-block ">&nbsp;</div>
    <div class="col-lg-3 col-md-4 h2 text-center d-none d-md-block">ชื่อ</div>
    <div class="col-lg-4 col-md-7 h2 text-center d-none d-md-block">คะแนน</div>
  </div>
    <?php
    foreach($scoreSumLeader as $key => $val) {
      #echo "Key=" . $key . ", Value=" . $val;
      $splitName = explode(" ", $detailLeader[$key][name]);
      echo '<div class="row justify-content-md-center py-1">';
      echo '<div class="col-lg-1 col-md-1 d-none d-md-block border border-right-0 border-left-0 border-bottom-0">';
      if($index == 1) { $cssColor = ' text-dark ';} else { $cssColor = ' text-muted '; }
      echo '<p class="text-center display-3 ' . $cssColor . '">' . $index . '</p>';
      echo '</div>';
      echo '<div class="col-lg-2 col-md-2 col-4 text-center border border-right-0 border-left-0 border-bottom-0 p-0">';
      $imgPath = 'img/' . $table . '/' . $detailLeader[$key][zone] . '-'. $detailLeader[$key][no_candidate] . ".jpg";
      if (file_exists($imgPath)) {
        $imgPath = $imgPath;
      } else {
        $imgPath = "img/no-image.jpg";
      }
      echo '<img src="' . $imgPath . '" alt="' . $imgPath  . '" class="img-thumbnail" width="120" height="120">';
      echo '</div>';
      echo '<div class="col-lg-3 col-md-4 col-8 border border-right-0 border-left-0 border-bottom-0">';
      echo '<p class="h1" style="margin:0;">เบอร์ ' . $detailLeader[$key][no_candidate] . " " . '</p>';
      echo '<p class="h2" style="margin:0;">' . $splitName[0] . '</p>';
      echo '<p class="h3" style="margin:0;">' . $splitName[1] . '</p>';

      echo '</div>';
      echo '<div class="col-lg-4 col-md-5 col-12 border border-right-0 border-left-0 border-bottom-0 p-0">';
      echo '<p class="text-center display-3">' . number_format($val) . '</p>';
      echo '</div>';
      echo '</div>';
      $index++;
    }
#print_r($detailLeader);
     ?>
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
