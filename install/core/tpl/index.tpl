<!doctype html>
<html>
	<head>
		<title>EspoCRM Installation</title>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		{if $isBuilt eq true}
			<script type="text/javascript" src="../client/espo.min.js"></script>
			<link href="../client/css/espo.min.css" rel="stylesheet">
		{else}
			<script type="text/javascript" src="../frontend/client/lib/jquery-2.0.2.min.js"></script>
			<link href="../frontend/client/css/bootstrap.css" rel="stylesheet">
		{/if}

		<script type="text/javascript" src="js/install.js"></script>
		<link href="css/install.css" rel="stylesheet">
	</head>
	<body class='install-body'>
		<header id="header"></header>
		<div class="container content">
			<div class="col-md-offset-1 col-md-10">
				<div class="panel panel-default">
					{include file="header.tpl"}
					{include file="$tplName"}
				</div>
			</div>
		</div>
		<footer class="container">{include file="footer.tpl"}</footer>
	</body>

</html>
