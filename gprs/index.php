<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,800' rel='stylesheet' type='text/css'>
<style>
	.body {
		font-family: 'Open Sans', sans-serif;
		font-weight:400;
	}
	.header {
		background:#D7745B;
		min-height:60px;
	}
	.content {
		min-height:360px;
	}
	.content h1 {
		font-family: 'Open Sans', sans-serif;
		font-weight:800;
		color:#6CAADD;
		font-size:72px;
	}
	.content h2 {
		font-family: 'Open Sans', sans-serif;
		font-weight:400;
	}
	.footer {
		position:absolute;
		background:#333;
		bottom:0px;
		width:100%;
		padding:15px;
	}
</style>
</head>
<body>
<div class="content" style="text-align:center; margin-top:100px;">
	<div class="container-fluid">
		<h1>SORRY</h1>
		<h2>The site may have moved to a different server. </h2>
		<p>The URL for this domain may have changed or the hosting provider may have moved the account to a different server. </p>
		<h2>Your IP: <b><?php echo $_SERVER['REMOTE_ADDR'];?></b></h2>
	</div>
</div>
</body>
</html>