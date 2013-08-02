<?php
session_start();
if (!isset($_SESSION['entity'])) {
	header('Location: landing.php');
}
require_once('functions.php');
require_once('tent-markdown.php');
?>
<html>
	<head>
		<title>Noot</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<meta charset="utf-8">
		<script type="text/javascript" src="jquery.min.js"></script>
		<script type="text/javascript" src="jquery.leanModal.min.js"></script>
		<script>
			$(function() {
    			$('a[rel*=leanModal]').leanModal({ top : 200, closeButton: ".modal_close" });		
			});
		</script>
	</head>

	<body>

	<?php include('header.php'); ?>

			<div class="container">
				<div class="sidebar">
				<h2>Notebooks</h2>
                    <a href="index.php">All notebooks</a>


				<?php 
				if ($notebooks['posts'] == '' OR $notebooks['posts'] == array()) {
					echo "No notebooks, create one!"; ?>
				<?php
				}
				elseif (isset($posts['error'])) {
					echo "<h3 style='color: red;'>Error: ".$posts['error']."</h3>";
				}
				else { ?>
                <?php
					echo "";
					foreach ($notebooks['posts'] as $notebook) {
						$content = $notebook['content'];
						echo "";
						if (!is_null($content['name'])) {
							echo "<li><a href='index.php?notebook=".$notebook['id']."'>".$content['name']."</a>";
							if (!is_null($content['description'])) {
								echo "".$content['description']."";
							}
							else {
								echo "";
							}
							echo "<a style='float: right;' href='edit.php?notebook=".$notebook['id']."'>".E."</a>";
							if (!is_null($content['description'])) {
								echo "".$content['description']."";
							}
							else {
								echo "";
							}
							echo "<a class='delete' href='task_handler.php?type=delete&id=".$notebook['id']."'><img src='img/delete.png' style='width: 8px; float: right; margin: 3px;'></a></li>"; 
						}
}
						

					echo "<form align='center' method='post' action='task_handler.php?type=notebook' style='margin-top: 30px;'>
						<input type='text' name='notebook_name' placeholder='Add new notebook' class='text' style='width: 70%'/>
						<input type='submit' class='text' style='width: 28%;'>
					</form></div>";
					}
				?>



                		</div>

				<div class='note-list' style="height: 100%;">

				<div class="filters">
                <a class="javascript-nav" rel="leanModal" href="#new_post" style="font-size: 16px;">Create new note +</a>
                <a class="javaless-nav" href="new_post_page.php" style="font-size: 16px;">Create new note +</a>
                </div>

				<?php
				if (!isset($_GET['notebook'])) {
					unset($_SESSION['redirect_notebook']);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Fnoot%2Fnote%2Fv0.1', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Fnoot%2Fnote%2Fv0.1');
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					echo "<table>";
					foreach ($posts['posts'] as $note) {
						$content = $note['content'];
						echo "<tr class='".$content['status']."'>";

						echo "<td><a href='index.php?note=".$note['id']."'>".$content['title']."</a>";

						/* if ($content['notes'] != '' AND !is_null($content['notes'])) {
							echo "<br><i><div style='font-size: 11px;'>".Tent_Markdown($content['notes'])."</div></i></a></td>";
						}
						else {
							echo "</td>";
						} */

						/* echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$note['id']."'><img src='img/delete.png'></a></td>"; */
						echo "</tr>";
					}
					echo "</table></div>";
				}
				elseif (isset($_GET['notebook'])) {
					$_SESSION['redirect_notebook'] = $_GET['notebook'];
					$id = $_GET['notebook'];
					$entity_sub_notebook = substr_replace($_SESSION['entity'] ,"",-1);
					$current_url = str_replace("{entity}", urlencode($entity_sub_notebook), $_SESSION['single_post_endpoint']);
					$current_url = str_replace("{post}", $id, $current_url);
					$mac_current = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts/'.urlencode($entity_sub_notebook)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch_current = curl_init();
					curl_setopt($ch_current, CURLOPT_URL, $current_url);
					curl_setopt($ch_current, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch_current, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac_current, time(), $nonce, $_SESSION['client_id'])));
					$current_notebook = curl_exec($ch_current);
					curl_close($ch_current);
					$current_notebook = json_decode($current_notebook, true);

					//Getting notes from the chosen notebook
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Fnoot%2Fnote%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['notebook'], $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Fnoot%2Fnote%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['notebook']);
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					if ($posts['posts'] != array()) {
						echo "<table>";
						foreach ($posts['posts'] as $note) {
							$content = $note['content'];
							echo "<tr class='".$content['status']."'>";

						    echo "<td><a href='index.php?note=".$note['id']."'>".$content['title']."</a>";

							/* echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$note['id']."'><img src='img/delete.png'></a></td>"; */
							echo "</tr>";
						}
						echo "</table>";
					}
					else {
						echo "<h2>No notes in \"".$current_notebook['post']['content']['name']."\"</h2>";
					}
				}
				?>
        </div>
        <div class="note-list" style="max-width: 700px; height: 100%;">
				<div class="filters" style='max-width: 674px;'></div>

        <!-- experimental -->
                    <?php if (isset($_GET['note'])) {
					$_SESSION['redirect_note'] = $_GET['note'];
					$id = $_GET['note'];
					$entity_sub_notebook = substr_replace($_SESSION['entity'] ,"",-1);
					$current_url = str_replace("{entity}", urlencode($entity_sub_notebook), $_SESSION['single_post_endpoint']);
					$current_url = str_replace("{post}", $id, $current_url);
					$mac_current = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts/'.urlencode($entity_sub_notebook)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch_current = curl_init();
					curl_setopt($ch_current, CURLOPT_URL, $current_url);
					curl_setopt($ch_current, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch_current, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac_current, time(), $nonce, $_SESSION['client_id'])));
					$current_notebook = curl_exec($ch_current);
					curl_close($ch_current);
					$current_notebook = json_decode($current_notebook, true);

					//Getting notes from the chosen notebook
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Fnoot%2Fnote%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['note'], $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Fnoot%2Fnote%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['note']);
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					if ($posts['posts'] != array()) {
						echo "<table>";
						foreach ($posts['posts'] as $note) {
							$content = $note['content'];
							echo "<tr class='".$content['status']."'>";

						    echo "<td><a href='index.php?note=".$note['id']."'>".$content['title']."</a>";

							/* echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$note['id']."'><img src='img/delete.png'></a></td>"; */
							echo "</tr>";
						}
						echo "</table>";
					}
					else {
						echo "<h2>No notes in \"".$current_notebook['post']['content']['name']."\"</h2>";
					}
				}
				?>
        <!-- experimental -->

</div>
		<?php include('footer.php') ?>

	</body>
</html>
