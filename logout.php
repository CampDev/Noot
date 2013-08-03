<?php
session_name('Noot');
session_start();
session_destroy();
header('Location: index.php');
?>
