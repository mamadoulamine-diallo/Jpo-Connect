<?php
$password = 'motdepasse';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Mot de passe haché : " . $hash;
