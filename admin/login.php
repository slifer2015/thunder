<?php

include 'init.php';

if ($_GB->GetSession('admin') != false) {
    header("location:index.php?cmd=index");
}
?>
<?php
if (isset($_POST['username'], $_POST['password'])) {
    $Users->adminLogin($_POST['username'], $_POST['password']);
} ?>

<?php
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ادمین</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="شبکه مجازی">

    <meta http-equiv="x-pjax-version" content="v173">

    <link rel="stylesheet" type="text/css" href="libs/assets/css/print.css" media="print">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!-- fav and touch icons -->
    <link rel="shortcut icon" href="libs/images/fav.png">
    <link rel="shortcut icon" href="libs/images/fav.png">

    <!-- build:css styles/vendor.css -->
    <!-- bower:css -->
    <link rel="stylesheet" href="libs/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="libs/assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="libs/assets/css/animate.min.css">
    <link rel="stylesheet" href="libs/assets/css/hover-min.css">
    <!-- endbower -->
    <!-- endbuild -->

    <!-- build:css(.tmp) styles/main.css -->
    <link id="style-components" href="libs/assets/css/loaders.css" rel="stylesheet">
    <link id="style-components" href="libs/assets/css/bootstrap-theme.css" rel="stylesheet">
    <link id="style-components" href="libs/assets/css/dependencies.css" rel="stylesheet">
    <link id="style-components" href="libs/assets/css/summernote.css" rel="stylesheet">
    <link id="style-base" href="libs/assets/css/style.css" rel="stylesheet">
    <link id="style-responsive" href="libs/assets/css/stilearn-responsive.css" rel="stylesheet">
    <link id="style-helper" href="libs/assets/css/helper.css" rel="stylesheet">
    <link id="style-sample" href="libs/assets/css/pages-style.css" rel="stylesheet">
    <link id="style-sample" href="libs/assets/css/bootstrap-tagsinput.css" rel="stylesheet">
    <!-- endbuild -->

    <link rel="stylesheet" href="libs/assets/css/jquery.tablesorter.pager.css">

    <!-- persian datePicker style -->
    <link href="libs/assets/css/persianDatepicker-default.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.min.js"></script>
    <![endif]-->

    <script src="libs/assets/js/jquery.js"></script>

</head>

