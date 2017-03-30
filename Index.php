<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php';

/*

TO TEST ASPECT!

$Test = new Stark\Test();
$Test->test();
*/


?>

<!DOCTYPE html>
<html lang="en">
<img style="position:absolute; max-width: 300px" id="watchIntro" src="img/watch-intro.png">
<head>

    <meta charset="utf-8">
    <meta http-eqsoenuiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Room Reserver</title>

    <!-- Bootstrap Core CSS -->

    <!-- TODO: implement CDN bootstrap with local bootstrap as fall back -->
    <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="CSS/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="CSS/landing-page.css" rel="stylesheet">
</head>

<body>

<!-- Header -->

<div class="intro-header">
    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="intro-message">
                    <h1><span class="title" style="font-size: 150%">The Architecture Strikes Back</span></h1>
                    <h3><span class="subtitle" style="font-size: 130%">Concordia University Room Reserver</span></h3>
                    <hr class="intro-divider">
                    <ul class="list-inline intro-social-buttons">
                        <li>
                            <a class="btn btn-default btn-lg" data-target="myModal" id="myBtn"><span
                                        class="network-name">Login</span></a>
                        </li>
                        <li>
                            <a href="https://my.concordia.ca/psp/upprpr9/EMPLOYEE/EMPL/h/?tab=CU_MY_FRONT_PAGE2"
                               class="btn btn-default btn-lg"><i class="fa fa-github fa-fw"></i>
                                <span class="network-name">MyConcordia</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container -->
</div>

<!-- Modal -->

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 style="color:red;">Login</h4>
            </div>
            <div class="modal-body">
                <form id="LoginForm" role="form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email"
                               placeholder="Enter your email address">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Enter your password">
                    </div>
                    <button id="login" type="button" class="btn btn-default btn-success btn-block">Login</button>

                </form>
                <div id="results"></div>
            </div>
        </div>
    </div>
</div>

<!-- vendor scripts & fall backs -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script> window.$ || window.jQuery || document.write('<script src="node_modules/jquery/dist/jquery.min.js">\x3C/script>')</script>

<!-- jQuery Cookie-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script> $.cookie || document.write('<script src="node_modules/jquery.cookie/jquery.cookie.js">\x3C/script>')</script>

<!-- bootstrap js -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script> window.$.fn || document.write('<script src="node_modules/bootstrap/dist/js/bootstrap.min.js">\x3C/script>')</script>

<!-- Google Web Fonts -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" rel="stylesheet">

<!-- local scripts -->

<!-- index.js -->
<script src="js/index.js"></script>

</body>
</html>
