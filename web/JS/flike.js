function flike() {
	
	const root = document.getElementById('reviews');

	root.addEventListener('click', async (e) => {
		if (!e.target) {
			console.log('Error! e.target=',e.target.innerHTML);
		  return;
		}

		const btn = e.target;

		let like = btn.value;
		let id = btn.getAttribute('data-commentid');
		let ip = btn.getAttribute('data-ip');
		
		if (!id || !ip || like == undefined) {//случай повторного нажатия на не активную кнопку
			e.stopPropagation() // останавливает всплытие
			return;
		}
		
		const url = new URL('/Silex-guestbook/web/like/'+id+'/'+ip+'/'+like, window.location.href);
		const response = await fetch(url);
		const res = await response.json();

		// создаем массив уведомлений
		let Locale = {
			alike: 'Спасибо, ваш лайк учтен!',
			atime: 'Вы уже оставили лайк к этому отзыву.',
			ups: ' Вы не можете лайкать свой отзыв.',
			aerr: 'Произошла неизвестная ошибка.'
		};
		
		const status = document.getElementById('status');
		if (res == 1) {// выводим статус-сообщение об успешно добавленном лайке
			btn.innerHTML = Number(like) + 1;	
			
			// для ф-ии закрытия вручную: можно добавить класс data-dismiss="alert" и &times; перед </button>			
			status.innerHTML = 
			 '<div class="alert alert-success fixed"><button type="button" class="close" ></button><strong>'+Locale.alike+'</strong></div>';
			setTimeout(function() { status.innerHTML = '<span id="status"></span>'; }, 4000);
		} else if (res == 2) {// выводим статус-сообщение о том, что пользователь уже голосовал
			status.innerHTML = 
			 '<div class="alert alert-success fixed"><button type="button" class="close" ></button><strong>'+Locale.atime+'</strong></div>';
			setTimeout(function() { status.innerHTML = '<span id="status"></span>'; }, 4000);
		} else if (res == 3) {// выводим статус-сообщение о том, что автор отзыва голосовать за него не может
			status.innerHTML = 
			 '<div class="alert alert-success fixed"><button type="button" class="close" ></button><strong>'+Locale.ups+'</strong></div>';
			setTimeout(function() { status.innerHTML = '<span id="status"></span>'; }, 4000);
		} else {// если во время запроса произошел какой-либо сбой, обрабатываем ошибку
			status.innerHTML = 
			 '<div class="alert alert-success fixed"><button type="button" class="close" data-dismiss="alert">&times;</button>  <strong>'+Locale.aerr+'</strong></div>';
			//setTimeout(function() { status.innerHTML = '<span id="status"></span>'; }, 4000);
		}
		
		btn.classList.add('class', 'disabled');//Деактивируем кнопку

	});
}
