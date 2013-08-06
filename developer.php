<html>
	<head>
		<title>Developer Ressources - Tasky</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
   		<div style="margin-top: 40px;" class="container">
		<?php
			require_once('markdown.php');
			$markdown = '#Noot Post Type Documentation

To store notes and notebooks, Noot uses two post types:

##Notes 
####URL: http://cacauu.de/noot/note/v0.1

* **Title (required) - Title of the note**

* **Body (required) - Body of the task, can use [Tent-flavored Markdown](https://tent.io/docs/post-types#markdown)**

##Notebooks
####URL: http://cacauu.de/noot/note/v0.1

* **Name (required) - Name of the note**';
			$html = Markdown($markdown);
			echo $html;
		?>
		</div>
	</body>
</html>
