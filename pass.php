<?php
// Password to be used for the user
$username = 'user1';
$password = 'password1';
 
// Encrypt password
$encrypted_password = password_hash($password, PASSWORD_BCRYPT);
 
// Print line to be added to .htpasswd file
echo $username . ':' . $encrypted_password;
?>
