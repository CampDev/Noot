<?php
session_start();
if (!isset($_SESSION['entity'])) {
	$error = "You're not logged in!";
		header('Location: index.php?error='.urlencode($error));
}
else {
require_once('functions.php');
?>
<html>
	<head>
		<title>Noot</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<meta charset="utf-8">
		<script type="text/javascript" src="live.js"></script>
	</head>

	<body>

    <?php include('header.php');?>

		<div id="body_wrap">
			<?php
			if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$nonce = uniqid('Noot_', true);
			$entity_sub = substr_replace($_SESSION['entity'] ,"",-1);

			//Getting the current version of the post
			$current_url = str_replace("{entity}", urlencode($entity_sub), $_SESSION['single_post_endpoint']);
			$current_url = str_replace("{post}", $id, $current_url);
			$mac_current = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
			$ch_current = curl_init();
			curl_setopt($ch_current, CURLOPT_URL, $current_url);
			curl_setopt($ch_current, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch_current, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac_current, time(), $nonce, $_SESSION['client_id'])));
			$current_note_json = curl_exec($ch_current);
			curl_close($ch_current);
			$current_note = json_decode($current_note_json, true);
			?>
            <div id="new-note">
            <h2>Edit your note</h2>
			<form align="center" method="post" action="task_handler.php?type=update&id=<?php echo $current_note['post']['id']; ?>&parent=<?php echo $current_note['post']['version']['id']; ?>">
				<p> <input type="text" name="title" value="<?php echo $current_note['post']['content']['title']; ?>" class="text" placeholder="Title" /></p>

                    <select name="notebook" class="select">
						<?php
						foreach ($notebooks['posts'] as $notebook) {
							if(!is_null($notebook['content']['name'])) {
								if ($notebook['id'] == $current_note['post']['content']['notebook']) {
									echo "<option SELECTED value='".$notebook['id']."'>".$notebook['content']['name']."</option>";
								}
								else {
									echo "<option value='".$notebook['id']."'>".$notebook['content']['name']."</option>";
								}
							}
						}
						?>
					</select>

					<select name="priority" size="1" class="select">
						<?php
							switch ($current_note['post']['content']['priority']) {
								case '0':
									echo "<option SELECTED value='0'>Low</option>";
									echo "<option value='1'>Average</option>";
									echo "<option value='2'>High</option>";
									echo "<option value='3'>Urgent</option>";
									break;

								case '1':
									echo "<option value='0'>Low</option>";
									echo "<option SELECTED value='1'>Average</option>";
									echo "<option value='2'>High</option>";
									echo "<option value='3'>Urgent</option>";
									break;

								case '2':
									echo "<option value='0'>Low</option>";
									echo "<option value='1'>Average</option>";
									echo "<option SELECTED value='2'>High</option>";
									echo "<option value='3'>Urgent</option>";
									break;

								case '3':
									echo "<option value='0'>Low</option>";
									echo "<option value='1'>Average</option>";
									echo "<option value='2'>High</option>";
									echo "<option SELECTED value='3'>Urgent</option>";
									break;
								
								default: //Shouldn't happen
									echo "<option value='0'>Low</option>";
									echo "<option SELECTED value='1'>Average</option>";
									echo "<option value='2'>High</option>";
									echo "<option value='3'>Urgent</option>";
									break;
							}
						?>
					</select>
					<input type="date" name="duedate" min="<?php echo date('Y-m-d', time()); ?>" <?php if(!is_null($current_note['post']['content']['duedate']) AND isset($current_note['post']['content']['duedate']) AND $current_note['post']['content']['duedate'] != '') {echo 'value="'.date('Y-m-d', $current_note['post']['content']['duedate']).'"';} ?>" class="select"> 
					<p><textarea name="notes" class="note"><?php if(!is_null($current_note['post']['content']['notes'])) {echo $current_note['post']['content']['notes'];} ?></textarea></p>
					<p>You can use <a href="https://tent.io/docs/post-types#markdown">Tent-flavored Markdown</a> in your notes to add links and style to the text</p>
					<p><input type="submit"></p>
			</form>
            </div>
            <?php
        	}
        	elseif (isset($_GET['notebook'])) {
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
				?>
				<form align="center" method="post" action="task_handler.php?type=update_notebook&id=<?php echo $current_notebook['post']['id']; ?>&parent=<?php echo $current_notebook['post']['version']['id']; ?>">
					<p><input name="name" type="text" value="<?php echo $current_notebook['post']['content']['name']; ?>" /></p>
					<p><textarea name="description" class="notes"><?php echo $current_notebook['post']['content']['description']; ?></textarea></p>
					<p><input type="submit" /></p>
				</form>
        	<?php
        	}
            ?>
		</div>
<?php include('footer.php') ?>

<?php }
?>
