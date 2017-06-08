<?php
// Heading
$_['heading_title']      = 'Яндекс.Деньги';

// Text
$_['text_yandexmoney']   = '<a onclick="window.open(\'https://money.yandex.ru\');"><img src="view/image/payment/yandexmoney.png" alt="Яндекс.Деньги" title="Яндекс.Деньги" /></a>';

$_['text_yes']       = 'Да';
$_['text_no']       = 'Нет';
$_['text_pay']       = 'Оплата';
$_['text_success']       = 'Настройки модуля обновлены!';
$_['text_all_zones']       = 'Все зоны';
$_['text_disabled']       = 'Выключено';
$_['text_enabled']       = 'Включено';
$_['text_need_update']       = "У вас неактуальная версия модуля. Вы можете <a target='_blank' href='https://github.com/yandex-money/yandex-money-cms-opencart/releases'>загрузить и установить</a> новую (%s)";

$_['yandexmoney_license']       = '<p>Любое использование Вами программы означает полное и безоговорочное принятие Вами условий лицензионного договора, размещенного по адресу  <a href="https://money.yandex.ru/doc.xml?id=527132"> https://money.yandex.ru/doc.xml?id=527132 </a>(далее – «Лицензионный договор»). Если Вы не принимаете условия Лицензионного договора в полном объёме, Вы не имеете права использовать программу в каких-либо целях.</p>';

$_['text_welcome1']       = '<p>Если у вас нет аккаунта в Яндекс-Деньги, то следует зарегистрироваться тут - <a href="https://money.yandex.ru/">https://money.yandex.ru/</a></p><p><b>ВАЖНО!</b> Вам нужно будет указать ссылку для приема HTTP уведомлений здесь - <a href="https://sp-money.yandex.ru/myservices/online.xml" target="_blank">https://sp-money.yandex.ru/myservices/online.xml</a>';

$_['text_welcome2']       = '<p>Для работы с модулем необходимо <a href="https://money.yandex.ru/joinups/">подключить магазин к Яндекc.Кассе</a>. После подключения вы получите параметры для приема платежей (идентификатор магазина — shopId и номер витрины — scid).</p>';

$_['text_params']       = 'Параметры для заполнения в личном кабинете';
$_['text_param_name']       = 'Название параметра';
$_['text_param_value']       = 'Значение';
$_['text_aviso1']       = 'Адрес приема HTTP уведомлений';
$_['text_aviso2']       = 'checkURL / avisoURL';
$_['title_default']       = 'Yandex Payment Solution (bank cards, e-money, and other payment methods)';


// Entry
$_['entry_version']         = 'Версия модуля:';
$_['entry_license']         = 'Лицензионный договор:';
$_['entry_testmode']         = 'Использовать в тестовом режиме?';
$_['entry_modes']         = 'Сценарий оплаты:';
$_['entry_mode1']         = 'Физическое лицо';
$_['entry_mode2']         = 'Юридическое лицо (выбор способа оплаты на стороне магазина)';
$_['entry_mode3']         = 'Юридическое лицо (выбор способа оплаты на стороне Яндекс.Кассы)';

$_['entry_methods']         = 'Укажите необходимые способы оплаты:';
$_['entry_method_ym']         = 'Оплата из кошелька в Яндекс.Деньгах';
$_['entry_method_cards']         = 'Оплата с произвольной банковской карты';
$_['entry_method_cash']         = 'Оплата наличными через кассы и терминалы';
$_['entry_method_mobile']         = 'Платеж со счета мобильного телефона';
$_['entry_method_wm']         = 'Оплата из кошелька в системе WebMoney';
$_['entry_method_ab']         = 'Оплата через Альфа-Клик';
$_['entry_method_sb']         = 'Оплата через Сбербанк: оплата по SMS или Сбербанк Онлайн';
$_['entry_method_ma']         = 'Оплата через MasterPass';
$_['entry_method_pb']         = 'Оплата через интернет-банк Промсвязьбанка';
$_['entry_method_qp']         = 'Оплата через доверительный платеж (Куппи.ру)';
$_['entry_method_qw']         = 'Оплата через QIWI Wallet';
$_['entry_method_mp']         = 'Оплата через мобильный терминал';
$_['entry_default_method']         = 'Способ оплаты по умолчанию';

$_['entry_page_mpos']         = 'Страница с инструкцией для платежей через мобильный терминал';
$_['entry_page_success']         = 'Пользовательская страница успеха';
$_['entry_page_fail']         = 'Пользовательская страница отказа';

$_['entry_shopid']         = 'Идентификатор вашего магазина в Яндекс.Деньгах (ShopID):';
$_['entry_scid']         = 'Идентификатор витрины вашего магазина в Яндекс.Деньгах (scid):';
$_['entry_title'] 	= 'Наименование платежного сервиса:';
$_['entry_total']         = 'Минимальная сумма:';
$_['entry_total2']         = 'Минимальная сумма заказа. Ниже этой суммы метод будет недоступен.';


$_['entry_password']         = 'Секретное слово (shopPassword) для обмена сообщениями:';
$_['entry_account']         = 'Номер кошелька Яндекс:';

$_['entry_order_status'] = 'Статус заказа после оплаты:';
$_['entry_notify'] = 'Уведомлять плательщика о смене статуса';
$_['entry_geo_zone']     = 'Географическая зона:';
$_['entry_status']       = 'Статус:';
$_['entry_sort_order']   = 'Порядок сортировки:';

