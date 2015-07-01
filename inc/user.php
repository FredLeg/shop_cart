<?php

	function user_fullname ( $user_id ) {
		global $db;
		$query = $db->prepare('SELECT firstname, lastname FROM users WHERE id = :id');
		$query->bindValue(':id', $user_id);
		$query->execute();
		$result = $query->fetch();
		return $result['firstname']." ".$result['lastname'];
		}
	function user_exists ( $user_email ) {
		global $db;
		$query = $db->prepare('SELECT * FROM users WHERE email = :email');
		$query->bindValue(':email', $user_email);
		$query->execute();
		$result = $query->fetch();
		return !empty( $result );
		}
	function user_crypt ( $user_password ) {
		return password_hash( $user_password , PASSWORD_BCRYPT );
		}
	function user_mini_show () {
		?><div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
		<div class="panel panel-primary">
		<div class="list-group">
			<?php
			echo '<a href="#" class="list-group-item">'. user_fullname($_SESSION['user_id']), '</a>';
			echo '<a href="logout.php" class="list-group-item">Déconnexion</a>';
			?>
		</div>
		</div>
		</div>
		<?php
		}
	function user_id ( $user_id = false ) {
		if ($user_id) $_SESSION['user_id'] = $user_id;
		return $_SESSION['user_id'];
		}
	function user_is_logged () {
		return !empty( $_SESSION['user_id'] );
		}
	function user_login ( $user_email, $user_password ) {
		global $db;
		$query = $db->prepare('SELECT id, pass FROM users WHERE email = :email');
		$query->bindValue(':email', $user_email);
		$query->execute();
		$result = $query->fetch();
		if ( empty( $result ) ) {
			return false;
		} else {
			if ( password_verify( $user_password, $result['pass'] )) {
				$id = $result['id'];
				user_id ( $id );
			    return $id;
			} else {
				return false;
			}
		}
		}

	define('REMEMBER_ME_SECRET_KEY', 'grain de sable 2015');

	function user_getToken() {
		$protocol = $_SERVER['REQUEST_SCHEME']; // Contient le protocole en cours http ou https

		// On définit l'empreinte de l'utilisateur, url en cours et user agent
		$footprints = $protocol.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].$_SERVER['HTTP_USER_AGENT'];

		// On crée un jeton qui contient la clé secrète concaténée avec l'empreinte de l'utilisateur
		$token = REMEMBER_ME_SECRET_KEY.$footprints;

		return $token;
		}

function setRememberMe($user_id, $expiration) {

	$current_time = time(); // On définit le timestamp actuel

	$token = user_getToken();

	// On définit une chaîne qui contient nos infos en clair
	$user_data = $current_time.'.'.$user_id;

	// On crypte les informations en clair concaténées avec le jeton
	$crypted_token = hash('sha256', $token.$user_data);

	// On stock les infos en clair et les infos cryptées dans des cookies
	setcookie('rememberme_data', $user_data, $current_time + $expiration);
	setcookie('rememberme_token', $crypted_token, $current_time + $expiration);
	}

function getRememberMe($expiration) {

	if (empty($_COOKIE['rememberme_data']) || empty($_COOKIE['rememberme_token'])) {
		return false;
	}

	$current_time = time(); // On définit le timestamp actuel

	$token = user_getToken();

	// On crypt les informations du cookie concaténées avec le jeton
	$crypted_token = hash('sha256', $token.$_COOKIE['rememberme_data']);

	// On vérifie que le jeton du cookie est égal au jeton crypté au dessus
	if(strcmp($_COOKIE['rememberme_token'], $crypted_token) !== 0) {
		return false;
	}

	// On récupère les infos du cookie dans 2 variables, correspondant aux 2 entrées du tableau renvoyé par explode
	list($user_time, $user_id) = explode('.', $_COOKIE['rememberme_data']);

	// On vérifie que le timestamp défini dans le cookie expire dans le futur et qu'il a été défini dans le passé
	if($user_time + $expiration > $current_time && $user_time < $current_time) {
		return $user_id;
	}
	return false;
	}