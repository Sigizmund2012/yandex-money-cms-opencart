<?php

/**
 * Class ControllerPaymentYandexMoney
 *
 * @property-read Language $language
 */
class ControllerPaymentYandexMoney extends Controller
{
    private function payment($order_info, $child = false)
    {
        $this->language->load('payment/yandexmoney');

        $yandexMoney = new YandexMoneyObj($this->config->get('ya_mode'));
        $yandexMoney->org_mode = (bool)($this->config->get('ya_kassamode') == '1');
        $yandexMoney->password = ($yandexMoney->org_mode) ? $this->config->get('ya_shopPassword') : $this->config->get('ya_appPassword');
        $yandexMoney->shopid = $this->config->get('ya_shopid');
        $yandexMoney->test_mode = (bool)($this->config->get('ya_workmode') != '1');
        $yandexMoney->epl = (bool)($this->config->get('ya_kassamode') == '1' && $this->config->get('ya_paymode') == 'kassa');

        if (isset($order_info['email'])) {
            $this->data['email'] = $order_info['email'];
        }
        if (isset($order_info['telephone'])) {
            $this->data['phone'] = $order_info['telephone'];
        }

        $this->data['mode'] = $yandexMoney->getMode();
        $this->data['cmsname'] = ($child) ? 'opencart-extracall' : 'opencart';
        $this->data['sum'] = $this->currency->format(
            $order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false
        );
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['comment'] = $order_info['comment'];
        $this->data['order_id'] = $order_info['order_id'];
        $this->data['action'] = $yandexMoney->getFormUrl();
        if ($yandexMoney->getMode() != YandexMoneyObj::MODE_BILLING) {
            $this->data['epl'] = $yandexMoney->epl;
            $this->data['org_mode'] = $yandexMoney->org_mode;

            $this->data['account'] = $this->config->get('ya_wallet');
            $this->data['shop_id'] = $this->config->get('ya_shopid');
            $this->data['scid'] = $this->config->get('ya_scid');

            $this->prepare_54law($order_info, $this->data);

            $this->data['customerNumber'] = trim($order_info['order_id'] . ' ' . $order_info['email']);

            $this->data['shopSuccessURL'] = (!$this->config->get('ya_pageSuccess')) ? $this->url->link(
                'checkout/success', '', 'SSL'
            ) : $this->url->link('information/information', 'information_id=' . $this->config->get('ya_pageSuccess'));
            $this->data['shopFailURL'] = (!$this->config->get('ya_pageFail')) ? $this->url->link(
                'checkout/failure', '', 'SSL'
            ) : $this->url->link('information/information', 'information_id=' . $this->config->get('ya_pageFail'));

            $this->data['formcomment'] = $this->config->get('config_name');
            $this->data['short_dest'] = $this->config->get('config_name');

            $this->data['allow_methods'] = array();
            $this->data['default_method'] = $this->config->get('ya_paymentDfl');
            foreach (array('PC' => 'ym', 'AC' => 'cards', 'GP' => 'cash', 'MC' => 'mobile', 'WM' => 'wm', 'SB' => 'sb', 'AB' => 'ab', 'PB' => 'pb', 'MA' => 'ma', 'QW' => 'qw', 'QP' => 'qp', 'MP' => 'mp') as $name => $value) {
                if ((is_array($this->config->get('ya_paymentOpt')) && in_array(
                            $name, $this->config->get('ya_paymentOpt')
                        ))
                    || (is_array($this->config->get('ya_paymentOpt_wallet')) && in_array(
                            $name, $this->config->get(
                            'ya_paymentOpt_wallet'
                        )
                        ))
                )
                    $this->data['allow_methods'][$name] = $this->language->get('text_method_' . $value);
            }
            $this->data['mpos_page_url'] = $this->url->link('payment/yandexmoney/confirm', '', 'SSL');
            $this->data['method_label'] = $this->language->get('text_method');
            $this->data['order_text'] = $this->language->get('text_order');

            if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                $this->data['imageurl'] = $this->config->get('config_ssl') . 'image/';
            } else {
                $this->data['imageurl'] = $this->config->get('config_url') . 'image/';
            }
        } else {
            $fio = array();
            if (!empty($order_info['lastname'])) {
                $fio[] = $order_info['lastname'];
            }
            if (!empty($order_info['firstname'])) {
                $fio[] = $order_info['firstname'];
            }
            $narrative = $this->parsePlaceholders($this->config->get('ya_billing_purpose'), $order_info);
            $this->data['formId'] = $this->config->get('ya_billing_id');
            $this->data['narrative'] = $narrative;
            $this->data['fio'] = implode(' ', $fio);

            $this->updateOrderStatus($order_info['order_id'], $this->config->get('ya_billing_status'), $narrative);
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/yandexmoney.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/yandexmoney.tpl';
        } else {
            $this->template = 'default/template/payment/yandexmoney.tpl';
        }
        if ($child) {
            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/footer',
                'common/header'
            );
        }
        $this->response->addHeader('Content-Type: text/html; charset=utf-8');
        $this->response->setOutput($this->render());
    }

    private function prepare_54law($order_info, &$data)
    {
        $this->load->model('account/order');
        $this->load->model('catalog/product');

        if (!$this->config->get('ya_54lawmode') || $this->config->get('ya_kassamode') != '1') return false;

        $taxRates = $this->config->get('ya_54lawtax');
        if (empty($taxRates) || !isset($taxRates['default'])) {
            $taxRates['default'] = YandexMoneyReceipt::DEFAULT_TAX_RATE_ID;
        }
        $receipt = new YandexMoneyReceipt($taxRates['default'], YandexMoneyReceipt::DEFAULT_CURRENCY);
        $order_products = $this->model_account_order->getOrderProducts($this->session->data['order_id']);
        foreach ($order_products as $prod) {
            $product_info = $this->model_catalog_product->getProduct($prod["product_id"]);
            if (isset($product_info['tax_class_id']) && isset($taxRates[$product_info['tax_class_id']])) {
                $taxId = $taxRates[$product_info['tax_class_id']];
                $receipt->addItem($prod["name"], $prod["price"], $prod["quantity"], $taxId);
            } else {
                $receipt->addItem($prod["name"], $prod["price"], $prod["quantity"]);
            }
        }

        $order_totals = $this->model_account_order->getOrderTotals($this->session->data['order_id']);
        $iTotal = 0;
        foreach ($order_totals as $total) {
            if (isset($total["code"]) && $total["code"] === "shipping") {
                if (isset($total['tax_class_id']) && isset($taxRates[$total['tax_class_id']])) {
                    $taxId = $taxRates[$total['tax_class_id']];
                    $receipt->addShipping($total["title"], $total["value"], $taxId);
                } else {
                    $receipt->addShipping($total["title"], $total["value"]);
                }
            } elseif (isset($total["code"]) && $total["code"] === "total") {
                $iTotal = $total["value"];
            }
        }

        $receipt->normalize(round($iTotal, 2));

        if (isset($order_info['email'])) {
            $receipt->setCustomerContact($order_info['email']);
        } elseif (isset($order_info['phone'])) {
            $receipt->setCustomerContact($order_info['phone']);
        }

        $data["receipt"] = $receipt->getJson();
    }

    protected function index()
    {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->payment($order_info);
    }

    public function repay()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('payment/yandexmoney/repay', 'order_id=' . $this->request->get['order_id'], 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }
        $this->load->model('account/order');
        $order_info = $this->model_account_order->getOrder((int)$this->request->get['order_id']);
        if ($order_info) {
            $this->payment($order_info, true);
        } else {
            $this->redirect($this->url->link('account/order/info', 'order_id=' . $this->request->get['order_id'], 'SSL'));
        }
    }

    public function confirm()
    {
        $this->language->load('payment/yandexmoney');
        $pay_url = $this->url->link('payment/yandexmoney/repay', 'order_id=' . $this->session->data['order_id'], 'SSL');
        $this->load->model('checkout/order');
        $finish = $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'), '<a href="' . $pay_url . '" class="button">' . $this->language->get('text_repay') . '</a>', true);
        $this->cart->clear();
        if (isset($this->request->post['paymentType']) && ($this->request->post['paymentType'] == 'MP')) {
            $this->redirect($this->url->link('information/information', 'information_id=' . $this->config->get('ya_pageSuccessMP'), 'SSL'));
        }
    }

    public function callback()
    {
        $ymObj = new YandexMoneyObj();
        $callbackParams = $_POST;
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            echo "You aren't Yandex.Money. We use module for Opencart 1.5.x";
            return;
        }
        $ymObj->org_mode = ($this->config->get('ya_kassamode') == '1');
        $ymObj->password = ($ymObj->org_mode) ? $this->config->get('ya_shopPassword') : $this->config->get('ya_appPassword');
        $ymObj->shopid = $this->config->get('ya_shopid');
        $notify = (bool)false;//$this->config->get('ya_notify');
        if (isset($callbackParams["orderNumber"]) || isset($callbackParams["label"])) {
            $order_id = ($ymObj->org_mode) ? $callbackParams["orderNumber"] : $callbackParams["label"];
        } else {
            $order_id = 0;
        }
        if ($ymObj->checkSign($callbackParams)) {
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            if ($order_info != false) {
                $comment = ($ymObj->org_mode && $callbackParams['paymentType'] == "MP" && isset($callbackParams['orderDetails'])) ? $callbackParams['orderDetails'] : '';
                $amount = number_format($callbackParams[($ymObj->org_mode) ? 'orderSumAmount' : 'withdraw_amount'], 2, '.', '');
                if ($callbackParams['paymentType'] == "MP" || $amount == number_format($order_info['total'], 2, '.', '')) {
                    if (isset($callbackParams['action']) && $callbackParams['action'] == 'paymentAviso') {
                        $res = $this->model_checkout_order->update($order_id, $this->config->get('ya_newStatus'), "Номер транзакции: " . $callbackParams['invoiceId'] . ". Сумма: " . $callbackParams['orderSumAmount'] . ' ' . $comment, $notify);
                    } elseif (isset($callbackParams["action"]) && $ymObj->org_mode) {
                        $res = $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'), $comment);
                    } else {
                        $sender = ($callbackParams['sender'] != '') ? "Номер кошелька Яндекс.Денег: " . $callbackParams['sender'] . "." : '';
                        $res = $this->model_checkout_order->confirm($order_id, $this->config->get('ya_newStatus'), $sender . " Сумма: " . $callbackParams['withdraw_amount'] . ' ' . $comment, $notify);
                    }
                    $ymObj->sendCode($callbackParams, "0");
                } else {
                    $ymObj->sendCode($callbackParams, "100");
                }
            } elseif (isset($callbackParams['paymentType']) && $callbackParams['paymentType'] == "MP") {
                //Заказа нет и пока будем отвечать успехом
                $ymObj->sendCode($callbackParams, "0");
            } else {
                $ymObj->sendCode($callbackParams, "200");
            }
        } else {
            $ymObj->sendCode($callbackParams, "1");
        }
    }

    private function updateOrderStatus($orderId, $status, $text)
    {
        $this->load->model('checkout/order');
        $this->model_checkout_order->confirm($orderId, $status, $text);

        $this->cart->clear();
        if (isset($this->session->data['order_id'])) {
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['guest']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);
            unset($this->session->data['totals']);
        }
    }

    private function parsePlaceholders($template, $order)
    {
        $replace = array();
        foreach ($order as $key => $value) {
            if (is_scalar($value)) {
                $replace['%' . $key . '%'] = $value;
            }
        }
        return strtr($template, $replace);
    }
}

