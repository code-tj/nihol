<!DOCTYPE html>
<html lang="en,ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--@meta-->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="./ui/templates/seb/img/seb.ico">

    <title><!--@title--></title>

    <!-- Bootstrap core CSS -->
    <link href="./ui/ext/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->

    <!-- Custom styles for this template -->
    <link href="./ui/templates/seb/css/style.css" rel="stylesheet">
    <!--@link-->

  </head>

  <body role="document">

    <!-- navbar -->
    <nav class="navbar navbar-blue navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="./"><!--@appname--></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <!--@menu_main-->
          </ul>
          <div class="navbar-right">
            <!--@menu_user-->
          </div>
        </div><!--/.navbar-collapse -->
      </div>
    </nav><!-- /navbar -->

    <div id="xcontent" class="container-fluid">

      <!-- messages -->
      <div class="row">
        <div class="col-md-12">
          <!--@msg-->
        </div>
      </div>

      <!-- main page content -->
      <div class="row">
        <div class="col-md-12">
          <!--@main-->
        </div>
      </div>

    </div> <!-- /container -->

    <!-- footer -->
    <footer class="footer">
      <div class="container-fluid">
        <!--@footer-->
      </div>
    </footer>

    <!-- JavaScript -->
    <script type="text/javascript" src="./ui/ext/jquery/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="./ui/ext/bootstrap/js/bootstrap.min.js"></script>
    <!--@js-->

  </body>
</html>