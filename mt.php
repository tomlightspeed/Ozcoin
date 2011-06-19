<?php
$mtgox = exec('curl https://mtgox.com/code/data/ticker.php');
echo "$mtgox";
?>