class YandexMoneyObj
{
    const MODE_NONE = 0;
    const MODE_KASSA = 1;
    const MODE_MONEY = 2;
    const MODE_BILLING = 3;

    private $mode;

    public $test_mode;//
    public $org_mode; //
    public $epl;        //

    public $shopid;    //
    public $password;    //

    public function __construct($mode)
    {
        $this->mode = (int)$mode;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getFormUrl()
    {
        if ($this->mode !== self::MODE_BILLING) {
            $demo = ($this->test_mode) ? 'https://demomoney.yandex.ru/' : 'https://money.yandex.ru/';
            return ($this->org_mode) ? $demo . 'eshop.xml' : $demo . 'quickpay/confirm.xml';
        }
        return 'https://money.yandex.ru/fastpay/confirm';
    }

    public function checkSign($callbackParams)
    { //
        if ($this->org_mode) {
            $string = $callbackParams['action'] . ';' . $callbackParams['orderSumAmount'] . ';' . $callbackParams['orderSumCurrencyPaycash'] . ';' . $callbackParams['orderSumBankPaycash'] . ';' . $callbackParams['shopId'] . ';' . $callbackParams['invoiceId'] . ';' . $callbackParams['customerNumber'] . ';' . $this->password;
            $md5 = strtoupper(md5($string));
            return (strtoupper($callbackParams['md5']) == $md5);
        } else {
            $string = $callbackParams['notification_type'] . '&' . $callbackParams['operation_id'] . '&' . $callbackParams['amount'] . '&' . $callbackParams['currency'] . '&' . $callbackParams['datetime'] . '&' . $callbackParams['sender'] . '&' . $callbackParams['codepro'] . '&' . $this->password . '&' . $callbackParams['label'];
            $check = (sha1($string) == $callbackParams['sha1_hash']);
            if (!$check) {
                header('HTTP/1.0 401 Unauthorized');
                return false;
            }
            return true;
        }
    }

    public function sendCode($callbackParams, $code)
    { //
        if (!$this->org_mode) return false;
        header("Content-type: text/xml; charset=utf-8");
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<' . $callbackParams['action'] . 'Response performedDatetime="' . date("c") . '" code="' . $code
            . '" invoiceId="' . $callbackParams['invoiceId'] . '" shopId="' . $this->shopid . '"/>';
        echo $xml;
    }
}

if (!interface_exists('JsonSerializable')) {
    interface JsonSerializable
    {
        function JsonSerialize();
    }
}

/**
 * Класс чека
 */
class YandexMoneyReceipt implements JsonSerializable
{
    /** @var string Код валюты - рубли */
    const CURRENCY_RUB = 'RUB';

