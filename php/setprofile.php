<?php
/*
	svc setprofile
	Save profile for logged-in user.
*/
function setprofile() {
	$a = array(
		'status' => 'system-error'
	);

	// raw inputs
	$taint_si = isset($_POST['si']) ? $_POST['si'] : 0;
	$taint_gender = isset($_POST['gender']) ? $_POST['gender'] : '';
	$taint_photo  = isset($_POST['photo'] ) ? $_POST['photo']  : '';
	$taint_phone  = isset($_POST['phone'] ) ? $_POST['phone']  : '';

	// validate inputs
	$si = validateToken($taint_si);
	$gender = validateGender($taint_gender);
	$photo  = validateUrl   ($taint_photo );
	$phone  = validatePhone ($taint_phone );

	// validate parameter set
	if (!$si){
		Log::write(LOG_WARNING, 'attempt with invalid parameter set');
		return $a;
	}
	if (!gender && !photo && !phone) {
		Log::write(LOG_WARNING, 'no inputs');
		return $a;
	}

	// get database connection
	$conn = getConnection();
	if (!$conn) {
		return $a;
	}

	// get logged-in user
	$result = getUserByToken($conn, $si);
	if (!$result) {
		return $a;
	}
	$row = pg_fetch_array($result, 0, PGSQL_ASSOC);
	$userid = $row['id'];

	// attempt to read profile record
	$profileid = 0;
	$name = 'query-profile';
	$sql = "select id from model.profile where userid = $1";
	$params = array($userid);
	$result = execSql($conn, $name, $sql, $params, true);
	if ($result) {
		$row = pg_fetch_array($result, 0, PGSQL_ASSOC);
		$profileid = $row['id'];
	}

	// insert or update profile
	$a['status'] = 'ok';
	if ($profileid) {
		$name = 'update-profile';
		$sql = "update model.profile set gender=$2, photo=$3, phone=$4 where id = $1";
		$params = array($profileid, $gender, $photo, $phone);
		$result = execSql($conn, $name, $sql, $params, true);
		if (!$result) {
			Log::write(LOG_NOTICE, "$name failed");
			$a['status'] = 'failed';
		}
	}
	else {
		$name = 'insert-profile';
		$sql = "insert into model.profile (userid, gender, photo, phone) values ($1,$2,$3,$4)";
		$params = array($userid, $gender, $photo, $phone);
		$result = execSql($conn, $name, $sql, $params, true);
		if (!$result) {
			Log::write(LOG_NOTICE, "$name failed");
			$a['status'] = 'failed';
		}
	}

	// success
	return $a;
}

function validateGender($taint) {
	$clean = false;
 	$ok = preg_match('/^[mfoMFO]{1,1}$/', $taint);
	if ($ok) {
		$clean = $taint;
	}
	return $clean;
}

function validateUrl($taint) {
	$clean = false;
 	$ok = preg_match('/^[a-zA-Z0-9\-.]{4,200}$/', $taint);
	if ($ok) {
		$clean = $taint;
	}
	return $clean;
}

function validatePhone($taint) {
	$clean = false;
 	$ok = preg_match('/^[0-9\ -]{5,20}$/', $taint);
	if ($ok) {
		$clean = $taint;
	}
	return $clean;
}

?>
