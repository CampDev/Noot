<?php 
        require_once('functions.php');
        if(isset($_SESSION['entity'])) {
            //Getting all the notebooks
            $entity = $_SESSION['entity'];
            $entity_sub = $_SESSION['entity_sub'];
            $nonce = uniqid('Noot_', true);
            $mac_notebooks = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Fnoot%2Fnotebook%2Fv0.1', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
            $init_notebooks = curl_init();
            curl_setopt($init_notebooks, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Fnoot%2Fnotebook%2Fv0.1');
            curl_setopt($init_notebooks, CURLOPT_HTTPGET, 1);
            curl_setopt($init_notebooks, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($init_notebooks, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_notebooks.'", ts="'.time().'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"')); //Setting the HTTP header
            $notebooks = curl_exec($init_notebooks);
            curl_close($init_notebooks);
            $notebooks = json_decode($notebooks, true);     
?>

        <div class="header">
        <div class="container">
            <div id="header-inner">

<div class="header-navigation">
                <span style="font-size: 32px; font-family: 'Tangerine', sans-serif;">Noot</span>
<!-- <img src="" style="width: 40px; height: 40px; margin-top: -5px; float: right;"> -->
                <?php } ?>
</div>

            </div>
        </div>
        </div>
