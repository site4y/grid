# pdgrid
PHP Data Grid Widget using Bootstrap, Jquery and Zend_Db

Компонент грид(таблица) предназначен для админки, для вывода списка элементов
в табличном виде с функциями сортировки, фильтрации, постраничной навигации,
ссылками для создания, редактирования и удаления элементов

# Использование:
Создайте грид, передав в конструктор необходимые параметры
(см. ниже **Список параметров**)
```
use pdima88\pdgrid\Grid;
$grid = new Grid( <ассоциативный массив с параметрами> );
```
и выведите грид в нужном месте страницы:
```
echo $grid->render();
```
в случае ajax запроса вызовите метод ajax, который вернет все необходимые данные и завершит выполнение
```
$grid->ajax();
```
# Список параметров:
___Данные:___
- data - Массив данных (уже подготовленных) - в этом случае сортировка и фильтрация не будет выполняться
       гридом, вы должны передать отсортированный и отфильтрованный массив данных.
       Грид выполняет только постраничное разделение.

или:
- select - Zend_Db_Select запрос, из которого будет сформированы sql и sqlcount параметры

или:
- sql - Запрос для получения элементов
- sqlcount - Запрос для получения количества элементов, необязателен, если не указан - берется запрос sql

       **!! ВАЖНО** : В случае использование фильтров, укажите в запросах подстановочный параметр {where},
       вместо которого будут вставлено условие фильтрации
       Не используйте ORDER (сортировку) в запросах, т.к. грид сам подставляет
       в конец запроса предложение ORDER - это вызовет ошибку.

___URL для получения данных и кнопок действий:___

- url - Базовый url страницы, где выводится грид, он будет использоваться для ссылок постраничной навигации,
        сортировки и фильтрации, не должен содержать параметров page, sort и filter, т.к. они будут добавлены
        гридом.
- ajax - Базовый url для AJAX запросов
- add - Ссылка на создание элемента
- edit - Ссылка на редактирование элемента
- delete - Ссылка на удаление элемента
- ajax-delete - Ссылка на удаление (AJAX)
       В ссылках действий могут быть использованы подстановочные параметры:

       - {id} - ID элемента (при редактировании или удалении)
       - {returnUrl} - URL адрес текущей страницы с гридом для возврата

___Параметры отображения:___
- paging - Количество элементов на странице, по умолчанию 10. Если указано 0 или false, то
       выводятся все элементы на одной странице, пагинация отключена
- rownum - Нужно ли выводить номера строк слева от каждой строки, по умолчанию: да
- footer - Нужно ли выводить нижнюю строку с информацией
- columns - Описание колонок таблицы, ассоциативный массив, ключи - названия столбцов запроса,
        значения - ассоциативный массив с параметрами колонки (см. **Параметры столбца**)
- multisort - Возможна ли сортировка одновременно по нескольким столбцам
- sort - Сортировка по умолчанию, здесь также можно указать сортировку по несортируемым полям,
       которая будет действовать в любом случае.
  ```
   'sort' => [
        'name' => 'asc|desc'
     ]
  ```
- group - Группировка строк по значению в столбце, по умолчанию false (нет группировки)
        Если требуется группировка, следует указать массив
   ```
   [
      'column' => '<название столбца>',
      'format' => <Формат вывода ячейки заголовка группы> - это может быть строка с именем функции,
                     просто строка с подставляемыми значениями или массив
                      (см. описание format в параметрах столбца)
      'edit' => URL ссылки редактирования, необязательно
      'delete' => URL ссылки на удаление группы, необязательно
      'ajax-delete' => URL ссылки на удаление группы (AJAX), необязательно
   ]
   ```
- actions - дополнительные кнопки операций (отображается в последней колонке)

# Параметры столбца:
- title - Название колонки, отображается в заголовке таблицы
- width - Ширина, число в пикселях (для изображений - макс.ширина изображения),
       по умолчанию - автоматическая ширина по содержимому
- style - CSS стиль, применяемый к ячейкам столбца (кроме заголовка), может быть задан строкой
       или массивом
- nowrap - Вывод значения в одну строку (запретить перенос строки)
- align - Горизонтальное выравнивание: left, right или center (по умолчанию: left)
- format - Определяет то, как выводится значение в этой колонке, по умолчанию - обычный вывод (см. **Форматы вывода**)
- href - URL ссылки, можно использовать параметры {*имя столбца*} для подстановки значений из запроса
- hrefTarget - если используется href, указывает цель ссылки
- sort - Возможна ли сортировка по этому столбцу (true|false, по умолчанию false),
       если сортировка для данного столбца выполняется по другому полю базы данных,
       отличному от указанного в ключе, укажите здесь название столбца в SQL запросе.
       Также можно указать массив значений, если требуется сортировка по нескольким полям
- filter - Тип фильтра по данному столбцу (по умолчанию - нет фильтра), см. **Типы фильтров**

# Форматы вывода:
- img - выводит изображение, из указанной ссылки
- checkbox - выводит Флажок вкл./выкл.
- datetime - выводит отформатированную Дату/время
- *массив* - будет выведено значение по ключу массива
- *функция* - если указано имя функции или метода, будет выведен результат ее вызова
       Функция принимает первым параметром значение столбца

В остальных случаях значение будет интерпретироваться как строка формата
для функции sprintf. В строке формата вы также можете использовать параметры
{*имя столбца*} для подстановки значений из запроса

<br>
___Пока не реализовано:___
- date (Дата)
- time (Время ЧЧ:ММ:СС)
- shorttime (Время ЧЧ:ММ)
- dateperiod (Диапазон дат)
- period (Диапазон даты/времени)
- timeperiod (Диапазон времени)

# Типы фильтров:
- text - Поиск по подстроке
- select - Выбор одного из нескольких значений, список берется из свойства format, которое должно быть массивом
- dateRange - Фильтр по диапазону дат

Все фильтры представлены отдельными классами, наследованными от базового Filter
