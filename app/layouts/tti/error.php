<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="A layout example that shows off a responsive email layout.">
		<title><?php
		global $QFC ;
		echo $QFC->getPageTitle(); ?> </title>
		<script type="text/javascript">
			var QFS_SITE_URL = '<?php echo siteUrl();?>' ;
		</script>
	</head>
	<body>
		<?php echo $contents ;?>
	</body>
</html>