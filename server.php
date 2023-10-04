<?php
header('Content-Type: application/json');
$jsonFile = 'array.json';
$jsonData = file_get_contents($jsonFile);
$users = json_decode($jsonData, true);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordtwo = $_POST['passwordtwo'];
    $response = array();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['error'] = 'Неправильний формат email';
    if ($password !== $passwordtwo) {
    $response['error'] = 'Паролі не співпадають';
    }
    } else if ($password !== $passwordtwo) {
    $response['error'] = 'Паролі не співпадають.';
    } else {
    $userExists = false;
	foreach ($users as $user) {
    if ($user['email'] === $_POST['email']) {
    $userExists = true;
    $logMessage = "Емейл {$_POST['email']} " . ($userExists ? 'існує в масиві користувачів.' : 'знайдено в масиві користувачів.');
     file_put_contents('log.txt', $logMessage . PHP_EOL, FILE_APPEND);
     break;
     }
     }
	 if (!$userExists) {
     $newUser = array(
     'id' => uniqid(),
     'name' => $_POST['name'],
     'surname' => $_POST['surname'],
     'email' => $_POST['email'],
     'password' => $_POST['password']
     );
     $users[] = $newUser;
     $jsonData = json_encode($users);
     file_put_contents('array.json', $jsonData);
     echo '<script>alert("Реєстрація успішна!");</script>';
     } else {
     echo '<script>alert("Такий користувач вже існує!");</script>';
     }
     }
     echo json_encode($response);}
     ?>







