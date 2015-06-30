<?php
require_once 'inc/db.php';
require_once 'inc/func.php';
require_once 'inc/user.php';

session_name('shop_session');
session_start();

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
