<?php

$key_value = "123321";
$plain_text = "YqvjywySafVDSDej";
$encrypted_text = mcrypt_ecb(MCRYPT_DES, $key_value, $plain_text, MCRYPT_ENCRYPT);


$decrypted_text = mcrypt_ecb(MCRYPT_DES, $key_value, $encrypted_text, MCRYPT_DECRYPT);
//$host="localhost";
//$username="editor";
$password=$decrypted_text;
echo $password;
//$db_name="editor";
?>