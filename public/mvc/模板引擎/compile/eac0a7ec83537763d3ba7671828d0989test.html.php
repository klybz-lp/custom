<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $title; ?></title>
</head>
<body>
    <?php include "D:\web\yaoyue\public\mvc/compile/6f573c9cd64b9fdc061b768a273027e4head.html.php" ?>
    <p><?php /*$title*/; ?></p>
	<?php foreach($data as $value): ?>
	<p><?php echo $value; ?></p>
	<?php endforeach?>

	<?php foreach($score as $key=>$value): ?>
	<p><?php echo $key; ?>||<?php echo $value; ?></p>
	<?php endforeach?>

    <?php foreach($cj as $value): ?>
	<?php foreach($value as $key=>$v): ?>
	<p><?php echo $key; ?>||<?php echo $v; ?></p>
	<?php endforeach?>
	<?php endforeach?>

	<?php if($number>0): ?>
	<p><?php echo $number; ?>大于0</p>
	<?php elseif($number==0): ?>
	<p><?php echo $number; ?>等于0</p>
	<?php else: ?>
	<p><?php echo $number; ?>小于0</p>
	<?php endif?>


</body>
</html>
