<?php
ob_start(); // fermé dans le bas de footer.php

require_once __DIR__.'/db.php';
require_once __DIR__.'/func.php';
require_once __DIR__.'/user.php';

session_name('shop_session');
session_start();

$env = ( true ? 'DEV' : 'PROD' );

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ( isset($_GET['BUY']) and !empty($_GET['id']) ) {
    $_SESSION['cart'][$_GET['id']] += 1;
	debug( $_SESSION['cart'] );
}
