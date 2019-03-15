<?php



$amount = is_numeric($_GET['a']) ? $_GET['a'] : 0;
echo $amount;