    /** @var string Используемая по умолчанию валюта */
    const DEFAULT_CURRENCY = self::CURRENCY_RUB;

    /** @var int Идентификатор ставки НДС по умолчанию */
    const DEFAULT_TAX_RATE_ID = 1;

    /** @var YandexMoneyReceiptItem[] Массив с информацией о покупаемых товарах */
    private $items;

    /** @var string Контакт покупателя, куда будет отправлен чек - либо имэйл, либо номер телефона */
    private $customerContact;

    /** @var int Идентификатор ставки НДС по умолчанию */
    private $taxRateId;

    /** @var string Валюта в которой производится платёж */
    private $currency;

    /** @var YandexMoneyReceiptItem|null Айтем в котором хранится информация о доставке как о товаре */
    private $shipping;

    /**
     * @param int $taxRateId
     * @param string $currency
     */
    public function __construct($taxRateId = self::DEFAULT_TAX_RATE_ID, $currency = self::DEFAULT_CURRENCY)
    {
        $this->taxRateId = $taxRateId;
        $this->items = array();
        $this->currency = $currency;
    }

    /**
     * Добавляет в чек товар
     * @param string $title Название товара
     * @param float $price Цена товара
     * @param float $quantity Количество покупаемого товара
     * @param int|null $taxId Идентификатор ставки НДС для товара или null
     * @return YandexMoneyReceipt
     */
    public function addItem($title, $price, $quantity = 1.0, $taxId = null)
    {
        $this->items[] = new YandexMoneyReceiptItem($title, $quantity, $price, false, $taxId);
        return $this;
    }

