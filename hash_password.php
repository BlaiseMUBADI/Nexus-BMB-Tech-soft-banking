<?php
// Script pour générer un hash de mot de passe compatible Laravel (bcrypt)
// Usage : php hash_password.php "motdepasse"

if ($argc < 2) {
    echo "Usage: php hash_password.php \"motdepasse\"\n";
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "Hash Laravel :\n" . $hash . "\n";
