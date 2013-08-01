<?php
if(session_id() == '') {
	session_start();
}
require_once('functions.php');
$entity = $_SESSION['entity'];
$entity_sub = $_SESSION['entity_sub'];
?>
<html>
	<head>
		<title>Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script type="text/javascript" src="live.js"></script>
	</head>

	<body>



<div class="container">
 <?php include_once('header.php') ?>
<div id="new-task">
				<h2>Create a new note</h2>
				<form align="center" action="task_handler.php?type=task" method="post">
					<p><input type="text" name="title" placeholder="Title" class="text" /></p>
					<p><select name="list" class="select">
						<?php
						foreach ($lists['posts'] as $list) {
							if(!is_null($list['content']['name'])) {
								if (isset($_GET['list'])) {
									if ($list['id'] == $_GET['list']) {
										echo "<option SELECTED value='".$list['id']."'>".$list['content']['name']."</option>";
									}
									else {
										echo "<option value='".$list['id']."'>".$list['content']['name']."</option>";
									}
								}
								else {
									echo "<option value='".$list['id']."'>".$list['content']['name']."</option>";
								}
							}
						}
						?>
					</select>
					<p><textarea name="notes" placeholder="Notes" class="note"></textarea> </p>
					<p>You can use <a href="https://tent.io/docs/post-types#markdown">Tent-flavored Markdown</a> in your notes to add links and style to the text</p>					<p><input type="submit"></p>
				</form>
</div>

</div>

	</body>
</html>
