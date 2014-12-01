<!DOCTYPE html>

<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'>
    <link href="assets/fonts/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="assets/css/bootstrap-select.min.css" type="text/css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="assets/css/jquery.slider.min.css" type="text/css">
    <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">

    <title>Latch webcast</title>

</head>

<body class="page-sub-page page-sign-in page-account" id="page-top">
    <!-- Wrapper -->
    <div class="wrapper">
        <!-- Navigation -->
        <div class="navigation">
            <div class="secondary-navigation">
                <div class="container">
                    <div class="user-area">
                        <div class="actions">
                            <a href="index.php?action=logout" class="promoted"><strong>Log out</strong></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <header class="navbar" id="top" role="banner">
                    <div class="navbar-header">
                        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="navbar-brand nav" id="brand">
                            <img src="assets/img/logo_11Paths_find_tweets.png" alt="11pathlogo">
                        </div>
                    </div>
                </header><!-- /.navbar -->
            </div><!-- /.container -->
        </div><!-- /.navigation -->
        <!-- end Navigation -->
        <!-- Page Content -->
        <div id="page-content">
            <!-- Breadcrumb -->
            <div class="container">
                <ol class="breadcrumb">
                    <li><a href="#">Home</a></li>
                    <li class="active">Latch</li>
                </ol>
            </div>
            <!-- end Breadcrumb -->

            <div class="container">
                <header><h1>Latch account</h1></header>

<?php
    writeErrorIfExists();
    writeSuccessIfExists();
?>

    <?php if (!isset($userPaired) || !$userPaired) { ?>

                <form action="index.php?action=doPair" class="form-horizontal" method="POST">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Pairing token:</label>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" name="pairingToken">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-3">
                            <button type="submit">Pair account</button>
                        </div>  
                    </div>

                    
                </form>
    <?php } else { ?>

        <form action="index.php?action=doUnpair" class="form-horizontal" method="POST">
            <div class="form-group">
                <label class="col-sm-2 control-label">Your account is paired. </label>
                <div class="col-sm-2">
                    <button type="submit">Unpair account</button>
                </div>
            </div>
        </form>

    <?php } ?>
                
                <br>
                <a href="index.php?action=profile">Back to profile</a>

                <div class="row">
                </div><!-- /.row -->
            </div><!-- /.container -->
        </div>
        <!-- end Page Content -->
        <!-- Page Footer -->
        <footer id="page-footer">
            <div class="inner">
                <section id="footer-copyright">
                    <div class="container" style="text-align: center;">
                        <span>Copyright © 2014. All Rights Reserved.</span>
                    </div>
                </section>
            </div><!-- /.inner -->
        </footer>
        <!-- end Page Footer -->
    </div>

    <script type="text/javascript" src="assets/js/jquery-2.1.0.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/smoothscroll.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="assets/js/icheck.min.js"></script>
    <script type="text/javascript" src="assets/js/retina-1.1.0.min.js"></script>
    <script type="text/javascript" src="assets/js/custom.js"></script>
<!--[if gt IE 8]>
<script type="text/javascript" src="assets/js/ie.js"></script>
<![endif]-->


</body>
</html>
