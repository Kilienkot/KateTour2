<?php
    $pdo = new PDO('mysql:host=localhost;dbname=katetour;port=3306', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>