<?php

class ControllerPaymentYandexMoney extends Controller
{
    private function payment($order_info, $child = false)
    {
        $this->language->load('payment/yandexmoney');

        $yandexMoney = new YandexMoneyObj();
        $yandexMoney->org_mode = (bool)($this->config->get('ya_kassamode') == '1');
        $yandexMoney->password = ($yandexMoney->org_mode) ? $this->config->get('ya_shopPassword') : $this->config->get('ya_appPassword');
        $yandexMoney->shopid = $this->config->get('ya_shopid');
        $yandexMoney->test_mode = (bool)($this->config->get('ya_workmode') != '1');
        $yandexMoney->epl = (bool)($this->config->get('ya_kassamode') == '1' && $this->config->get('ya_paymode') == 'kassa');

        if (isset($order_info['email'])) $this->data['email'] = $order_info['email'];
        if (isset($order_info['telephone'])) $this->data['phone'] = $order_info['telephone'];
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['action'] = $yandexMoney->getFormUrl();
        $this->data['epl'] = $yandexMoney->epl;
        $this->data['org_mode'] = $yandexMoney->org_mode;
        $this->data['order_id'] = $order_info['order_id'];
        $this->data['account'] = $this->config->get('ya_wallet');
        $this->data['shop_id'] = $this->config->get('ya_shopid');
        $this->data['scid'] = $this->config->get('ya_scid');

        $this->prepare_54law($order_info, $this->data);

        $this->data['customerNumber'] = trim($order_info['order_id'] . ' ' . $order_info['email']);

        $this->data['shopSuccessURL'] = (!$this->config->get('ya_pageSuccess')) ? $this->url->link('checkout/success', '', 'SSL') : $this->url->link('information/information', 'information_id=' . $this->config->get('ya_pageSuccess'));
        $this->data['shopFailURL'] = (!$this->config->get('ya_pageFail')) ? $this->url->link('checkout/failure', '', 'SSL') : $this->url->link('information/information', 'information_id=' . $this->config->get('ya_pageFail'));

        $this->data['formcomment'] = $this->config->get('config_name');
        $this->data['short_dest'] = $this->config->get('config_name');
        $this->data['comment'] = $order_info['comment'];
        $this->data['cmsname'] = ($child) ? 'opencart-extracall' : 'opencart';
        $this->data['sum'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $this->data['allow_methods'] = array();
        $this->data['default_method'] = $this->config->get('ya_paymentDfl');
        foreach (array('PC' => 'ym', 'AC' => 'cards', 'GP' => 'cash', 'MC' => 'mobile', 'WM' => 'wm', 'SB' => 'sb', 'AB' => 'ab', 'PB' => 'pb', 'MA' => 'ma', 'QW' => 'qw', 'QP' => 'qp', 'MP' => 'mp') as $name => $value) {
            if ((is_array($this->config->get('ya_paymentOpt')) && in_array($name, $this->config->get('ya_paymentOpt')))
                || (is_array($this->config->get('ya_paymentOpt_wallet')) && in_array($name, $this->config->get('ya_paymentOpt_wallet')))
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

        $kassa_taxRate = $this->config->get('ya_54lawtax');
        if (!$this->config->get('ya_54lawmode') || $this->config->get('ya_kassamode') != '1') return false;
        $items = array();
        $order_products = $this->model_account_order->getOrderProducts($this->session->data['order_id']);
        foreach ($order_products as $prod) {
            $amount = $prod["price"];
            $product_info = $this->model_catalog_product->getProduct($prod["product_id"]);
            $price = new stdClass();
            $price->amount = $amount;
            $price->currency = "RUB";
            $tax_id = (isset($product_info['tax_class_id'])) ? $product_info['tax_class_id'] : "default";
            $items[] = array(
                "quantity" => $prod["quantity"],
                "price" => $price,
                "tax" => isset($kassa_taxRate[$tax_id]) ? $kassa_taxRate[$tax_id] : $kassa_taxRate["default"],
                "text" => $prod["name"]
            );
            //$subTotal += $price->amount*$prod["quantity"];
        }
        //Coupon
        //Shipping
        $order_totals = $this->model_account_order->getOrderTotals($this->session->data['order_id']);
        $shipping = [];
        $voucherValue = 0;
        $iDisc = 0;
        $iTotal = 0;
        $iSubTotal = 0;
        $iShip = 0;

        foreach ($order_totals as $total) {
            if (isset($total["code"])) {
                switch ($total["code"]) {
                    case "shipping":
                        $price = new stdClass();
                        $price->amount = round($total["value"], 2);
                        $price->currency = "RUB";
                        $tax_id = (isset($total['tax_class_id'])) ? $total['tax_class_id'] : "default";
                        $shipping = array(
                            "quantity" => 1,
                            "price" => $price,
                            "tax" => $kassa_taxRate[$tax_id],
                            "text" => $total["title"]
                        );
                        $iShip = $total["value"];
                        break;
                    case "coupon":
                        $iDisc = $total["value"];
                        //$subTotal += abs($total["value"]);
                        break;
                    case "voucher":
                        $voucherValue = $total["value"];
                        break;
                    case "sub_total":
                        $iSubTotal = $total["value"];
                        break;
                    case "total":
                        $iTotal = $total["value"];
                        break;
                }
            }
        }

        if ($iDisc || $voucherValue) {
            $subTotalPercent = $iSubTotal / 100;
            $discount = abs($iDisc) + abs($voucherValue);
            $percentDiscount = $discount / $subTotalPercent;
            foreach ($items as $item1) {
                $itemPercent = $item1["price"]->amount / 100  * $percentDiscount;
                $item1["price"]->amount = round($item1["price"]->amount - $itemPercent, 2);
            }
        }

        $receipt = new stdClass();
        if (isset($order_info['email'])) {
            $receipt->customerContact = $order_info['email'];
        } elseif (isset($order_info['phone'])) {
            $receipt->customerContact = $order_info['phone'];
        }

        if (!empty($shipping)) {
            $items[] = $shipping;
        }

        $receipt->items = $items;

        $data["receipt"] = htmlentities(json_encode($receipt));
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
}

Class YandexMoneyObj
{
    public $test_mode;//
    public $org_mode; //
    public $epl;        //

    public $shopid;    //
    public $password;    //

    public function getFormUrl()
    { //
        $demo = ($this->test_mode) ? 'https://demomoney.yandex.ru/' : 'https://money.yandex.ru/';
        return ($this->org_mode) ? $demo . 'eshop.xml' : $demo . 'quickpay/confirm.xml';
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
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
			<' . $callbackParams['action'] . 'Response performedDatetime="' . date("c") . '" code="' . $code . '" invoiceId="' . $callbackParams['invoiceId'] . '" shopId="' . $this->shopid . '"/>';
        echo $xml;
    }
}

?>