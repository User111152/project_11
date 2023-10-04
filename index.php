<?php
header('Content-Type: text/html; charset=UTF-8'); 
mb_internal_encoding('UTF-8'); 
mb_http_output('UTF-8');
mb_http_input('UTF-8'); 
mb_regex_encoding('UTF-8'); 
$jsonFile = 'array.json';
$jsonData = file_get_contents($jsonFile);
$users = json_decode($jsonData, true); 
?>
<!doctype html>
<html>
<head>
<title>index</title>
<style>
.error {
color: red;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>index</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<form method="POST" class="transparent" onsubmit="return validateForm();">
<div class="form-inner">
Ім'я <input id="name" name="name" type="text" class="form-control"><br>
Прізвище <input id="surname" name="surname" type="text" class="form-control"><br>
email <input id="email" name="email" type="text" class="form-control"> <span id="emailError" class="error" class="form-control"></span> <br>
Пароль <input id="password" name="password" type="password" class="form-control"><br>
Повторення паролю <input id="passwordtwo" name="passwordtwo" type="password" class="form-control"><br><span id="passwordError" class="error" class="form-control"></span><br>
<input name="submit" type="submit" value="Войти">
</div>
</form>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordtwo = $_POST['passwordtwo'];
    $response = array();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['error'] = 'Неправильний формат email';
    } elseif ($password !== $passwordtwo) {
    $response['error'] = 'Паролі не співпадають';
    } else {
    $response['message'] = 'Реєстрація успішна';
    }
    echo json_encode($response);
    exit; 
    }
?>
<script>
   function validateForm() {
    var name = document.getElementById("name").value;
    var surname = document.getElementById("surname").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var passwordtwo = document.getElementById("passwordtwo").value;
    var emailError = document.getElementById("emailError");
    var passwordError = document.getElementById("passwordError");
    var valid = true;
    if (!email.includes("@")) {
        emailError.textContent = "Email повинен містити @";
        valid = false;
    } else {
        emailError.textContent = "";
    }
    if (password !== passwordtwo) {
        passwordError.textContent = "Паролі не співпадають";
        valid = false;
    } else {
        passwordError.textContent = "";
    }
    return valid;
}
    validateForm();
</script>
<?php
	$userExists = false;  
    foreach ($users as $user) {
    if ($user['email'] === $_POST['email']) {
        $userExists = true;
		$logMessage = "Емейл {$_POST['email']} " . ($userExists ? 'існує в масиві користувачів.' : 'знайдено в масиві користувачів');
    file_put_contents('log.txt', $logMessage . PHP_EOL, FILE_APPEND);
    break; 
	}
	else{
    $logMessage = "Імейл {$_POST['email']} " . ($userExists ? 'не існує в масиві користувачів.' : 'не знайдено в масиві користувачів, додано');
    file_put_contents('log.txt', $logMessage . PHP_EOL, FILE_APPEND);
	}
    if (!$userExists) {
    $newUser = array(
    'id' => uniqid(),  
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'password' => $_POST['password']
    );
    $users[] = $newUser;
	$jsonData = json_encode($users);
    file_put_contents('array.json', $jsonData);
    }
    }
    ?>
    <div class="container mt-5">
    <h1>Список користувачів</h1>
    <table class="table table-bordered">
    <thead>
    <tr>
    <th>ID</th>
    <th>Ім'я</th>
    <th>Email</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
    <tr>
    <td><?php echo htmlspecialchars($user['id']); ?></td>
    <td><?php echo htmlspecialchars($user['name']); ?></td>
    <td><?php echo htmlspecialchars($user['email']); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
    fetch('server.php')
    .then(response => response.json())
    .then(users => {
    function displayUsers(users) {
    var table = document.createElement('table');
    var headerRow = table.insertRow();
    headerRow.insertCell().textContent = 'ID';
    headerRow.insertCell().textContent = 'Ім\'я';
    headerRow.insertCell().textContent = 'Прізвище';
    headerRow.insertCell().textContent = 'Email';
    users.forEach(user => {
    var row = table.insertRow();
    row.insertCell().textContent = user.id;
    row.insertCell().textContent = user.name;
    row.insertCell().textContent = user.surname;
    row.insertCell().textContent = user.email;
    });
    document.body.appendChild(table);
    }
    displayUsers(users);
    })
    .catch(error => {
    console.error('Помилка під час отримання користувачів:', error);
    });
    </script>
    </body>
    </html>
