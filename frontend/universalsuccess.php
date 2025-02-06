<?php session_start()?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Successful</title>
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/universalsuccess.css">
</head>
<body>
<?php include 'navbar.php';?>
<div id="area">
<?php 
if(isset($_GET['success'])){
    if($_GET['success'] == 'signin'){?>
        <h2>Sign In Successful</h2>
        <h3>You will be redirected shortly.</h3>
        <script>
        window.setTimeout(function(){
            window.location.href = "index.php";
        }, 3000);
        </script>
        </div>
<?php }elseif($_GET['success'] == 'signup'){?>
        <h2>Sign Up Successful</h2>
        <h3>You will be redirected shortly.</h3>
        <script>
        window.setTimeout(function(){
            window.location.href = "index.php";
        }, 3000);
        </script>
        </div>
<?php }elseif($_GET['success'] == 'signout'){?>
        <h2>Sign Out Successful</h2>
        <h3>You will be redirected shortly.</h3>
        <script>
        window.setTimeout(function(){
            window.location.href = "index.php";
        }, 3000);
        </script>
        </div>
<?php }elseif($_GET['success'] == 'resetpw'){?>
        <h2>Password Reset Successful</h2>
        <h3>You will be redirected shortly.</h3>
        <script>
        window.setTimeout(function(){
            window.location.href = "signin.php";
        }, 3000);
        </script>
        </div>
<?php }elseif($_GET['success'] == 'changeacinfo'){?>
        <h2>Account Information Updated Successful</h2>
        <h3>You will be redirected shortly.</h3>
        <script>
        window.setTimeout(function(){
            window.location.href = "index.php";
        }, 3000);
        </script>
        </div>
<?php }} else{?>
        <script>
            window.setTimeout(function(){
                window.location.href = "index.php";
            }, 1);
        </script>
        </div>
<?php }?>
<?php include 'footer.php';?>
</body>
</html>
