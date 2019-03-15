<?php
session_start();
include("config.php");
$db->execute("INSERT INTO `tbltest`(`id`, `name`, `date`) VALUES ('', 'Test', NOW())");
