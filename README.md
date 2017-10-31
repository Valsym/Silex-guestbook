# Silex-guestbook
Silex test project

Задача:
Необходимо разработать гостевую книгу. Сайт должен состоять из 3 страниц:<br />
1. Просмотр списка отзывов.<br />
2. Просмотр конкретного отзыва.<br />
3. Создание нового отзыва.<br /><br />
Список полей сущности:<br />
1. Дата публикации.<br />
2. Имя автора<br />
3. IP адрес автора<br />
4. Текст отзыва<br />
5. Кол-во лайков.<br /><br />
Возможности:<br />
1. На странице со списком отзывов пользователь может:<br />
A. задать сортировку по дате публикации и кол-ву лайков в прямом и обратном порядках.<br />
B. голосовать/ставить лайк за понравившийся отзыв. Дважды голосовать за один и тот же
отзыв с одного и того же IP адреса нельзя.<br />
2. На странице просмотра конкретного отзыва должны быть ссылки перехода на следующий
и предыдущий отзыв.<br />
3. На странице создания отзыва система не должна допускать отзывы с любыми html тегами.<br /><br />
Требования:<br />
1. Минифреймворк Silex. <br />
2. Сайт должен быть спроектирован с использованием MVC паттерна. Структура файлов и
папок - на свое усмотрение. Использование шаблонизатора, например, таких как Twig,
необязательно.<br />
3. Фронтэнд часть должна быть реализована с использованием Bootstrap и простейшего
стандартного шаблона.<br /><br />
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++<br />
Замечания по решению:<br />
Можно дир-ю /vendor  не скачивать, а запустить composer install, в папке, где лежит файл composer.json, но тогда не забудьте скачать туда QuickForm2 отсюда: http://pear.php.net/package/HTML_QuickForm2/redirected или мою копию из /vendor.

Далее все как обычно: запускаем Apache и mySQL, эскпортируйте базу (дамп базы данных прилагается).<br />
Запуск приложения на локальном сервере - в браузере набрать localhost/Silex-guestbook

"Вживую" работу приложения можно посмотреть тут: http://valsy.ru/guestbook/<br />
Полный рефакторинг кода, с учетом пожелания работодателя добавить Модель, см. в ветке v2:
