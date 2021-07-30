<?php
include 'notlogin.php';
include 'config.php';
include 'dbconnect.php';

# Max Zone
$sql = "SELECT DISTINCT zone FROM `polling_station" . $table . "`";
$result = mysqli_query($conn, $sql);
$maxZone = mysqli_num_rows($result);

if (empty($_GET[p])) {
  $p = 1;
} else {
  $p = $_GET[p];
}
$zone = $_GET[zone];
if(empty($zone) OR $zone > $maxZone){
  $zone = 1;
}

// All candidate in zone
$sql = "SELECT * FROM `candidate" . $table . "` WHERE `zone` = $zone";
$result = mysqli_query($conn, $sql);
$allCandidate = mysqli_num_rows($result);
$showLeft = 6;
$showRight = 8;
$pMax = ceil(($allCandidate - 6) / $showRight);

$nextZone = $zone +1;
if ($nextZone > $maxZone AND $p == $pMax) {
  $nextZone = 0;
}

  if ($nextZone == 0) {
    $redirect = "index.php";
  } else if ($pMax == 1 OR $p == $pMax) {
    $redirect = "?zone=" . $nextZone . "&p=1";
  } else {
    $pNext = $p+1;
    $redirect = "?zone=" . $zone . "&p=" . $pNext;
  }


$lstStart = ($p * $showRight) -2;
$lstEnd = ($p * $showRight) + $showLeft -1;

#Station all row by zone
$sql = "SELECT * FROM `polling_station" . $table . "` WHERE `zone` = $zone";
$result = mysqli_query($conn, $sql);
$allStationZone = mysqli_num_rows($result);

// sum assistant voter
$sumLeaderVoter = 0;
if(mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_array($result)) {
    $sumLeaderVoter += $row["voters_leader"];
  }
}

#Station send score by zone
$sql = "SELECT * FROM `polling_station" . $table . "` WHERE `zone` = $zone AND `score` != '' ";
$result = mysqli_query($conn, $sql);
$sendScoreZone = mysqli_num_rows($result);

## all score candidate to array
$sql = "SELECT * FROM `polling_station" . $table . "` WHERE `zone` = $zone AND `score` != '' ";

$result = mysqli_query($conn, $sql);
$sendScore = mysqli_num_rows($result);

if(mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_array($result)) {
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
/*
echo "== sum ==" . "<br>";
print_r($scoreSum);

echo "<br>==========<br>";
echo $scoreSum["20"];
*/
$showTitle = $show . $titleAssistant . " เขต " . $zone;
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <title><?php echo $showTitle; ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <META HTTP-EQUIV="Refresh" CONTENT="<?php echo $secRedirect; ?>;URL=<?php echo $redirect; ?>">

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
  $sql = "SELECT * FROM `candidate" . $table . "` WHERE `zone` = $zone";
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
  $index = 0;
  $voter = 0;
  foreach($scoreSumLeader as $key => $val) {
    #echo "Key=" . $key . ", Value=" . $val;
    $voter += $val;
  }

  # %
  $votePercent = round(100 / $sumLeaderVoter * ($voter + $txtCardVote[0] + $txtCardVote[1]));
  /*
  $showVoter = "ผู้มีสิทธิ์ " . number_format($sumLeaderVoter) . " คน " ;

                $showVoter = "ผู้มีสิทธิ์ " . number_format($sumLeaderVoter) . " คน (" . $votePercent . "%)" ." บัตรดี : " . number_format($voter)
                              . "&nbsp;"
                              . $txtCardVote[0] . " : " . number_format($arrCardVote[2])
                              . "&nbsp;"
                              . $txtCardVote[1] . " : " . number_format($arrCardVote[3]);
*/

  if(empty($voter)){
    $sendScoreZone = 0;
  }
   ?>

<div class="container-fluid">
  <p class="text-center mt-4 mb-0"><span class="h1"><?php echo $showTitle; ?></span> &nbsp; <span class="h3">(<?php echo $subtitle . ')</span> &nbsp; <span class="h1">' . $sendScoreZone . " / " . $allStationZone . " หน่วย"; ?></span></p>
  <p class="text-center h3 p-0"><?php echo $showVoter; ?></p>
  <div class="row">
    <?php
    foreach($scoreSumLeader as $key => $val) {
      #echo "Key=" . $key . ", Value=" . $val;
      $splitName = explode(" ", $detailLeader[$key][name]);
      if($index < $showLeft) {
        $cssColor = ' text-dark ';
      } else {
        $cssColor = ' text-muted ';
      }

      if ($index == 0 OR $index == $showLeft ) {
        if ($index > 0) {
          echo '</div>';
        }

      echo '
      <div class="col-lg-6 col-md-12">
      <div class="row justify-content-md-center">
        <div class="col-lg-2 col-md-2 h2 text-center d-none d-md-block p-0">ลำดับ</div>
        <div class="col-lg-6 col-md-6 h2 text-center d-none d-md-block p-0">ชื่อ</div>
        <div class="col-lg-4 col-md-4 h2 text-center d-none d-md-block p-0">คะแนน</div>
      </div>
      ';
    }

    if ($p > 1 AND $index > 5) {
      if ($index < $lstStart) {
        $index++;
        continue;
      }
    }

      echo '<div class="row justify-content-md-center py-0">';

      echo '<div class="col-lg-2 col-md-2 d-none d-md-block border border-right-0 border-left-0 border-bottom-0 p-1">';
      $showIndex = $index +1;
      echo '<p class="text-center display-4 ' . $cssColor . '">' . $showIndex . '</p>';
      echo '</div>';
      echo '<div class="col-lg-6 col-md-6 col-sm-12 border border-right-0 border-left-0 border-bottom-0 p-0">';
      echo '<div class="row">';
        echo '<div class="col-lg-4 col-md-3 col-3 p-0">';
          $imgPath = 'img/' . $table . '/' . $zone . '/' . $detailLeader[$key][zone] . '-'. $detailLeader[$key][no_candidate] . ".jpg";
          if (file_exists($imgPath)) {
            $imgPath = $imgPath;
          } else {
            $imgPath = "img/no-image.jpg";
          }
          echo '<img src="' . $imgPath . '" alt="' . $imgPath  . '" class="img-thumbnail p-0" width="86" height="86">';
        echo '</div>';
        echo '<div class="col-lg-8 col-md-9 col-9">';
          echo '<div class="h1 col-lg-12 col-md-12 m-0 p-0">เบอร์ ' . $detailLeader[$key][no_candidate] . '</div>';
          echo '<div class="h2 col-lg-12 col-md-12 m-0 p-0">' . $splitName[0] . '</div>';
          if ($index < $showLeft) {
            echo '<div class="h4 col-lg-12 col-md-12 m-0 p-0">' . $splitName[1] . '</div>';
          }
        echo '</div>';
      echo '</div>';
      echo '</div>';
      echo '<div class="col-lg-4 col-md-4 border border-right-0 border-left-0 border-bottom-0 p-0">';
      echo '<p class="text-center display-4">' . number_format($val) . '</p>';
      echo '</div>';
      echo '</div>';

      if ($index == $lstEnd OR $index == ($allCandidate-1)){
        break;
      } else {
        $index++;
      }

    }

     ?>
</div>


</div>

</body>
</html>
<?php

// Free result set
mysqli_free_result($result);

mysqli_close($con);
?>