<body class="animated fadeIn">
    <div class="spinnerContainer">
        <!-- loading -->
        <div class="spinner">
            <div class="spinner-container container1">
                <div class="circle1"></div>
                <div class="circle2"></div>
                <div class="circle3"></div>
                <div class="circle4"></div>
            </div>
            <div class="spinner-container container2">
                <div class="circle1"></div>
                <div class="circle2"></div>
                <div class="circle3"></div>
                <div class="circle4"></div>
            </div>
            <div class="spinner-container container3">
                <div class="circle1"></div>
                <div class="circle2"></div>
                <div class="circle3"></div>
                <div class="circle4"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 loginContainer">
            <div class="col-xs-12 col-md-12 internal">
                <div class="row small xsmallSpace"></div>
                <div class="row">
                    <div class="col-md-12">
                        <form role="form" method="post" action="">
                            <input type="hidden" name="action" value="login" />
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input type="text" class="form-control ltr" name="username" id="username" placeholder="نام کاربری" autocomplete="off" autofocus="" spellcheck="false">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input type="password" class="form-control ltr" name="password" id="password" placeholder="گذرواژه" autocomplete="off" spellcheck="false">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 nonSpace">
                                    <input type="submit" class="btn btn-primary btn-default btn-block text-white text-16" value="ورود به سیستم"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- javascript
    ================================================== -->
    <!-- build:js scripts/vendor-main.js -->
    <!-- bower:js -->
    <script src="libs/assets/js/jquery-ui.js"></script>
    <script src="libs/assets/js/jquery.ui.touch-punch.min.js"></script>
    <script src="libs/assets/js/bootstrap.js"></script>
    <!-- endbuild -->

    <!-- filter search table-->
    <script src="libs/assets/js/jquery.filtertable.min.js"></script>

    <!-- notification.js -->
    <script src="libs/assets/js/jquery.bootstrap-growl.min.js"></script>

    <!-- build:js scripts/vendor-usefull.js -->
    <script src="libs/assets/js/pace.min.js"></script>
    <script src="libs/assets/js/jquery.pjax.js"></script>
    <script src="libs/assets/js/masonry.pkgd.min.js"></script>
    <script src="libs/assets/js/screenfull.min.js"></script>
    <script src="libs/assets/js/jquery.nicescroll.min.js"></script>
    <script src="libs/assets/js/countUp.min.js"></script>
    <script src="libs/assets/js/skycons.js"></script>
    <script src="libs/assets/js/jquery.lazyload.min.js"></script>
    <script src="libs/assets/js/wow.min.js"></script>
    <!-- endbuild -->

    <!-- summernote text editor -->
    <script src="libs/assets/js/summernote.min.js"></script>
    <!-- end summernote text editor -->

    <!-- build:js scripts/vendor-form.js -->
    <script src="libs/assets/js/jquery.validate.js"></script>
    <script src="libs/assets/js/additional-methods.js"></script>
    <script src="libs/assets/js/jquery.autogrowtextarea.min.js"></script>
    <script src="libs/assets/js/typeahead.min.js"></script>
    <script src="libs/assets/js/jquery.mask.min.js"></script>
    <script src="libs/assets/js/bootstrap-tagsinput.js"></script>
    <script src="libs/assets/js/jquery.multi-select.js"></script>
    <script src="libs/assets/js/select2.js"></script>
    <script src="libs/assets/js/jquery.selectBoxIt.js"></script>
    <script src="libs/assets/js/moment.js"></script>
    <script src="libs/assets/js/daterangepicker.js"></script>
    <script src="libs/assets/js/bootstrap-datepicker.js"></script>
    <script src="libs/assets/js/bootstrap-timepicker.js"></script>
    <script src="libs/assets/js/jquery.minicolors.min.js"></script>
    <script src="libs/assets/js/dropzone.min.js"></script>
    <script src="libs/assets/js/jquery.steps.min.js"></script>
    <script src="libs/assets/js/fullcalendar.js"></script>
    <!-- endbuild -->

    <!-- build:js scripts/vendor-editor.js -->
    <script src="libs/assets/js/wysihtml5-0.3.0.js"></script>
    <script src="libs/assets/js/bootstrap-wysihtml5-0.0.2.js"></script>
    <script src="libs/assets/js/markdown.js"></script>
    <script src="libs/assets/js/to-markdown.js"></script>
    <script src="libs/assets/js/bootstrap-markdown.js"></script>
    <!-- endbuild -->


    <!-- build:js scripts/excanvas.js -->
    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="libs/assets/js/excanvas.min.js"></script><![endif]-->
    <!-- endbuild -->

    <!-- build:js scripts/vendor-graph.js -->
    <script src="libs/assets/js/raphael-min.js"></script>
    <script src="libs/assets/js/morris.min.js"></script>
    <script src="libs/assets/js/jquery.flot.js"></script>
    <script src="libs/assets/js/jquery.flot.resize.js"></script>
    <script src="libs/assets/js/jquery.flot.categories.js"></script>
    <script src="libs/assets/js/jquery.flot.time.js"></script>
    <script src="libs/assets/js/jquery.flot.axislabels.js"></script>
    <script src="libs/assets/js/jquery.easypiechart.js"></script>
    <script src="libs/assets/js/jquery.sparkline.min.js"></script>
    <!-- endbuild -->

    <!-- build:js scripts/vendor-table.js -->
    <script src="libs/assets/js/jquery.dataTables.js"></script>
    <script src="libs/assets/js/dataTables.tableTools.js"></script>
    <script src="libs/assets/js/datatables.js"></script>
    <script src="libs/assets/js/jquery.tablesorter.min.js"></script>
    <script src="libs/assets/js/jquery.tablesorter.widgets.min.js"></script>
    <script src="libs/assets/js/jquery.tablesorter.pager.min.js"></script>
    <!-- endbuild -->

    <!-- build:js scripts/vendor-util.js -->
    <script src="libs/assets/js/holder.js"></script>
    <!-- endbower -->
    <!-- endbuild -->


    <!-- required stilearn template js -->
    <!-- build:js scripts/main.js -->
    <script src="libs/assets/js/fileinput.js"></script>
    <script src="libs/assets/js/js-prototype.js"></script>
    <script src="libs/assets/js/slip.js"></script>
    <script src="libs/assets/js/hogan-2.0.0.js"></script>
    <script src="libs/assets/js/theme-setup.js"></script>
    <script src="libs/assets/js/chat-setup.js"></script>
    <script src="libs/assets/js/panel-setup.js"></script>
    <!-- endbuild -->

    <!-- highchart.js -->
    <script src="libs/assets/js/highcharts.js"></script>
    <script src="libs/assets/js/exporting.src.js"></script>
    <script src="libs/assets/js/highcharts-more.js"></script>

    <!-- This scripts will be reload after pjax or if popstate event is active (use with class .re-execute) -->
    <!-- build:js scripts/initializer.js -->
    <script class="re-execute" src="libs/assets/js/bootstrap-setup.js"></script>
    <script class="re-execute" src="libs/assets/js/jqueryui-setup.js"></script>
    <script class="re-execute" src="libs/assets/js/dependencies-setup.js"></script>
    <script class="re-execute" src="libs/assets/js/demo-setup.js"></script>
    <!-- endbuild -->

    <!-- print.js -->
    <script class="re-execute" src="libs/assets/js/jquery.print.js"></script>

    <!-- persianDatePicker.js -->
    <script src="libs/assets/js/persianDatepicker.min.js"></script>

    <!-- accounting.js -->
    <script src="libs/assets/js/accounting.min.js"></script>

    <!-- custom.js -->
    <script src="libs/assets/js/poolad.js"></script>



</body>
</html>