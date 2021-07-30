<?php
#$table = "_ud";
if ($_SESSION["table"]) {
  $table = $_SESSION["table"];
} else if ($_SESSION["display"]) {
  $table = $_SESSION["display"];
} else {
  exit();
}

switch ($table) {
  case "_muk" :
    $place = "เมืองมุกดาหาร";
    $title = "คะแนนการเลือกตั้ง นายก / ส.ท." . $place;
    $show = "ผลการเลือกตั้ง";
    $subtitle = "อย่างไม่เป็นทางการ";
    $titleLeader = " นายกเทศมนตรี";
    $titleAssistant = " ส.ท.";
    $cerrectAssistant = 6; // จำวนสมาชิกสภาในแต่ละเขตที่มีได้
    $secRedirect = 10; // จำนวนวินาทีที่แสดงในแต่ละหน้า
    $showImg = 0; // show image is 1
    break;
  case "_ud" :
    $place = "นครอุดรธานี";
    $title = "คะแนนการเลือกตั้ง นายก / ส.ท. " . $place;
    $show = "ผลการเลือกตั้ง";
    $subtitle = "อย่างไม่เป็นทางการ";
    $titleLeader = " นายกเทศมนตรี";
    $titleAssistant = " ส.ท.";
    $cerrectAssistant = 6; // จำวนสมาชิกสภาในแต่ละเขตที่มีได้
    $secRedirect = 10; // จำนวนวินาทีที่แสดงในแต่ละหน้า
    $showImg = 1; // show image is 1
    break;
  }

$txtCardVote = array(
  "0" => "บัตรเสีย ", // นายก
  "1" => "ไม่เลือก ", // นายก
  "2" => "บัตรเสีย ", // สท.
  "3" => "ไม่เลือก " // สท. 
);


?>
