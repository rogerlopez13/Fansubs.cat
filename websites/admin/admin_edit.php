<?php
$header_title="Edició d’administradors - Altres";
$page="other";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['username'])) {
			$data['username']=escape($_POST['username']);
		} else {
			crash("Dades invàlides: manca username");
		}
		if (!empty($_POST['username_old'])) {
			$data['username_old']=escape($_POST['username_old']);
		} else if ($_POST['action']=='edit') {
			crash("Dades invàlides: manca username_old");
		} else {
			$data['username_old']=NULL;
		}
		if (!empty($_POST['password'])) {
			$data['password']=$password=hash('sha256', PASSWORD_SALT . $_POST['password']);
		} else if ($_POST['action']=='edit') {
			$data['password']=NULL;
		} else {
			crash("Dades invàlides: manca password");
		}
		if (!empty($_POST['admin_level']) && is_numeric($_POST['admin_level'])) {
			$data['admin_level']=escape($_POST['admin_level']);
		} else {
			crash("Dades invàlides: manca admin_level");
		}
		if (!empty($_POST['fansub_id']) && is_numeric($_POST['fansub_id'])) {
			$data['fansub_id']=escape($_POST['fansub_id']);
		} else {
			$data['fansub_id']="NULL";
		}
		if (!empty($_POST['default_storage_processing']) && is_numeric($_POST['default_storage_processing'])) {
			$data['default_storage_processing']=escape($_POST['default_storage_processing']);
		} else {
			crash("Dades invàlides: manca default_storage_processing");
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-admin-user", "S’ha actualitzat l’administrador «".$_POST['username']."»");
			if ($data['password']!=NULL) {
				query("UPDATE admin_user SET username='".$data['username']."',password='".$data['password']."',admin_level=".$data['admin_level'].",fansub_id=".$data['fansub_id'].",default_storage_processing=".$data['default_storage_processing'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE username='".$data['username_old']."'");
			} else {
				query("UPDATE admin_user SET username='".$data['username']."',admin_level=".$data['admin_level'].",fansub_id=".$data['fansub_id'].",default_storage_processing=".$data['default_storage_processing'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE username='".$data['username_old']."'");
			}
		}
		else {
			log_action("create-admin-user", "S’ha creat l’administrador «".$_POST['username']."»");
			query("INSERT INTO admin_user (username,password,admin_level,fansub_id,default_storage_processing,created,created_by,updated,updated_by) VALUES ('".$data['username']."','".$data['password']."',".$data['admin_level'].",".$data['fansub_id'].",".$data['default_storage_processing'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
		}

		$_SESSION['message']="S’han desat les dades correctament.";

		header("Location: admin_list.php");
		die();
	}

	if (!empty($_GET['id'])) {
		$result = query("SELECT u.* FROM admin_user u WHERE username='".escape($_GET['id'])."'");
		$row = mysqli_fetch_assoc($result) or crash('Admin not found');
		mysqli_free_result($result);
	} else {
		$row = array();
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['username']) ? "Edita l’administrador" : "Afegeix un administrador"; ?></h4>
					<hr>
					<form method="post" action="admin_edit.php">
						<div class="mb-3">
							<label for="form-user" class="mandatory">Usuari</label>
							<input class="form-control" name="username" id="form-user" required maxlength="200" value="<?php echo $row['username']; ?>" autocomplete="new-password">
							<input type="hidden" name="username_old" value="<?php echo htmlspecialchars($row['username']); ?>">
						</div>
						<div class="mb-3">
<?php
	if ($row['username']==NULL) {
?>
							<label for="form-password" class="mandatory">Contrasenya</label>
							<input class="form-control" type="password" name="password" required id="form-password" autocomplete="new-password">
<?php
	} else {
?>
							<label for="form-password">Contrasenya (introdueix-la només si la vols canviar)</label>
							<input class="form-control" type="password" name="password" id="form-password" autocomplete="new-password">
<?php
	}
?>
						</div>
						<div class="mb-3">
							<label for="form-admin-level" class="mandatory">Nivell d’administrador</label>
							<select class="form-select" name="admin_level" id="form-admin-level" required>
								<option value="">- Selecciona un nivell -</option>
								<option value="1"<?php echo $row['admin_level']==1 ? " selected" : ""; ?>>1: Gestor de versions</option>
								<option value="2"<?php echo $row['admin_level']==2 ? " selected" : ""; ?>>2: Gestor de fitxes i versions</option>
								<option value="3"<?php echo $row['admin_level']==3 ? " selected" : ""; ?>>3: Control total</option>
							</select>
						</div>
						<div class="mb-3">
							<label for="form-fansub">Fansub associat</label>
							<select name="fansub_id" class="form-select" id="form-fansub">
								<option value="">- No associat a cap fansub -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
								<option value="<?php echo $frow['id']; ?>"<?php echo $row['fansub_id']==$frow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
							</select>
						</div>
						<div class="mb-3">
							<label for="form-default_storage_processing" class="mandatory">Processament de fitxers per defecte</label>
							<select class="form-select" name="default_storage_processing" id="form-default_storage_processing" required>
								<option value="">- Selecciona un processament -</option>
								<option value="1"<?php echo $row['default_storage_processing']==1 ? " selected" : ""; ?>>Recomprimeix el vídeo i l’àudio</option>
								<option value="0"<?php echo $row['default_storage_processing']==0 ? " selected" : ""; ?>>Recomprimeix el vídeo, copia l’àudio</option>
								<option value="2"<?php echo $row['default_storage_processing']==2 ? " selected" : ""; ?>>Recomprimeix l’àudio, copia el vídeo</option>
								<option value="3"<?php echo $row['default_storage_processing']==3 ? " selected" : ""; ?>>No recomprimeixis res (regenera l’MP4)</option>
								<option value="4"<?php echo $row['default_storage_processing']==4 ? " selected" : ""; ?>>Copia sense cap canvi (còpia 1:1)</option>
								<option value="5"<?php echo $row['default_storage_processing']==5 ? " selected" : ""; ?>>Omet l’emmagatzematge local</option>
							</select>
						</div>
						<div class="mb-3 text-center pt-2">
							<button type="submit" name="action" value="<?php echo !empty($row['username']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['username']) ? "Desa els canvis" : "Afegeix l’administrador"; ?></button>
						</div>
					</form>
					
				</article>
			</div>
		</div>
<?php
}

else{
	header("Location: login.php");
}



include("footer.inc.php");
?>