    /**
     * Добавляет в чек доставку
     * @param string $title Название способа доставки
     * @param float $price Цена доставки
     * @param int|null $taxId Идентификатор ставки НДС для доставки или null
     * @return YandexMoneyReceipt
     */
    public function addShipping($title, $price, $taxId = null)
    {
        $this->shipping = new YandexMoneyReceiptItem($title, 1.0, $price, true, $taxId);
        $this->items[] = $this->shipping;
        return $this;
    }

    /**
     * Устанавливает адрес доставки чека - или имейл или номер телефона
     * @param string $value Номер телефона или имэйл получателя
     * @return YandexMoneyReceipt
     */
    public function setCustomerContact($value)
    {
        $this->customerContact = $value;
        return $this;
    }

    /**
     * Возвращает стоимость заказа исходя из состава чека
     * @param bool $withShipping Добавить ли к стоимости заказа стоимость доставки
     * @return float Общая стоимость заказа
     */
    public function getAmount($withShipping = true)
    {
        $result = 0.0;
        foreach ($this->items as $item) {
            if ($withShipping || !$item->isShipping()) {
                $result += $item->getAmount();
            }
        }
        return $result;
    }

    /**
     * Преобразует чек в массив для дальнейшей его отправки в JSON формате
     * @return array Ассоциативный массив с чеком, готовый для отправки в JSON формате
     */
    public function jsonSerialize()
    {
        $items = array();

        foreach ($this->items as $item) {
            if ($item->getPrice() >= 0.0) {
                $items[] = array(
                    'quantity' => (string)$item->getQuantity(),
                    'price' => array(
                        'amount' => number_format($item->getPrice(), 2, '.', ''),
                        'currency' => $this->currency,
                    ),
                    'tax' => $item->hasTaxId() ? $item->getTaxId() : $this->taxRateId,
                    'text' => $this->escapeString($item->getTitle()),
                );
            }
        }
        return array(
            'items' => $items,
            'customerContact' => $this->escapeString($this->customerContact),
        );
    }

