function ajaxLoadData(pageNumb = 0 ){ // если информация выводится первый раз или записей для пагинации недостаточно page = 0
	// запрос на загрузку записей из БД
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200){
			// вывод информации на страницу
			document.getElementById('table').innerHTML = this.responseText;
		}
	}
	
	xmlhttp.open('POST', 'controller.php', true);
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xmlhttp.send('requestType=printData&page='+pageNumb);
}

function ajaxSaveData(){
	// запрос на сохранение данных формы в БД
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200){
			var feedbackAlert = document.getElementById('feedbackAlert'); // div с информацией о записи данных в БД
			feedbackAlert.innerHTML = this.responseText;
			setTimeout(function(){ // через 4 секунды div исчезает
				feedbackAlert.innerHTML = '';
			}, 4000);
			ajaxLoadData();
		}
	}
	xmlhttp.open('POST', 'controller.php', true);
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	var params = 'requestType=saveData&username='+document.getElementById('uname').value+
				'&email='+document.getElementById('email').value+
				'&homepage='+document.getElementById('url').value+
				'&text='+document.getElementById('text').value+
				'&tags='+document.getElementById('tags').value;
	
	var date = new Date();

	var month = date.getMonth() + 1;
	var stringMonth = ''+month;
	if (month < 10)
		stringMonth = '0'+stringMonth;

	var day = date.getDate();
	var stringDate = ''+day;
	if (day < 10)
		stringDate = '0'+stringDate;

	var dateString = date.getFullYear()+'-'+stringMonth+'-'+stringDate+' '+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();

	params = params+'&createdAt='+dateString;

	xmlhttp.send(params);

	// очистка всех полей и стилей для них
	document.getElementById('uname').value = '';
	clearValidationFeedback(document.getElementById('uname')); // функция из validate.js
	document.getElementById('email').value = '';
	clearValidationFeedback(document.getElementById('email'));
	document.getElementById('url').value = '';
	clearValidationFeedback(document.getElementById('url'));
	document.getElementById('text').value = '';
	clearValidationFeedback(document.getElementById('text'));
	document.getElementById('tags').value = '';
	clearFlags(); // validate.js
	document.getElementById('submitButton').setAttribute('disabled', true);

}

function ajaxSearch(searchType = null, searchRequest = null, pageNumb = 0){
	// поисковый запрос
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200){
			// вывод информации на страницу
			document.getElementById('table').innerHTML = this.responseText;
		}
	}
	
	xmlhttp.open('POST', 'controller.php', true);
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

	if (searchType == null || searchRequest == null){
		searchType = document.getElementById('searchType').value;
		searchRequest = document.getElementById('searchRequest').value;
	}

	xmlhttp.send('requestType=search&searchType='+searchType+'&searchRequest='+searchRequest+'&page='+pageNumb);
}

function fakeData(){
	// запуск Faker
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200){
			setTimeout(ajaxLoadData(), 1000);
		}
	}
	
	xmlhttp.open('GET', 'faker.php', true);

	xmlhttp.send();
}

document.getElementById('submitButton').onclick = ajaxSaveData;
ajaxLoadData(); // первый запуск функции для отображения имеющихся записей