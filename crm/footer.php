<footer class="footer">
	<div class="container-fluid">
		Copyright &copy; 2014-<?php echo date('Y');?> <a href="#" target="_blank"> <?php echo SITENAME;?></a>. All rights reserved. 
		<?php echo $db->getQueriesCount()." Query in ".$db->getExecTime()." Seconds";?>
		<div class="pull-right hidden-xs"> <b>Version</b> <?php echo VERSION;?></div>
	</div>
</footer>
</body>
</html>