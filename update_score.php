<?php
include 'chklogin.php';
include 'config.php';
include 'dbconnect.php';
 ?>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
#print_r($_POST['candidate_score']);
#$polling_station_id = mysqli_real_escape_string($conn, $_POST['polling_station_id']);
$polling_station_id = $_POST['polling_station_id'];
if (empty($_POST['clear_score'])) {
#$candidate_score = mysqli_real_escape_string($conn, $_POST['candidate_score']);
$candidate_score = $_POST['candidate_score'];
//print_r($candidate_score);
$score = json_encode($candidate_score);
echo "<br>";
//print_r($score);

####
$cardVote = $_POST['cardVote'];
//print_r($candidate_score);
$card_vote = json_encode($cardVote);


} else {
  $score = "";
  $card_vote = "";
}

$sql = "UPDATE `polling_station" . $table . "` SET `score` = '$score', `card_vote` = '$card_vote' WHERE `id` = '$polling_station_id'";
#echo $sql;

if (mysqli_query($conn, $sql)) {
    echo "บันทึกข้อมูล เรียบร้อยแล้ว";
} else {
    echo "Error updating record: " . mysqli_error($conn);
}


mysqli_close($conn);

$url = "survey-results.php?id_station=" . $polling_station_id;

header( "refresh: 2; url=$url" );
exit(0);

?>
</body>
</html>
