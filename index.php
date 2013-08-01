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
		<title>Tasky</title>
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
                    <li><a href="index.php">All notebooks</a></li>


				<?php 
				if ($lists['posts'] == '' OR $lists['posts'] == array()) {
					echo "No lists, create one!"; ?>
				<?php
				}
				elseif (isset($posts['error'])) {
					echo "<h3 style='color: red;'>Error: ".$posts['error']."</h3>";
				}
				else { ?>
                <?php
					echo "<table style='margin: 0px; width: 100%;'>";
					foreach ($lists['posts'] as $list) {
						$content = $list['content'];
						echo "<tr>";
						if (!is_null($content['name'])) {
							echo "<td><a href='index.php?list=".$list['id']."'>".$content['name']."</a></td>";
							if (!is_null($content['description'])) {
								echo "<td>".$content['description']."</td>";
							}
							else {
								echo "<td></td>";
							}
							echo "<td><a href='edit.php?list=".$list['id']."'>".Edit."</a></td>";
							if (!is_null($content['description'])) {
								echo "<td>".$content['description']."</td>";
							}
							else {
								echo "<td></td>";
							}
							echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$list['id']."'><img src='img/delete.png' style='width: 8px;'></a></td>"; 
						}

						
						echo "</tr>";
					}
					echo "</table>					<form align='center' method='post' action='task_handler.php?type=list'>
						<input type='text' name='list_name' placeholder='Add new list' class='text' style='width: 70%'/>
						<input type='submit' class='text' style='width: 20%;'>
					</form></div>";
					}
				?>


                		</div>

				<div class='task-list'>

				<div class="filters">Font-size / Font-family
                <a class="javascript-nav" rel="leanModal" href="#new_post"><img src="img/createpost.png" style="margin-left: 20px; width: 28px;" alt="New post"></a>
                <a class="javaless-nav" href="new_post_page.php"><img src="img/createpost.png" style="margin-left: 20px; width: 28px;" alt="New post"></a>
                </div>

				<?php
				if (!isset($_GET['list'])) {
					unset($_SESSION['redirect_list']);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1');
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					echo "<table>";
					foreach ($posts['posts'] as $task) {
						$content = $task['content'];
						echo "<tr class='".$content['status']."'>";

						echo "<td><a class='edit' href='edit.php?type=update&id=".$task['id']."'>".$content['title'];

						/* if ($content['notes'] != '' AND !is_null($content['notes'])) {
							echo "<br><i><div style='font-size: 11px;'>".Tent_Markdown($content['notes'])."</div></i></a></td>";
						}
						else {
							echo "</td>";
						} */

						/* echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$task['id']."'><img src='img/delete.png'></a></td>"; */
						echo "</tr>";
					}
					echo "</table></div>";
				}
				elseif (isset($_GET['list'])) {
					$_SESSION['redirect_list'] = $_GET['list'];
					$id = $_GET['list'];
					$entity_sub_list = substr_replace($_SESSION['entity'] ,"",-1);
					$current_url = str_replace("{entity}", urlencode($entity_sub_list), $_SESSION['single_post_endpoint']);
					$current_url = str_replace("{post}", $id, $current_url);
					$mac_current = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts/'.urlencode($entity_sub_list)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch_current = curl_init();
					curl_setopt($ch_current, CURLOPT_URL, $current_url);
					curl_setopt($ch_current, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch_current, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac_current, time(), $nonce, $_SESSION['client_id'])));
					$current_list = curl_exec($ch_current);
					curl_close($ch_current);
					$current_list = json_decode($current_list, true);

					//Getting tasks from the chosen list
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['list'], $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['list']);
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					if ($posts['posts'] != array()) {
						echo "<table>";
						foreach ($posts['posts'] as $task) {
							$content = $task['content'];
							echo "<tr class='".$content['status']."'>";

							echo "<td><a class='edit' href='edit.php?type=update&id=".$task['id']."'>".$content['title'];

							/* echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$task['id']."'><img src='img/delete.png'></a></td>"; */
							echo "</tr>";
						}
						echo "</table>";
					}
					else {
						echo "<h2>No tasks in \"".$current_list['post']['content']['name']."\"</h2>";
					}
				}
				?>
        </div>
		<?php include('footer.php') ?>

	</body>
</html>
