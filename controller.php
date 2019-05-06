<?php

	require_once "model.php";
	$config = parse_ini_file("config.ini");

	switch ($_REQUEST['requestType']) {
		case 'printData':
			// вывод записей с пагинацией
			$dbconnect = new dbConnect($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName'], $config['charset']);
		
			$total = $dbconnect->query('SELECT COUNT(*) FROM notes')->fetch_array()[0]; // общее количество записей

			$dbconnect->printData("SELECT username, email, homepage, text, tags, createdAt FROM notes ORDER BY createdAt LIMIT ?,?", $total, filter_var($_REQUEST['page'], FILTER_SANITIZE_NUMBER_INT), $config['numRows'], false);
			$dbconnect->close();
			break;

		case 'saveData':
			// добавление записи в БД
			$username = filter_var(trim($_REQUEST['username']), FILTER_SANITIZE_STRING);
			$email = filter_var(trim($_REQUEST['email']), FILTER_SANITIZE_EMAIL);
			$homepage = filter_var(trim($_REQUEST['homepage']), FILTER_SANITIZE_URL);
			$text = strip_tags(trim($_REQUEST['text']));

			$tags = explode(',', trim($_REQUEST['tags']));
			for ($i=0; $i < count($tags); $i++) { 
				$tags[$i] = filter_var(trim($tags[$i]), FILTER_SANITIZE_STRING);
			}

			$createdAt = filter_var(trim($_REQUEST['createdAt']), FILTER_SANITIZE_STRING);

			// создаётся объект для сохранения полученных данных в базу
			$dbconnect = new dbConnect($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName'], $config['charset']);
			if($result = $dbconnect->save($username, $email, $homepage, $text, $tags, $createdAt))
				echo "<div class='alert alert-success'>Ваша запись успешно добавлена</div>";
			else
				echo "<div class='alert alert-danger'>При добавлении записи возникла ошибка: ".$result."</div>";
			$dbconnect->close();
			break;

		case 'search':
			// поиск информации в БД и вывод результата
			$searchType = filter_var(trim($_REQUEST['searchType']), FILTER_SANITIZE_STRING);
			$searchRequest = filter_var(trim($_REQUEST['searchRequest']), FILTER_SANITIZE_STRING);
			
			$dbconnect = new dbConnect($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName'], $config['charset']);

			$total = $dbconnect->query("SELECT COUNT(*) FROM notes WHERE $searchType LIKE '%$searchRequest%'")->fetch_array()[0]; // общее количество записей

			$dbconnect->printData("SELECT username,email,homepage,text,tags,createdAt FROM notes WHERE $searchType LIKE '%$searchRequest%' ORDER BY createdAt LIMIT ?,?", $total, filter_var($_REQUEST['page'], FILTER_SANITIZE_NUMBER_INT), $config['numRows'], true);
			$dbconnect->close();
			break;
		
		default:
			echo "Неизвестный запрос";
			break;
	}