<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 9/26/2015
 * Time: 4:55 PM
 */

?>
<!--Import jQuery before materialize.js-->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="libs/js/materialize.min.js"></script>
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="libs/js/index.js"></script>
<?php if(isset($_SESSION['userID'])){ ?>
    <script type="text/javascript" src="libs/js/core.js"></script>
    <script type="text/javascript">
        function getFile(){
            document.getElementById("imageFile").click();
            return false;
        }
        $(document).ready(function(){

            var page = 1;
            var totalPages = <?php echo $_PAG->pages;?>;
            $(window).scroll(function(){
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                    page = page + 1;
                    if(page <= totalPages){
                        getPosts(page);
                    }
                }
            });
            getPosts(1);
        });

    </script>
<?php } ?>
</body>
</html>
