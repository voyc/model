<?php
/*
	svc getprofile
	Read and return profile for logged-in user.
*/
function getprofile() {
	$a = array(
		'status' => 'system-error'
	);

	// raw inputs
	$taint_si = isset($_POST['si']) ? $_POST['si'] : 0;
	$taint_gender = isset($_POST['gender']) ? ['gender'] : 0;
	$taint_photo  = isset($_POST['photo'] ) ? ['photo']  : 0;
	$taint_phone  = isset($_POST['phone'] ) ? ['phone']  : 0;

	// validate inputs
	$si = validateToken($taint_si);

	// validate parameter set
	if (!$si){
		Log::write(LOG_WARNING, 'attempt with invalid parameter set');
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

	// read profile for logged-in user
	$gender= '';
	$photo = '';
	$phone = '';
	$name = 'query-profile';
	$sql = "select gender, photo, phone from model.profile where userid = $1";
	$params = array($userid);
	$result = execSql($conn, $name, $sql, $params, true);
	if ($result) {
		$row = pg_fetch_array($result, 0, PGSQL_ASSOC);
		$gender= $row['gender'];
		$photo = $row['photo'];
		$phone = $row['phone'];
	}

	// success
	$a['status'] = 'ok';
	$a['gender'] = $gender;
	$a['photo' ] = $photo ;
	$a['phone' ] = $phone ;
	return $a;
}
?>
