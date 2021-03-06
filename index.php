

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Metasearch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Metasearch Engine" content="">
    <meta name="James Coll" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 60px;
      }

      /* Custom container */
      .container {
        margin: 0 auto;
        max-width: 1000px;
      }
      .container > hr {
        margin: 60px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 80px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 100px;
        line-height: 1;
      }
      .jumbotron .lead {
        font-size: 24px;
        line-height: 1.25;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }
      .marketing p + h4 {
        margin-top: 28px;
      }
	.masthead h1,h2,h3 {
	   	text-align: center;
	}

      /* Customize the navbar links to be fill the entire space of the .navbar */
      .navbar .navbar-inner {
        padding: 0;
      }
      .navbar .nav {
        margin: 0;
        display: table;
        width: 100%;
      }
      .navbar .nav li {
        display: table-cell;
        width: 1%;
        float: none;
      }
      .navbar .nav li a {
        font-weight: bold;
        text-align: center;
        border-left: 1px solid rgba(255,255,255,.75);
        border-right: 1px solid rgba(0,0,0,.1);
      }
      .navbar .nav li:first-child a {
        border-left: 0;
        border-radius: 3px 0 0 3px;
      }
      .navbar .nav li:last-child a {
        border-right: 0;
        border-radius: 0 3px 3px 0;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="../assets/ico/favicon.png">
  </head>

  <body>

    <div class="container">

      <div class="masthead">
        <h1 style="font-size:350%">Metasearch</h1><br />
        <div class="navbar">
          <div class="navbar-inner">
            <div class="container">
              <ul class="nav">
                <li class="active"><a href="index.php">Home</a></li>
		<li><a href="metrics.php">Metrics</a></li>
                <li><a href="review.php">Reviews</a></li>
                <li><a href="about.php">About</a></li>
                
              </ul>
            </div>
          </div>
        </div><!-- /.navbar -->
      </div>

      <div style="text-align:center">
        
        <form class="form-search" method="POST" action="query.php" style="padding:50px">  
            
            <input class="input-large search-query" name="query" type="text" size="60" maxlength="60" value="" /><br />
            <br /> 
		<input type="checkbox" name="rewrite" value="1"> Refine  <br /><br />
		<input class="btn" name="bt_search" type="submit" value="Search" > <br /><br />
		
            <!-- type of output -->
		<label class="radio inline"> 
            <input class="radio inline" name="outputType" type="radio" value="1" checked="checked" > Show Each Engine
		</label>            
		<label class="radio inline">
<input  class="radio inline" name="outputType" type="radio" value="2" > Aggregated  
		</label>
		<label class="radio inline">
            <input  class="radio inline" name="outputType" type="radio" value="3" > Clustered</label>
             
</form> 
      
	</div>

     

      <!-- Example row of columns -->
      <div class="row-fluid"><div class="span4" id="divCheckboxBing" style="display: none;"></div>
	 <div class="span4" id="divCheckboxBlekko" style="display: none;"></div>
        <div class="span4" id="divCheckboxGoogle" style="display: none;"></div></div>

     <hr>

      <div class="footer" style="text-align:center">
        <p>&copy; James Coll</p>
      </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>

  </body>
</html>
