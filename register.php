<?php
include_once 'header.php';

### Connexion PHP


//debug($_POST);

/*
$lastname = '';
if (!empty($_POST['lastname'])) {
	$lastname = $_POST['lastname'];
}
*/

$lastname   = !empty($_POST['lastname'])   ? $_POST['lastname']           : 'Leg';
$firstname  = !empty($_POST['firstname'])  ? $_POST['firstname']          : 'Fred';
$email      = !empty($_POST['email'])      ? $_POST['email']              : 'frederic.legembre@gmail.com';
$password   = !empty($_POST['password'])   ? $_POST['password']           : 'aabbcc';
$password2  = !empty($_POST['password2'])  ? $_POST['password2']          : 'aabbcc';
$newsletter = !empty($_POST['newsletter']) ? intval($_POST['newsletter']) : 0;

$errors = array();

// On a appuyé sur le bouton Envoyer, le formulaire a été soumis
if (!empty($_POST)) {

	if (empty($lastname)) {
		$errors['lastname'] = 'Vous devez renseigner votre nom';
	}
	if (empty($firstname)) {
		$errors['firstname'] = 'Vous devez renseigner votre prénom';
	}
	if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors['email'] = 'Vous devez renseigner un email valide';
	}
	if (empty($password)) {
		$errors['password'] = 'Vous devez renseigner votre mot de passe';
	}
	if (strlen($password2)<6) {
		$errors['password2'] = 'Vous devez fournir un mot de passe de 6 caractères minimum';
	}
	if (strcmp($password2,$password)!== 0) {
		$errors['password2'] = 'Vous devez confirmer votre mot de passe';
	}

	if (empty($errors)) {

		if ( user_exists( $email ) ) {
			$errors['email-already-exists'] = "L'email est déjà pris";
		} else {

			$query = $db->prepare('INSERT INTO users (lastname, firstname, email, newsletter, pass, register_date) VALUES (:lastname, :firstname, :email, :newsletter, :password, NOW())');
			$query->bindValue('lastname',   $lastname);
			$query->bindValue('firstname',  $firstname);
			$query->bindValue('email',      $email);
			$query->bindValue('password',   user_crypt( $password ));
			$query->bindValue('newsletter', $newsletter, PDO::PARAM_INT);
			$db_error = null;
			//try {
				$query->execute();
			//} catch ( Exception $error ) {
			//	$db_error = $error;
			//	echo $db_error.getCode();
			//}
			$result = $db->lastInsertId();

			if (empty($result)) {
				echo '<div class="alert alert-danger" role="danger">Une erreur est survenue</div>';
				goto form;
			} else {
				echo '<div class="alert alert-success" role="success">Merci :)</div>';
				$_SESSION['user_id'] = $db->lastInsertId();
				echo "user_id:", $_SESSION['user_id'];
				goto end;
			}
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

	<div class="form-group<?= !empty($errors['lastname']) ? ' has-error' : '' ?>">
		<label for="lastname" class="col-sm-2 control-label">Nom</label>
		<div class="col-sm-3">
			<input type="text" id="lastname" name="lastname" class="form-control" placeholder="Nom" value="<?= $lastname ?>">
		</div>
	</div>

	<div class="form-group<?= !empty($errors['firstname']) ? ' has-error' : '' ?>">
		<label for="firstname" class="col-sm-2 control-label">Prénom</label>
		<div class="col-sm-3">
			<input type="text" id="firstname" name="firstname" class="form-control" placeholder="Prénom" value="<?= $firstname ?>">
		</div>
	</div>

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

	<div class="form-group<?= !empty($errors['password2']) ? ' has-error' : '' ?>">
		<label for="password2" class="col-sm-2 control-label">Confirmation</label>
		<div class="col-sm-3">
			<input type="password" id="password2" name="password2" class="form-control" placeholder="Confirmation du mot de passe" value="<?= $password2 ?>">
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
					<input type="checkbox" name="newsletter" value="1" <?= $newsletter ? 'checked' : '' ?>> S'abonner à la newsletter
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

<?php
end:

include_once 'footer.php';
?>