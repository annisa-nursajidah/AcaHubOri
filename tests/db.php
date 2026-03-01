<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
foreach($pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN) as $db) echo $db . PHP_EOL;
