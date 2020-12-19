        </div><!-- /#container -->
    <!-- END CONTENT -->


    <!-- BEGIN FOOTER -->	
        <div id="sidebar">
            <?php include('/theme/sidebar.php'); ?>
        </div><!-- /#sidebar -->

    </div><!-- /#wrapper -->
	
    <div id="footer">
        <div class="credit">© 2020 Your Website Name &bull; Theme by <a href="https://www.design-with.in/" target="_blank">Design Within</a> &bull; Powered by <a href="https://github.com/paperains/CORE/" target="_blank">CORE</a></div>
        <div class="disclaimer">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet.<br />Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta. Mauris massa. Vestibulum lacinia arcu eget nulla.</div>
    </div><!-- /#footer -->
    <!-- END FOOTER -->
</div><!-- /alignment -->

<script>
function myFunction() {
    var x = document.getElementById("menu");
    if (x.className === "navbar-fixed-top") {
        x.className += " responsive";
    } else {
        x.className = "navbar-fixed-top";
    }
}
</script>
</body>
</html>
