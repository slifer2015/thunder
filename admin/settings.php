<?php
include 'init.php';
include 'header.php';
if ($_GB->GetSession('admin') == false) {
    header("location:login.php");
}
if (isset($_POST['disclaimer'])) {
    //$disclaimer = $_DB->escapeString($_POST['disclaimer']);
    $siteName = $_DB->escapeString($_POST['site_name']);
    $siteUrl = $_DB->escapeString($_POST['site_url']);
    $apikey = $_DB->escapeString($_POST['googleApiConfig']);
    /*if (isset($_POST['email']) && $_POST['email'] == 'on') {*/
    $emailactivation = $_POST["site_email_activation"] != "1" ? 0 : 1;
    /*} else {
        $emailactivation = 0;
    }*/
    $_GB->updateConfig('emailactivation', $emailactivation, 'users');
    $_GB->updateConfig('site_name', $siteName, 'site');
    $_GB->updateConfig('url', $siteUrl, 'site');

    $_GB->updateConfig('disclaimer', $_POST['disclaimer'], 'site');
    $_GB->updateConfig('googleApiConfig', $apikey, 'site');
    echo $_GB->ShowError('Settings updated successfully', 'yes');

}
?>


    <div class="content">
        <!-- content-control -->
        <div class="content-control">
            <!--control-nav-->
            <ul class="control-nav pull-right">
                <li><a class="rtl text-24"> تنظیمات <i class="fa fa-cogs"></i></a></li>
            </ul><!--/control-nav-->
        </div><!-- /content-control -->

        <div class="content-body">
            <!-- APP CONTENT
            ================================================== -->
            <div class="callout callout-info">
                <p class="rtl">در این بخش شما می توانید تنظیمات پنل را مشاهده نمایید.</p>
            </div>

            <div class="row xsmallSpace"></div>

            <div id="panel-1" class="panel panel-default border-blue">
                <div class="panel-heading bg-blue">
                    <h3 class="panel-title rtl"> تنظیمات </h3>

                    <div class="panel-actions">
                        <button data-expand="#panel-1" title="نمایش" class="btn-panel">
                            <i class="fa fa-expand"></i>
                        </button>
                        <button data-collapse="#panel-1" title="بازکردن" class="btn-panel">
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>

                <div class="panel-body">

                    <div class="alert alert-info fade in">
                        <strong>Site & Application</strong>
                    </div>

                    <form id="loginForm" action="settings.php" method="post">

                    <div class="table-responsive table-responsive-datatables">
                        <table class="table table-striped table-bordered rtl">
                            <thead>
                            <tr>
                                <th>Property</th>
                                <th>Value</th>
                            </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>Site Title</td>
                                    <td>
                                        <input type="text" class="form-control" name="site_name" value="<?php echo htmlentities($_GB->getConfig('site_name', 'site')); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Google API Key</td>
                                    <td>
                                        <input type="text" class="form-control" name="googleApiConfig" value="<?php echo htmlentities($_GB->getConfig('googleApiConfig', 'site')); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Site Url</td>
                                    <td>
                                        <input type="text" class="form-control" name="site_url" value="<?php echo htmlentities($_GB->getConfig('url', 'site')); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>App Users Need Accounts Activation</td>
                                    <td>
                                        <select class="form-control" style="display: block;" name="site_email_activation">
                                            <?php
                                            if ($_GB->getConfig('emailactivation', 'users') == 1) {
                                                echo '<option value="1" selected>Yes</option><option value="2">No</option>';
                                            } else {
                                                echo '<option value="1">Yes</option><option value="2" selected>No</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Disclaimer</td>
                                    <td>
                                        <textarea class="form-control" name="disclaimer" class="materialize-textarea"><?php echo $_GB->getConfig('disclaimer', 'site'); ?></textarea>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    </form>

                </div>

                <div class="panel-footer clearfix">
                    <button type="submit" class="btn btn-icon btn-primary">
                        <i class="fa fa-check"></i>
                        Save
                    </button>
                </div>
            </div>
        </div><!--/content-body -->
    </div>

<?php
include 'footer.php';
?>