    /**
     * Сериализует чек в JSON формат
     * @return string Чек в JSON формате
     */
    public function getJson()
    {
        if (defined('JSON_UNESCAPED_UNICODE')) {
            return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            // для версий PHP которые не поддерживают передачу параметров в json_encode
            // заменяем в полученной при сериализации строке уникод последовательности
            // вида \u1234 на их реальное значение в utf-8
            return preg_replace_callback(
                '/\\\\u(\w{4})/',
                array($this, 'legacyReplaceUnicodeMatches'),
                json_encode($this->jsonSerialize())
            );
        }
    }

    public function legacyReplaceUnicodeMatches($matches)
    {
        return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
    }

    /**
     * Подгоняет стоимость товаров в чеке к общей цене заказа
     * @param float $orderAmount Общая стоимость заказа
     * @param bool $withShipping Поменять ли заодно и цену доставки
     * @return YandexMoneyReceipt
     */
    public function normalize($orderAmount, $withShipping = false)
    {
        if (!$withShipping) {
            if ($this->shipping !== null) {
                $orderAmount -= $this->shipping->getAmount();
            }
        }
        $realAmount = $this->getAmount($withShipping);
        if ($realAmount != $orderAmount) {
            $coefficient = $orderAmount / $realAmount;
            $realAmount = 0.0;
            $aloneId = null;
            foreach ($this->items as $index => $item) {
                if ($withShipping || !$item->isShipping()) {
                    $item->applyDiscountCoefficient($coefficient);
                    $realAmount += $item->getAmount();
                    if ($aloneId === null && $item->getQuantity() === 1.0) {
                        $aloneId = $index;
                    }
                }
            }
            if ($aloneId === null) {
                $aloneId = 0;
            }
            $diff = $orderAmount - $realAmount;
            if (abs($diff) >= 0.001) {
                if ($this->items[$aloneId]->getQuantity() === 1.0) {
                    $this->items[$aloneId]->increasePrice($diff);
                } else {
                    $item = $this->items[0]->fetchItem(1);
                    $item->increasePrice($diff);
                    array_splice($this->items, $aloneId + 1, 0, array($item));
                }
            }
        }
        return $this;
    }

