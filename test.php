test
<script>
    
    var preview = document.createElement('div');
    preview.classList.add('someClass');
    preview.innerHTML = 'some text';
    var email = document.createElement('input');
    document.body.appendChild(preview);
    var value = document.getElementById(emailID).value;
</script>


<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$dbname = 'test';
$username = 'root';
$password = 'VtVgfhfif354';
$dsn = "mysql:host=localhost;dbname={$dbname};charset=utf8";
$opt = array(
	\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
	\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
	\PDO::ATTR_EMULATE_PREPARES => false,
	\PDO::MYSQL_ATTR_FOUND_ROWS => true
);
$pdo = new \PDO($dsn, $username, $password, $opt);

/*
$loader = new \BeeJee\Input\ImageLoaderBase64(
    array('image/jpeg', 'image/png', 'image/gif'),
    array('jpg', 'jpeg', 'png', 'gif'));
if (isset($_POST['image']) AND isset($_POST['imageblob'])) {
    $check = $loader->checkImage($_POST['imageblob'], $_POST['image'], 320, 240);
    if ($check === true) {
        $saved = $loader->saveFile($_POST['imageblob'], 'png', 'C:/Temp');
        echo "file saved at $saved";
    } else echo 'WRONG data.';
} else echo 'No data.';
*/



	