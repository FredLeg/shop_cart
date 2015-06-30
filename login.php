<?php
include_once 'header.php';


### Connexion PHP
$expiration = 60 * 60 * 24 * 7; // 7 jours

$rememberMe = getRememberMe( $expiration );

echo  $rememberMe ;


if ( $rememberMe !== false ) {

	user_id( $rememberMe );

	//header('Location: /');
	//exit;

}

//debug($_POST);

/*
$lastname = '';
if (!empty($_POST['lastname'])) {
	$lastname = $_POST['lastname'];
}
*/

$email    = !empty($_POST['email'])    ? $_POST['email']        : 'frederic.legembre@gmail.com';
$password = !empty($_POST['password']) ? $_POST['password']     : 'aabbcc';
$memo     = !empty($_POST['memo'])     ? intval($_POST['memo']) : 0;
// memo = remember_me

$errors = array();

// On a appuyé sur le bouton Envoyer, le formulaire a été soumis
if (!empty($_POST)) {

	if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors['email'] = 'Vous devez renseigner un email valide';
	}
	if (empty($password)) {
		$errors['password'] = 'Vous devez renseigner votre mot de passe';
	}

	if (empty($errors)) {

		$okid = user_login( $email, $password );
		if ( !$okid ) {
			$errors['bad_login'] = "Il n'existe pas de compte avec cet email / mot de passe";
		} else {
			if (!empty($memo)) {
				setRememberMe( user_id(), $expiration );
			}
			$_SESSION['user_id'] = $okid;
			echo '<div class="alert alert-success" role="success">Authentification réussie</div>';
			echo redirect_js( '/' ,2 );
		}
	}
}
form:
?>

<h1>S'inscrire</h1>

<?php if (!empty($errors)) { ?>
<div class="alert alert-danger" role="danger">
	<?php
	foreach ($errors as $error) {
		echo $error.'<br>';
	}
	?>
</div>
<?php } ?>

<form class="form-horizontal" action="" method="POST" novalidate>


	<div class="form-group<?= !empty($errors['email']) ? ' has-error' : '' ?>">
		<label for="email" class="col-sm-2 control-label">Email</label>
		<div class="col-sm-5">
			<input type="email" id="email" name="email" class="form-control" placeholder="Email" value="<?= $email ?>">
		</div>
	</div>

	<div class="form-group<?= !empty($errors['password']) ? ' has-error' : '' ?>">
		<label for="password" class="col-sm-2 control-label">Mot de passe</label>
		<div class="col-sm-3">
			<input type="password" id="password" name="password" class="form-control" placeholder="Mot de passe" value="<?= $password ?>">
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
					<input type="checkbox" name="memo" value="1" <?= $memo ? 'checked' : '' ?>> Se souvenir de moi
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-default">Envoyer</button>
		</div>
	</div>
</form>

<hr>
<a class="btn btn-primary" href="<?= $loginUrl ?>">Facebook Connect</a>

<?php
end:

include_once 'footer.php';
?>