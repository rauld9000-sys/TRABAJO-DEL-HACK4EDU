
<?php

$curl = curl_init(
    'https://generativelanguage.googleapis.com'
);

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 20
]);

curl_exec($curl);

$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);

curl_close($curl);

echo "HTTP: " . $codigo . PHP_EOL;
echo "Error: " . ($error ?: 'Ninguno') . PHP_EOL;
