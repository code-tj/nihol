<?php if(!defined('BDIR')){echo '[+_+]'; exit;} ?>
<!DOCTYPE html>
<html lang="en,ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $UI->show('meta'); ?>
    <link rel="icon" href="<?php echo $conf['tpl']; ?>/img/ico/b2.png">

    <title><?php $UI->show('title'); ?></title>

    <!-- Bootstrap core CSS -->
    <link href="./ui/extra/bootstrap/v3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="./ui/extra/bootstrap/v3/css/bootstrap-theme.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="<?php echo $conf['tpl']; ?>/css/style.css" rel="stylesheet">
    <?php $UI->show('link'); ?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body role="document">

    <!-- Fixed navbar -->
    <nav class="navbar navbar-blue navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="./"><div id="xlogo"></div></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <!--<li class="active2"><a href="./" style="font-size:17px;">OFFICE IN TAJIKISTAN</a></li>-->
            <?php $UI->show('menu1'); ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Info <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="./?c=page&act=about">About</a></li>
                <li><a href="./?c=page&act=faq">FAQ</a></li>
              </ul>
            </li>
            
          </ul>
          <?php $UI->show('usermenu'); ?>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <?php $UI->show('bn'); ?>

    <!-- ui main here, contains div.container/container-fluid -->
    <div class="container-fluid theme-showcase" role="main">
      <?php CORE::show('error'); ?>
      <?php CORE::show('info'); ?>
      <?php CORE::show('debug'); ?>

      <?php $UI->show('main'); ?>

    </div> <!-- /container -->

    <footer class="footer"> <!-- footer -->
      <div class="container-fluid">
        <p class="text-muted text-center">
          <?php if(XDEBUG) echo 'Exec: '.(microtime(true)-$start).'; '; ?>
          <?php echo date('Y').' © '.strtoupper(APPNAME); ?>
        </p>
      </div>
    </footer> <!-- /footer -->

    <!-- JavaScript -->
    <script type="text/javascript" src="<?php echo UIPATH; ?>/extra/jquery/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="<?php echo UIPATH; ?>/extra/bootstrap/v3/js/bootstrap.min.js"></script>
    <?php $UI->show('js'); ?>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--<script src="<?php echo UIPATH; ?>/extra/bootstrap/v3/js/ie10-viewport-bug-workaround.js"></script>-->
  </body>
</html>