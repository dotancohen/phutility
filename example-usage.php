<?php

// Was:

if ( isset($_POST['name']) && $_POST['name']!='' && isset($_POST['password']) && $_POST['password']!='' ) {
	login();
} else {
	send_error();
}



// Now:

if ( ensure_fields($_POST, 'name', 'password') ) {
	login();
} else {
	send_error();
}



?>
