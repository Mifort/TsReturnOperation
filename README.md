# TsReturnOperation

Приложение похоже отменяет операцию продажи или перемещения товара или что-то подобное
1.Формируем список  потенциальных получателей писем(продавцов, клиентов, создателей, экспертов)
2.В зависимости от типа сообщения получаем какие-то различия формируем данные для отправки адресатам
3.Получаем email сотрудников из настроек. я так понимаю здесь какая-то выборка сотрудников,
        которым разрешено отправлять сообщения в зависимости  от $resellerId
4.Отправляем клиентское уведомление, только если произошла смена статуса. Также здесь шлем письмо ответственным  сотрудник
5.Отправляем SMS , если есть телефон


Сразу же все классы вынес в отдельные файлы.
В классе Contractor при создания экземпляра заменил self на static. Это позволяет создавать экземпляр дочернего класса , а не родительского. 
Для клиента создал по аналогии свой класс  и наследовал от Contractor. 
Тут можно было делать конечно и обычные классы , без self и static. Но оставил пока так.
Месенджер переделан по паттерну "делегирование".
В этот же раздел и перенес  функции (теперь они стали методами).
Свойства в классах переделал на protected. Это вписывается в концепцию ООП - инкапсуляцию.