    /**
     * Деэскейпирует строку для вставки в JSON
     * @param string $string Исходная строка
     * @return string Строка с эскейпированными "<" и ">"
     */
    private function escapeString($string)
    {
        return str_replace(array('<', '>'), array('&lt;', '&gt;'), html_entity_decode($string));
    }
}

/**
 * Класс товара в чеке
 */
class YandexMoneyReceiptItem
{
    /** @var string Название товара */
    private $title;

    /** @var float Количество покупаемого товара */
    private $quantity;

    /** @var float Цена товара */
    private $price;

    /** @var bool Является ли наименование доставкой товара */
    private $shipping;

    /** @var int|null Идентификатор ставки НДС для конкретного товара */
    private $taxId;

    /**
     * YandexMoneyReceiptItem constructor.
     * @param string $title
     * @param float $quantity
     * @param float $price
     * @param bool $isShipping
     * @param int|null $taxId
     */
    public function __construct($title, $quantity, $price, $isShipping, $taxId)
    {
        $this->title = mb_substr($title, 0, 60, 'utf-8');
        $this->quantity = (float)$quantity;
        $this->price = round($price, 2);
        $this->shipping = $isShipping;
        $this->taxId = $taxId;
    }

    /**
     * Возвращает цену товара
     * @return float Цена товара
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Возвращает общую стоимость позиции в чеке
     * @return float Стоимость покупаемого товара
     */
    public function getAmount()
    {
        return round($this->price * $this->quantity, 2);
    }

    /**
     * Возвращает название товара
     * @return string Название товара
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Возвращает количество покупаемого товара
     * @return float Количество товара
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Проверяет, установлена ли для товара ставка НДС
     * @return bool True если ставка НДС для товара установлена, false если нет
     */
    public function hasTaxId()
    {
        return $this->taxId !== null;
    }

    /**
     * Возвращает ставку НДС товара
     * @return int|null Идентификатор ставки НДС или null если он не был установлен
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * Привеняет для товара скидку
     * @param float $value Множитель скидки
     */
    public function applyDiscountCoefficient($value)
    {
        $this->price = round($value * $this->price, 2);
    }

    /**
     * Увеличивает цену товара на указанную величину
     * @param float $value Сумма на которую цену товара увеличиваем
     */
    public function increasePrice($value)
    {
        $this->price = round($this->price + $value, 2);
    }

    /**
     * Уменьшает количество покупаемого товара на указанное, возвращает объект позиции в чеке с уменьшаемым количеством
     * @param float $count Количество на которое уменьшаем позицию в чеке
     * @return YandexMoneyReceiptItem Новый инстанс позиции в чеке
     */
    public function fetchItem($count)
    {
        if ($count > $this->quantity) {
            throw new BadMethodCallException();
        }
        $result = new YandexMoneyReceiptItem($this->title, $count, $this->price, false, $this->taxId);
        $this->quantity -= $count;
        return $result;
    }

    /**
     * Проверяет является ли текущая позиция доставкой товара
     * @return bool True если доставка товара, false если нет
     */
    public function isShipping()
    {
        return $this->shipping;
    }
}
