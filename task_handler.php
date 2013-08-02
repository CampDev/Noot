<?php
	session_start();
	if (isset($_GET['type']) AND isset($_SESSION['entity'])) {
		require_once('functions.php');
		$entity_sub = substr_replace($_SESSION['entity'] ,"",-1);
		$nonce = uniqid('Noot_', true);
		if (isset($_SESSION['redirect_notebook'])) {
			$redirect_url = 'index.php?notebook='.$_SESSION['redirect_notebook'];
			unset($_SESSION['redirect_notebook']);
		}
		else {
			$redirect_url = 'index.php';
		}
		switch ($_GET['type']) {
				case 'complete': //Post completed
					//Getting the current version of the post
					$id = $_GET['id'];
					$nonce = uniqid('Noot_', true);
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
					$parent_version = $_GET['parent'];

					//Building the new note
					$completed_post_raw = array(
						'id' => $id,
						'entity' => substr($_SESSION['entity'], 0, strlen($_SESSION['entity']) -1),
						'type' => 'http://cacauu.de/noot/note/v0.1#done',
						'content' => array(
							'title' => $current_note['post']['content']['title'],
							'status' => 'Done',
							'priority' => $current_note['post']['content']['priority'],
							'notebook' => $current_note['post']['content']['notebook'],
							'assignee' => '',
							'duedate' => $current_note['post']['content']['duedate'],
							'notes' => $current_note['post']['content']['notes'],
						),
						'version' => array(
							'parents' => array(
								array(
									'version' => $parent_version,
								),
							),
						),
						'mentions' => array(
							array(
								'entity' => $_SESSION['entity_sub'],
								'post' => $current_note['post']['content']['notebook'],
								'type' => 'http://cacauu.de/noot/note/v0.1#todo',
							),
						),
					);
					$completed_post = json_encode($completed_post_raw);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'PUT', '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']."/".urlencode($entity_sub)."/".$id);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
					curl_setopt($ch, CURLOPT_POSTFIELDS, $completed_post);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id'])."\n".'Content-Type: application/vnd.tent.post.v0+json; type="http://cacauu.de/noot/note/v0.1#done"'));
					$complete_note = curl_exec($ch);
					curl_close($ch);
					if (!isset($complete_note['error'])) {
						header('Location: '.$redirect_url);
					}
					break;

				case 'uncomplete': //Post completed
					//Getting the current version of the post
					$id = $_GET['id'];
					$nonce = uniqid('Noot_', true);
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
					$parent_version = $_GET['parent'];

					//Building the new note
					$uncompleted_post_raw = array(
						'id' => $id,
						'entity' => substr($_SESSION['entity'], 0, strlen($_SESSION['entity']) -1),
						'type' => 'http://cacauu.de/noot/note/v0.1#todo',
						'content' => array(
							'title' => $current_note['post']['content']['title'],
							'status' => 'To Do',
							'priority' => $current_note['post']['content']['priority'],
							'notebook' => $current_note['post']['content']['notebook'],
							'assignee' => '',
							'duedate' => $current_note['post']['content']['duedate'],
							'notes' => $current_note['post']['content']['notes'],
						),
						'version' => array(
							'parents' => array(
								array(
									'version' => $parent_version,
								),
							),
						),
						'mentions' => array(
							array(
								'entity' => $_SESSION['entity_sub'],
								'post' => $current_note['post']['content']['notebook'],
								'type' => 'http://cacauu.de/noot/note/v0.1#todo',
							),
						),
					);
					$uncompleted_post = json_encode($uncompleted_post_raw);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'PUT', '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']."/".urlencode($entity_sub)."/".$id);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
					curl_setopt($ch, CURLOPT_POSTFIELDS, $uncompleted_post);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id'])."\n".'Content-Type: application/vnd.tent.post.v0+json; type="http://cacauu.de/noot/note/v0.1#todo"'));
					$uncomplete_note = curl_exec($ch);
					curl_close($ch);
					if (!isset($uncomplete_note['error'])) {
						header('Location: '.$redirect_url);
					}
					break;

				case 'update': //Updated post sent
					$id = $_GET['id'];
					$parent = $_GET['parent'];
					if (is_null($_POST['notes'])) {
						$_POST['notes'] = '';
					}
					$updated_post_raw = array(
						'id' => $id,
						'entity' => substr($_SESSION['entity'], 0, strlen($_SESSION['entity']) -1),
						'type' => 'http://cacauu.de/noot/note/v0.1#'.$_POST['status'],
						'content' => array(
							'title' => $_POST['title'],
							'body' => $_POST['body'],
						),
						'version' => array(
							'parents' => array(
								array(
									'version' => $parent,
								),
							),
						),
						'mentions' => array(
							array(
								'entity' => $_SESSION['entity_sub'],
								'post' => $_POST['notebook'],
								'type' => 'http://cacauu.de/noot/note/v0.1#todo',
							),
						),
					);
					var_export($_POST);
					echo "<hr />";
					var_export($updated_post_raw);
					echo "<hr/>";
					$updated_post = json_encode($updated_post_raw);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'PUT', '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']."/".urlencode($entity_sub)."/".$id);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
					curl_setopt($ch, CURLOPT_POSTFIELDS, $updated_post);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id'])."\n".'Content-Type: application/vnd.tent.post.v0+json; type="http://cacauu.de/noot/note/v0.1#'.$_POST['status'].'"'));
					$update_note = curl_exec($ch);
					var_export($update_note);
					curl_close($ch);
					if (!isset($update_note['error'])) {
						header('Location: '.$redirect_url);
					}
					break;

				case 'delete':
					$id = $_GET['id'];
					$mac = generate_mac('hawk.1.header', time(), $nonce, "DELETE", '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']."/".urlencode($entity_sub)."/".$id);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
					curl_setopt($ch, CURLOPT_VERBOSE, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id'])."\n".'Content-Type: application/vnd.tent.post.v0+json;'));
					$delete = curl_exec($ch);
					curl_close($ch);
					if (!isset($delete['error'])) {
						header('Location: index.php');
					}
					break;

				case 'update_notebook': //Updated post sent
					$id = $_GET['id'];
					$parent = $_GET['parent'];
					$name = $_POST['name'];
					$description = $_POST['description'];
					$updated_notebook = array(
						'type' => 'http://cacauu.de/noot/notebook/v0.1#',
						'permissions' => array(
							'public' => false,
						),
						'content' => array(
							'name' => $name,
							'description' => $description,
						)
					);
					$updated_notebook = json_encode($updated_notebook);
					var_export($updated_notebook);
					echo "<hr />";
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'PUT', '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']."/".urlencode($entity_sub)."/".$id);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
					curl_setopt($ch, CURLOPT_POSTFIELDS, $updated_notebook);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id'])."\n".'Content-Type: application/vnd.tent.post.v0+json; type="http://cacauu.de/noot/notebook/v0.1#'));
					$update_notebook = curl_exec($ch);
					curl_close($ch);
					var_export($update_notebook);
					/*if (!isset($update_note['error'])) {
						$_SESSION['updated'] = $_POST['title'];
						header('Location: index.php');
					}*/
					break;

				case 'delete':
					$id = $_GET['id'];
					$mac = generate_mac('hawk.1.header', time(), $nonce, "DELETE", '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']."/".urlencode($entity_sub)."/".$id);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
					curl_setopt($ch, CURLOPT_VERBOSE, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id'])."\n".'Content-Type: application/vnd.tent.post.v0+json;'));
					$delete = curl_exec($ch);
					curl_close($ch);
					if (!isset($delete['error'])) {
						header('Location: '.$redirect_url);
					}
					break;

				case 'note':
					$post_raw = array(
						'type' => 'http://cacauu.de/noot/note/v0.1#',
						'permissions' => array(
							'public' => false,
						),
						'content' => array(
							'title' => $_POST['title'],
							'body' => $_POST['body'],
						),
						'mentions' => array(
							array(
								'entity' => $_SESSION['entity_sub'],
								'post' => $_POST['notebook'],
								'type' => 'http://cacauu.de/noot/note/v0.1#',
							),
						),
					);
					$post_json = json_encode($post_raw);
					$entity = $_SESSION['entity'];
					$entity_sub_note = $_SESSION['entity_sub'];
					$mac_send = generate_mac('hawk.1.header', time(), $nonce, 'POST', '/posts', $entity_sub_note, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_send.'", ts="'.time().'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"'."\n".'Content-Type: application/vnd.tent.post.v0+json; type="http://cacauu.de/noot/note/v0.1#"')); //Setting the HTTP header
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$new_note = json_decode(curl_exec($ch), true);
					curl_close($ch);
					if (!isset($new_note['error'])) {
						header('Location: '.$redirect_url);
					}					
					break;

				case 'notebook':
					$post_raw = array(
						'type' => 'http://cacauu.de/noot/notebook/v0.1#',
						'permissions' => array(
							'public' => false,
						),
						'content' => array(
							'name' => $_POST['notebook_name'],
						)
					);
					$post_json = json_encode($post_raw);
					$entity = $_SESSION['entity'];
					$entity_sub_notebook = $_SESSION['entity_sub'];
					$mac_send = generate_mac('hawk.1.header', time(), $nonce, 'POST', '/posts', $entity_sub_notebook, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_send.'", ts="'.time().'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"'."\n".'Content-Type: application/vnd.tent.post.v0+json; type="http://cacauu.de/noot/notebook/v0.1#"')); //Setting the HTTP header
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$new_notebook = json_decode(curl_exec($ch), true);
					curl_close($ch);
					if (!isset($new_notebook['error'])) {
						header('Location: '.$redirect_url);
					}
					break;
				
				default: //Shouldn't happen
					# code...
					break;
			}	
	}
	elseif (!isset($_SESSION['entity'])) {
		$error = "You're not logged in!";
		header('Location: index.php?error='.urlencode($error));
	}
?>
