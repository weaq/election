<?php
    session_start();
    $message="";
    if(count($_POST)>0) {
      include 'dbconnect.php';
      $result = mysqli_query($conn,"SELECT * FROM login_user WHERE user='" . $_POST["user"] . "' and pwd = '". md5($_POST["password"]) ."'");
      $row  = mysqli_fetch_array($result);
      if(is_array($row)) {
        $_SESSION["id"] = $row['id'];
        $_SESSION["user"] = $row['user'];
        $_SESSION["table"] = $row['allTable'];
      } else {
         $message = "Invalid Username or Password!";
      }
    }
    if(isset($_SESSION["id"])) {
    header("Location:index-x.php");
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <title>Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">

              <div class="row">
                  <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10 mx-auto text-center form p-4">
                      <h1 class="display-4 py-2 ">ลงชื่อเข้าใช้</h1>
                      <div class="message"><?php if($message!="") { echo $message; } ?></div>
                      <div class="px-2">
                          <form method="post" action="" class="justify-content-center">
                              <div class="form-group">
                                  <label class="sr-only">User</label>
                                  <input type="text" name="user" class="form-control" placeholder="username">
                              </div>
                              <div class="form-group">
                                  <label class="sr-only">Password</label>
                                  <input type="password" name="password" class="form-control" placeholder="password">
                              </div>
                              <button type="submit" class="btn btn-primary btn-lg">Log in</button>
                          </form>
                      </div>
                  </div>
              </div>



</div>

</body>
</html>
