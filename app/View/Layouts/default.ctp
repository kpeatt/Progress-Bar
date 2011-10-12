<!DOCTYPE html>
<html lang="eng">

<head>

	<meta charset="utf-8">
	
	<title>Progress Bar: <?php echo $title_for_layout; ?></title>

	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="robots" content="nofollow" />
	
	<?php
		echo $this->Html->meta('icon')."\n\n\t";

		echo $this->Html->css('style')."\n";
		
		echo $scripts_for_layout;
	?>

</head>

<body>

	<?php echo $this->Session->flash(); ?>
	
	<?php echo $content_for_layout; ?>

</body>
</html>