#yandexmoney-opencart

Модуль оплаты yandexmoney-opencart необходим для интеграции с сервисом [Яндекс.Касса](http://kassa.yandex.ru/) на базе CMS OpenCart. 

Существует два платежных сценария: с выбором способа оплаты на сайте магазина и на стороне Яндекс.Кассы. 
> **Выбор способа оплаты на сайте магазина** 
> В этом случае в настройках модуля отмечаются все способы оплаты, доступные этому магазину и плательщик выбирает нужный ему метод из списка на сайте магазина. Этот сценарий доступен всем магазинам по умолчанию.

> **Выбор способа оплаты на стороне Яндекс.Кассы**
> Все доступные магазину способы появятся на стороне Яндекс.Кассы сами. Новые способы оплаты будут добавляться автоматически. Если вам нужен этот сценарий, напишите на merchants@yamoney.ru (понадобятся дополнительные настройки на стороне Яндекс.Кассы).

Доступные платежные методы, если вы работаете как юридические лицо:
* **Банковские карты** -  Visa (включая Electron), MasterCard и Maestro любого банка мира
* **Электронные деньги** - Яндекс.Деньги, WebMoney и QIWI Wallet
* **Наличные** - [Более 170 тысяч пунктов](https://money.yandex.ru/pay/doc.xml?id=526209) оплаты по России
* **Баланс телефона** - Билайн, МегаФон и МТС
* **Интернет банкинг** - Альфа-Клик, Сбербанк Онлайн, MasterPass и Промсвязьбанк
* **Кредитование** - Доверительный платеж (Куппи.ру)

###Требования к CMS OpenCart:
* версия 1.5.4 - 1.5.6;

###Совместимость с CMS Opencart 2.1.x - 2.2.x
Данный модуль совместим только с CMS Opencart версии 1.5.x. Для CMS Opencart 2.1.x - 2.2.x нами разработан универсальный модуль, который можно [загрузить здесь](https://github.com/yandex-money/yandex-money-cms-opencart2).

###Совместимость с CMS Opencart 2.3.x
Для CMS Opencart 2.3.x нами разработан универсальный модуль, который можно [загрузить здесь](https://github.com/yandex-money/yandex-money-cms-opencart2/tree/oc23x).

###Установка модуля
Для установки данного модуля необходимо распаковать [архив](https://github.com/yandex-money/yandex-money-cms-opencart/archive/master.zip) и поместить папки `admin`, `catalog` в корень вашего сайта!
Пожалуйста, обязательно делайте бекапы!

###Лицензионный договор.
Любое использование Вами программы означает полное и безоговорочное принятие Вами условий лицензионного договора, размещенного по адресу https://money.yandex.ru/doc.xml?id=527132 (далее – «Лицензионный договор»). 
Если Вы не принимаете условия Лицензионного договора в полном объёме, Вы не имеете права использовать программу в каких-либо целях.

###Нашли ошибку или у вас есть предложение по улучшению модуля?
Пишите нам cms@yamoney.ru
При обращении необходимо:
* Указать наименование CMS и компонента магазина, а также их версии
* Указать версию платежного модуля (доступна на странице настроек модуля)
* Описать проблему или предложение
* Приложить снимок экрана (для большей информативности)