// Error
$_['error_permission']   = 'У Вас нет прав для управления этим модулем!';
$_['error_empty_payment']   = 'Нужно выбрать хотя бы один метод оплаты!';
$_['error_ya_shopid']   = 'Укажите идентификатор магазина (shopId)';
$_['error_ya_scid']   = 'Укажите идентификатор витрины магазина (scid)';
$_['error_ya_shopPassword']   = 'Укажите секретное слово (shopPassword)';
$_['error_ya_wallet']   = 'Укажите номер кошелька получателя!';
$_['error_ya_appPassword']   = 'Укажите секретное слово из настроек кошелька Яндекс.Денег';

$_['setting_head'] = "Настройки";
$_['license'] = "Работая с модулем, вы автоматически соглашаетесь с <a href='https://money.yandex.ru/doc.xml?id=527132' target='_blank'>условиями его использования</a>.";
$_['version'] = "Версия модуля ";
$_['tab_kassa'] = "Яндекс.Касса";
$_['tab_money'] = "Яндекс.Деньги";
$_['forwork_kassa'] = "Для работы с модулем нужно подключить магазин к <a target=\"_blank\" href=\"https://kassa.yandex.ru/\">Яндекс.Кассе</a>.";
$_['kassa_enable'] = "Включить приём платежей через Яндекс.Кассу";
$_['testmode'] = " Тестовый режим";
$_['workmode'] = " Рабочий режим";
$_['checkUrl_help'] = "Скопируйте эту ссылку в поля Check URL и Aviso URL в настройках личного кабинета Яндекс.Кассы";
$_['successUrl'] = "Страницы с динамическими адресами";
$_['successUrl_help'] = "Включите «Использовать страницы успеха и ошибки с динамическими адресами» в настройках личного кабинета Яндекс.Кассы";
$_['lk_kassa'] = "Параметры из личного кабинета Яндекс.Кассы";
$_['shopid'] = "Идентификатор магазина";
$_['scid'] = "Номер витрины магазина";
$_['shopPassword'] = "Секретное слово";
$_['lk_help'] = "Shop ID, scid, ShopPassword можно посмотреть в <a href='https://kassa.yandex.ru/my' target='_blank'>личном кабинете</a> после подключения Яндекс.Кассы.";
$_['paymode_head'] = "Настройка сценария оплаты";
$_['paymode_label'] = "Сценарий оплаты";
$_['smartpay'] = "Выбор способа оплаты на стороне Яндекс.Кассы";
$_['shoppay'] = "Выбор способа оплаты на стороне магазина";
$_['paymode_help'] = "<a href='https://tech.yandex.ru/money/doc/payment-solution/payment-form/payment-form-docpage/' target='_blank'>Подробнее о сценариях оплаты</a>";
$_['option_help'] = "Отметьте способы оплаты, которые указаны в вашем договоре с Яндекс.Деньгами";
$_['forwork_money'] = "";
$_['enable_money'] = "Включить прием платежей в кошелек на Яндексе";
$_['redirectUrl_help'] = "Скопируйте эту ссылку в поле Redirect URL на <a href='https://money.yandex.ru/myservices/online.xml' target='_blank'>странице настройки уведомлений</a>.";
$_['account_head'] = "Настройки приема платежей";
$_['wallet'] = "Номер кошелька";
$_['password'] = "Секретное слово";
$_['account_help'] = "Cекретное слово нужно скопировать со <a href='https://money.yandex.ru/myservices/online.xml' target='_blank'>странице настройки уведомлений</a> на сайте Яндекс.Денег";
$_['option_wallet'] = "Способы оплаты";
$_['optDefault'] = "Способ оплаты по умолчанию";
$_['successPage_label'] = "Страница успеха платежа";
$_['page_standart'] = "Стандартная---";
$_['successPage_help'] = "Эту страницу увидит покупатель, когда оплатит заказ";
$_['failPage_label'] = "Страница отказа";
$_['page_standart'] = "Стандартная---";
$_['failPage_help'] = "Эту страницу увидит покупатель, если что-то пойдет не так: например, если ему не хватит денег на карте";
$_['successMP_label'] = "Страница успеха для способа «Оплата картой при доставке»";
$_['successMP_help'] = "Это страница с информацией о доставке. Укажите на ней, когда привезут товар и как его можно будет оплатить";
$_['namePay_label'] = "Название платежного сервиса";
$_['namePay_help'] = "Это название увидит пользователь";
$_['54lawmode_label'] = "Отправлять в Яндекс.Кассу данные для чеков (54-ФЗ)";
$_['54lawmode_help'] = "";
$_['54lawtaxtable_label'] = " передавать в Яндекс.Кассу как ";
$_['54lawtax_default_head'] = "Ставка по умолчанию";
$_['54lawtax_default_head_desc'] = "Ставка по умолчанию будет в чеке, если в карточке товара не указана другая ставка.";
$_['54lawtax_head'] = "Ставка в вашем магазине";
$_['54lawtax_head_desc'] = "Слева — ставка НДС в вашем магазине, справа — в Яндекс.Кассе. Пожалуйста, сопоставьте их.";
$_['feature_head'] = "Дополнительные настройки для администратора";
$_['debug_label'] = "Запись отладочной информации";
$_['off'] = "Отключена";
$_['on'] = "Включена";
$_['debug_help'] = "Настройку нужно будет поменять, только если попросят специалисты Яндекс.Денег";
$_['newStutus_label'] = "Статус заказа после оплаты";
$_['sordOrder_label'] = "Порядок сортировки";
$_['idZone_label'] = "Регион отображения";

