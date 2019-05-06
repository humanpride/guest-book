<?php
/**
* Выполняет запросы к БД и выводит форматированный результат
*/
class dbConnect
{
	private $mysql;

	function __construct(string $dbHost, string $dbUser, string $dbPass, string $dbName, string $charset)
	{
		$this->mysql = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
		$this->mysql->set_charset($charset);

		if ($this->mysql->connect_errno) {
			printf('Не удалось подключиться: %s'.PHP_EOL, $mysql->connect_error);
			exit();
		}
	}

	function query(string $query){
		$result = $this->mysql->query($query);
		return $result;
	}

	function save(string $username, string $email, string $homepage, string $text, array $tags, string $createdAt){

		$stringOfTags = implode(', ', $tags);

		if ($this->mysql->query("INSERT INTO notes (username, email, homepage, text, tags, createdAt) VALUES ('$username', '$email', '$homepage', '$text', '$stringOfTags', '$createdAt')"))
			return true;
		else
			return $this->mysql->error;
			
	}

	function printData(string $statement, int $total, int $pageNum, int $configNumRows, bool $printForSearch){

		$limit = $configNumRows; // сколько записей отображать на странице

		$pages = ceil($total / $limit); // общее количество страниц

		if ($pageNum == 0) // если записи выводятся впервые
			$pageNum = $pages; // выводить последнюю страницу(со свежими записями)

		$offset = ($pageNum - 1) * $limit; // смещение в выборке из БД

   		$stmt = $this->mysql->prepare($statement);

   		$stmt->bind_param('ii', $offset, $limit);

   		$stmt->execute();

   		$stmt->store_result();

   		$stmt->bind_result($username, $email, $homepage, $text, $tags, $createdAt);

   		if ($printForSearch){
   			// если был получен поисковый запрос, то извлекается название столбца и текст запроса из SQL выражения
   			// в последующем они используются для AJAX пагинации результатов поиска
   			preg_match("/WHERE\s(.+)\sLIKE\s'%(.+)%'/", $statement, $pregResult);
   		}

   		if ($stmt->num_rows == 0){
   			if ($printForSearch) {
   				// при отсутствии результатов поиска, поисковая строка всё равно отображается
   				echo "<form class='form-inline justify-content-end'>
	   					<label class='sr-only' for='searchRequest'>Search bar</label>
	   					<input type='text' name='searchText' class='form-control mb-2 mr-sm-2' id='searchRequest' placeholder='Поиск'>

	   					<label class='sr-only' for='searchType'>Choose column for search</label>
	   					
	   					<select name='searchSelect' class='form-control mb-2 mr-sm-2' id='searchType'>
						    <option value='username'".(($printForSearch && $pregResult[1] != 'username')? '':' selected').">Username</option>
						    <option value='email'".(($printForSearch && $pregResult[1] == 'email')? ' selected':'').">Email</option>
						    <option value='tags'".(($printForSearch && $pregResult[1] == 'tags')? ' selected':'').">Tag</option>
						    <option value='createdAt'".(($printForSearch && $pregResult[1] == 'createdAt')? ' selected':'').">CreatedAt</option>
					    </select>

	   					<button type='button' class='btn btn-primary mb-2 mr-sm-2' onclick='return ajaxSearch()'>Поиск</button>
	   					<button type='button' class='btn btn-primary mb-2' onclick='return ajaxLoadData()'>Сбросить</button>
   					</form>
   					<div class='alert alert-light' role='alert'>
   						К сожалению, по данному запросу записей не найдено
   					</div>";
   			}
   			else
   				echo "<div class='alert alert-light' role='alert'>
						Записей в книге нет. Вы можете <a href='#' onclick='return fakeData()'>запустить Faker</a>.
					</div>";
   		}
   		else{
   			echo "<form class='form-inline justify-content-end'>
   					<label class='sr-only' for='searchRequest'>Search bar</label>
   					<input type='text' name='searchText' class='form-control mb-2 mr-sm-2' id='searchRequest' placeholder='Поиск'>

   					<label class='sr-only' for='searchType'>Choose column for search</label>
   					
   					<select name='searchSelect' class='form-control mb-2 mr-sm-2' id='searchType'>
					    <option value='username'".(($printForSearch && $pregResult[1] != 'username')? '':' selected').">Username</option>
					    <option value='email'".(($printForSearch && $pregResult[1] == 'email')? ' selected':'').">Email</option>
					    <option value='tags'".(($printForSearch && $pregResult[1] == 'tags')? ' selected':'').">Tag</option>
					    <option value='createdAt'".(($printForSearch && $pregResult[1] == 'createdAt')? ' selected':'').">CreatedAt</option>
				    </select>

   					<button type='button' class='btn btn-primary mb-2 mr-sm-2' onclick='return ajaxSearch()'>Поиск</button>";

   			if ($printForSearch)
   				echo "<button type='button' class='btn btn-primary mb-2' onclick='return ajaxLoadData()'>Сбросить</button>";

   			echo "</form>";

   			echo "<table class='table table-sm'>
   					<thead>
   						<tr>
   							<th scope='col'>Username</th>
   							<th scope='col'>Email</th>
   							<th scope='col'>Homepage</th>
   							<th scope='col' class='table-text'>Text</th>
   							<th scope='col'>Tags</th>
   							<th scope='col' class='table-createdat'>CreatedAt</th>
   						</tr>
   					</thead>
   					<tbody>";
   			while ($stmt->fetch())
   				echo "<tr>
   						<td>$username</td>
   						<td>$email</td>
   						<td>$homepage</td>
   						<td>$text</td>
   						<td>$tags</td>
   						<td>$createdAt</td>
   					</tr>";
   			echo "</tbody></table>";

   			// вывод пагинации
   			if ($pages > 1) {
   				echo "<nav>
   						<ul class='pagination justify-content-end' id='pag'>
   							<li class='page-item";
   				
   				if ($pageNum == 1)
   					echo " disabled'>
   								<span class='page-link'>Предыдущая</span>";
				elseif ($printForSearch)
					echo "'>
								<a class='page-link' href='#' onclick='return ajaxSearch(\"{$pregResult[1]}\",\"{$pregResult[2]}\",".($pageNum-1).")'>Предыдущая</a>";
				else
					echo "'>
								<a class='page-link' href='#' onclick='return ajaxLoadData(".($pageNum-1).")'>Предыдущая</a>";
				echo "		</li>";

				function printPagination(int $num, int $pageNum, bool $printForSearch){
					echo "	<li class='page-item";
					if ($num == $pageNum)
						echo " active'>
								<span class='page-link'>
									$num
									<span class='sr-only'>(current)</span>
								</span>";
					elseif ($printForSearch)
						echo "'>
								<a class='page-link' href='#' onclick='return ajaxSearch(\"{$pregResult[1]}\",\"{$pregResult[2]}\",$num)'>$num</a>";
					else
						echo "'>
								<a class='page-link' href='#' onclick='return ajaxLoadData($num)'>$num</a>";
					echo "	</li>";
				}

				// сколько отображается страниц пагинации
				$left = 4; // от текущей слева
				$right = 5; // от текущей справа

				if ($pageNum > $left && $pageNum < ($pages-$right))
					for ($i = $pageNum - $left; $i <= $pageNum + $right; $i++)
						printPagination($i, $pageNum, $printForSearch);
				elseif ($pageNum <= $left){
					$slice = 1 + $left - $pageNum; // кол-во элементов, которые не будут показаны слева и будут добавлены справа для постоянного количества страниц
					for ($i = 1; $i <= $pageNum + ($right + $slice); $i++)
						printPagination($i, $pageNum, $printForSearch);
				}
				else{ // когда pageNum >= pages - right (элемент около правой границы)
					$slice = $right - ($pages - $pageNum);
					for ($i = $pageNum - ($left + $slice); $i <= $pages; $i++)
						printPagination($i, $pageNum, $printForSearch);
				}

				echo "		<li class='page-item";
				if ($pageNum == $pages)
   					echo " disabled'>
   								<span class='page-link'>Следующая</span>";
				elseif ($printForSearch)
					echo "'>
								<a class='page-link' href='#' onclick='return ajaxSearch(\"{$pregResult[1]}\",\"{$pregResult[2]}\",".($pageNum+1).")'>Следующая</a>";
				else
					echo "'>
								<a class='page-link' href='#' onclick='return ajaxLoadData(".($pageNum+1).")'>Следующая</a>";
				echo "		</li>
						</ul>
					</nav>";
			}
		}
   	}

	function close(){
		$this->mysql->close();
	}
}

?>