<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php';


print_r(Registry::getConnection());
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Room Reserver</title>

    <!-- Bootstrap Core CSS -->
    <link href="CSS/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="CSS/landing-page.css" rel="stylesheet">
	
	<!-- jQuery -->
    <script src="Javascript/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="Javascript/bootstrap.min.js"></script>
    
    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" rel="stylesheet">
	
</head>

<body>

    <!-- Header -->

    <div class="intro-header">
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="intro-message">
                        <h1><span class="title" style="font-size: 150%">The Force Awakens</span></h1>
                        <h3><span class="subtitle" style="font-size: 130%">Concordia University Room Reserver</span></h3>
                        <hr class="intro-divider">
                        <ul class="list-inline intro-social-buttons">
                            <li>
                                <a class="btn btn-default btn-lg" data-target="myModal" id="myBtn"><span class="network-name">Login</span></a>   
                            </li>
							<li>
                                <a href="https://my.concordia.ca/psp/upprpr9/EMPLOYEE/EMPL/h/?tab=CU_MY_FRONT_PAGE2" class="btn btn-default btn-lg"><i class="fa fa-github fa-fw"></i> <span class="network-name">MyConcordia</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container -->
</div>

  <!-- Modal -->
  <form action="PHP/Pages/Validation.php" method="post">
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
<!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 style="color:red;">Login</h4>
        </div>
        <div class="modal-body">
          <form id="form">
            <div class="form-group">
              <label for="email">Email</label>
              <input type="text" class="form-control" name="email" placeholder="Enter your email address">
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="password" class="form-control" name="password" placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-default btn-success btn-block">Login</button>
          </form>
        </div>
    </div>
	</div>
	</div>
	</form>
	
	<script>
		$(document).ready(function(){
			$("#myBtn").click(function(){
				$("#myModal").modal();
			});
		});
	</script>

</body>

</html>
