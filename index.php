<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 9/26/2015
 * Time: 3:58 AM
 */
include 'init.php';
include 'header.php';
if (isset($_SESSION['userID'])) {
    header('Location: home.php');
}
?>
<!--
<div class="center" style="margin-top: 5%;">
    <h5 class="white-text">
        Hello every body
    </h5>

    <h2 class="grey-text">Social Network</h2>

    <h5 class="white-text"> allow you to chat and share your best moments with your <br>
        friends and family so try it now !don't worry you are not alone because <br>
        once you make a new friends you will enjoy with them
    </h5>

</div>-->

<div class="form">

    <ul class="tab-group">
        <li class="tab active"><a href="#login">Log In</a></li>
        <li class="tab "><a href="#signup">Sign Up</a></li>
    </ul>

    <div class="tab-content">

        <div id="login">
            <h1>Welcome Back</h1>

            <form id="loginForm" action="" method="POST">

                <div class="field-wrap">
                    <label>
                        Username<span class="req">*</span>
                    </label>
                    <input id="UserName" name="UserName" type="text" required autocomplete="on"/>
                </div>

                <div class="field-wrap">
                    <label>
                        Password<span class="req">*</span>
                    </label>
                    <input id="UserPassword" name="UserPassword" type="password" required autocomplete="off"/>
                </div>

                <p class="forgot"><a href="#">Forgot Password?</a></p>

                <button class="semi-transparent-button "  type="submit"  onclick=" login();" >Login</button

            </form>

        </div>
        <div   id="signup">
            <h1>Sign Up for Free</h1>

            <form id="loginForm" action="" method="POST">

                <div class="top-row">
                    <div class="field-wrap">
                        <label>
                            UserName<span class="req">*</span>
                        </label>
                        <input id="Username" name="Username" type="text" required autocomplete="on"/>
                    </div>

                    <div class="field-wrap">
                        <label>
                            Full Name<span class="req">*</span>
                        </label>
                        <input id="FullName" name="FullName" type="text" required autocomplete="on"/>
                    </div>
                </div>

                <div class="field-wrap">
                    <label>
                        Email Address<span class="req">*</span>
                    </label>
                    <input id="UserEmail" name="UserEmail" type="email" required autocomplete="on"/>
                </div>
                <div class="field-wrap">
                    <label>
                        Address<span class="req">*</span>
                    </label>
                    <input id="UserAddress" name="UserAddress" type="text" required autocomplete="on"/>
                </div>
                <div class="field-wrap">
                    <label>
                        Job<span class="req">*</span>
                    </label>
                    <input id="UserJob" name="UserJob" type="text" required autocomplete="on"/>
                </div>

                <div class="field-wrap">
                    <label>
                        Password<span class="req">*</span>
                    </label>
                    <input id="userPassword" name="userPassword" type="password" required autocomplete="off"/>
                </div>

                <button class="semi-transparent-button " type="submit" onclick=" register();" >Register</button>

            </form>

        </div>

    </div>
    <!-- tab-content -->

</div>

<?php
include'footer.php';
?>