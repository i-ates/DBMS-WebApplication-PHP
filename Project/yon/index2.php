<?php
include_once("fonk/yonfonk.php");
$clas = new users;
$clas->cookcon($vt, true);
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="../dosya/jqu.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../dosya/boost.css">
    <title>Manager Login</title>
    <style>
        #log {
            margin-top:20%;
            min-height:250px;
            background-color:#FEFEFE;
            border-radius:10px;
            border:1px solid #B7B7B7;
        }
    </style>
</head>
<body style = "background-color:#EEE;">
<div class = "container text-center">
    <div class = "row mx-auto">
        <div class="col-md-4"></div>
        <div class="col-md-4 mx-auto text-center" id = "log">
            <?php
            @$buton = $_POST["buton"];
            if(!$buton):
                ?>
                <form action = "<?php $_SERVER['PHP_SELF'] ?>" method = "post">
                    <div class="col-md-12 border-bottom p-2"><h3>WAITER LOGIN</h3></div>
                    <div class="col-md-12"><input type="text" name="User_Name" class="form-control mt-3" required="required" placeholder="Manager Name" autofocus="autofocus"/> </div>
                    <div class="col-md-12"><input type="password" name="Password" class="form-control mt-2" required="required" placeholder="Password"/> </div>
                    <div class="col-md-12"><input type="submit" name="buton" class="btn btn-success mt-2" value="Login"/> </div>
                </form>
            <?php

            else:
                @$Password = htmlspecialchars(strip_tags($_POST["Password"]));
                @$User_Name = htmlspecialchars(strip_tags($_POST["User_Name"]));
                if ($Password == "" || $User_Name == "") :
                    echo "Username and password cannot be empty!";
                    header("refresh:2, url=index.php");
                else:
                    $clas->waitercontrol($vt, $User_Name, $Password);
                endif;
            endif;
            ?>
        </div>
        <div class="col-md-4"></div>
    </div>
</div>
</body>
</html>