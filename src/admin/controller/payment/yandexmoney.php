<?php

/**
 * Class ControllerPaymentYandexMoney
 *
 * @property-read Language $language
 * @property-read Request $request
 * @property-read Config $config
 */
class ControllerPaymentYandexMoney extends Controller
{
    const MODE_NONE = 0;
    const MODE_KASSA = 1;
    const MODE_MONEY = 2;
    const MODE_BILLING = 3;

    private $error = array();

    private $ya_version = '1.8.2';

    private $lang = array(
        'setting_head',
        'license',
        'version',
        'tab_kassa',
        'tab_money',
        'forwork_kassa',
        'kassa_enable',
        'testmode',
        'workmode',
        'checkUrl_help',
        'successUrl',
        'successUrl_help',
        'lk_kassa',
        'shopid',
        'scid',
        'shopPassword',
        'lk_help',
        'paymode_head',
        'paymode_label',
        'smartpay',
        'shoppay',
        'paymode_help',
        'option_help',
        'forwork_money',
        'enable_money',
        'redirectUrl_help',
        'account_head',
        'wallet',
        'password',
        'account_help',
        'option_wallet',
        'optDefault',
        'successPage_label',
        'page_standart',
        'successPage_help',
        'failPage_label',
        'page_standart',
        'failPage_help',
        'successMP_label',
        'successMP_help',
        'namePay_label',
        'namePay_help',
        '54lawmode_label',
        '54lawmode_help',
        '54lawtaxtable_label',
        '54lawtax_default_head',
        '54lawtax_default_head_desc',
        '54lawtax_head',
        '54lawtax_head_desc',
        'feature_head',
        'debug_label',
        'off',
        'on',
        'debug_help',
        'newStutus_label',
        'sordOrder_label',
        'idZone_label',

        'tab_billing',
        'forwork_billing',
        'enable_billing',
        'billing_id',
        'billing_purpose',
        'billing_purpose_desc',
        'billing_purpose_default',
        'billing_status',
        'billing_status_desc',
    );

    private $name_methods = array(
        'PC' => 'Оплата из кошелька в Яндекс.Деньгах',
        'AC' => 'Оплата с произвольной банковской карты',
        'GP' => 'Оплата наличными через кассы и терминалы',
        'MC' => 'Оплата со счета мобильного телефона',
        'WM' => 'Оплата из кошелька в системе WebMoney',
        'SB' => 'Оплата через Сбербанк: оплата по SMS или Сбербанк Онлайн',
        'AB' => 'Оплата через Альфа-Клик',
        'MA' => 'Оплата через MasterPass',
        'PB' => 'Оплата через Промсвязьбанк',
        'QW' => 'Оплата через QIWI Wallet',
        'QP' => 'Оплата через доверительный платеж (Куппи.ру)',
    );

    private $requiredParameters = array(
        self::MODE_NONE => array(

        ),
        self::MODE_KASSA => array(
            'ya_shopid',
            'ya_scid',
            'ya_shopPassword',
        ),
        self::MODE_MONEY => array(
            'ya_wallet',
            'ya_appPassword'
        ),
        self::MODE_BILLING => array(
            'ya_billing_id',
            'ya_billing_purpose',
            'ya_billing_status',
        ),
    );

    private $kassa_taxes = array(
        "1" => "без НДС",
        "2" => "НДС по ставке 0%",
        "3" => "НДС чека по ставке 10%",
        "4" => "НДС чека по ставке 18%",
        "5" => "НДС чека по расчетной ставке 10/110",
        "6" => "НДС чека по расчетной ставке 18/118"
    );

    private $allow_params = array(
        "ya_kassamode",
        "ya_workmode",
        "ya_shopid",
        "ya_scid",
        "ya_shopPassword",
        "ya_paymode",
        "ya_paymentOpt",
        "ya_paymentOpt_wallet",
        "ya_paymentDfl",
        "ya_pageSuccess",
        "ya_pageFail",
        "ya_pageSuccessMP",
        "ya_namePaySys",
        "ya_54lawmode",
        "ya_54lawtax",
        "ya_newStatus",
        "ya_debugmode",
        "ya_moneymode",
        "ya_wallet",
        'ya_appPassword',
        'ya_sortOrder',
        'ya_idZone',

        'ya_billingmode',
        'ya_billing_id',
        'ya_billing_purpose',
        'ya_billing_status',
    );

    private function sendStatistics()
    {
        $this->language->load('payment/yandexmoney');
        $this->load->model('setting/setting');
        $setting = $this->model_setting_setting->getSetting('yandexmoney');
        $array = array(
            'url' => $this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG,
            'cms' => 'opencart',
            'version' => VERSION,
            'ver_mod' => $this->ya_version,
            'yacms' => false,
            'email' => $this->config->get('config_email'),
            'shopid' => $setting['ya_shopid'],
            'settings' => array(
                'kassa' => (bool) ($setting['ya_kassamode']=='1')?true:false,
                'kassa_epl' => (bool) ($setting['ya_kassamode']=='1' && $setting['ya_paymode']=='kassa')?true:false,
                'p2p' => (bool) ($setting['ya_moneymode']=='1')?true:false
            )
        );

        $array_crypt = base64_encode(serialize($array));

        $url = 'https://statcms.yamoney.ru/v2/';
        $curlOpt = array(
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_POST => true,
            CURLOPT_FRESH_CONNECT => TRUE,
        );

        $curlOpt[CURLOPT_HTTPHEADER] = array('Content-Type: application/x-www-form-urlencoded');
        $curlOpt[CURLOPT_POSTFIELDS] = http_build_query(array('data' => $array_crypt, 'lbl'=>1));

        $curl = curl_init($url);
        curl_setopt_array($curl, $curlOpt);
        $rbody = curl_exec($curl);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        $rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $json=json_decode($rbody);
        if ($rcode==200 && isset($json->new_version)){
            return sprintf($this->language->get('text_need_update'),$json->new_version);
        }else{
            return false;
        }
    }

    public function install()
    {
        $this->log->write("install yandexmoney");
    }

    public function uninstall()
    {
        $this->log->write("uninstall yandexmoney");
    }

    public function index()
    {
        $this->language->load('payment/yandexmoney');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->saveSettings();
        }
        $this->data['errors'] = $this->error;

        $url = new Url(HTTP_CATALOG, $this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG);
        $this->data['callback_url'] = str_replace("http:", "https:", $url->link('payment/yandexmoney/callback', '', 'SSL'));
        $this->data['shopSuccessURL'] = $url->link('checkout/success', '', 'SSL');
        $this->data['shopFailURL'] = $url->link('checkout/failure', '', 'SSL');

        $this->data['yandexmoney_version'] = $this->ya_version;

        $this->loadLocalization();

        $this->data['action'] = $this->url->link('payment/yandexmoney', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        $this->load->model('localisation/order_status');
        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/geo_zone');
        $this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/tax_class');
        $this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
        $this->data['kassa_taxes'] = $this->kassa_taxes;

        $this->load->model('catalog/information');
        $this->data['pages_mpos'] = $this->model_catalog_information->getInformations();

        foreach ($this->allow_params as $s_item) {
            $this->data[$s_item] = (isset($this->request->post[$s_item])) ? $this->request->post[$s_item] : $this->config->get($s_item);
        }
        $this->data['name_methods'] = $this->name_methods;

        $this->template = 'payment/yandexmoney.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate()
    {
        $this->language->load('payment/yandexmoney');
        $this->error = array();
        if (!$this->user->hasPermission('modify', 'payment/yandexmoney')) {
            $this->error[] = $this->language->get('error_permission');
            return false;
        }
        foreach ($this->allow_params as $param) {
            if (!isset($this->request->post[$param])) {
                $this->request->post[$param] = '';
            }
        }

        if (isset($this->request->post['ya_kassamode']) && $this->request->post['ya_kassamode'] == '1') {
            $mode = self::MODE_KASSA;
        } elseif (isset($this->request->post['ya_moneymode']) && $this->request->post['ya_moneymode'] == '1') {
            $mode = self::MODE_MONEY;
        } elseif (isset($this->request->post['ya_billingmode']) && $this->request->post['ya_billingmode'] == '1') {
            $mode = self::MODE_BILLING;
        } else {
            $mode = self::MODE_NONE;
        }

        foreach ($this->requiredParameters[$mode] as $field) {
            if (!$this->request->post[$field]) {
                $this->error[] = $this->language->get('error_' . $field);
            }
        }

        if ($mode == self::MODE_KASSA && $this->request->post['ya_paymode'] == 'shop' && empty($this->request->post['ya_paymentOpt'])) {
            $this->error[] = $this->language->get('error_empty_payment');
        }
        if ($mode == self::MODE_MONEY && count($this->request->post['ya_paymentOpt_wallet']) == 0) {
            $this->error[] = $this->language->get('error_empty_payment');
        }

        if (!$this->error) return true; else return false;
    }

    private function loadLocalization()
    {
        $langList = array(
            'yandexmoney_license', 'heading_title', 'text_payment', 'text_yes', 'text_no', 'text_disabled',
            'text_enabled', 'text_all_zones', 'text_welcome1', 'text_welcome2', 'text_params', 'text_param_name',
            'text_param_value', 'text_aviso1', 'text_aviso2', 'title_default', 'entry_version', 'entry_license',
            'entry_testmode', 'entry_modes', 'entry_mode1', 'entry_mode2','entry_mode3','entry_methods',
            'entry_method_ym','entry_method_cards','entry_method_cash', 'entry_method_mobile','entry_method_wm',
            'entry_method_ab','entry_method_sb','entry_method_ma', 'entry_method_pb','entry_method_qw',
            'entry_method_qp','entry_method_mp','entry_default_method', 'entry_page_mpos','entry_page_success',
            'entry_page_fail','entry_shopid','entry_scid','entry_title', 'entry_total','entry_total2','entry_password',
            'entry_account','entry_order_status','entry_notify', 'entry_geo_zone','entry_status','entry_sort_order',
            'button_save','button_cancel'
        );
        foreach ($langList as $item) {
            $this->data[$item] = $this->language->get($item);
        }
        foreach ($this->lang as $item) {
            $this->data['lang_'.$item] = $this->language->get($item);
        }
    }

    private function saveSettings()
    {
        $settings = array();
        $settings['yandexmoney_status'] = '1';
        foreach ($this->allow_params as $allowedParameter) {
            $settings[$allowedParameter] = false;
            if (isset($this->request->post[$allowedParameter])) {
                $settings[$allowedParameter] = $this->request->post[$allowedParameter];
            }
        }

        if (isset($this->request->post['ya_kassamode']) && $this->request->post['ya_kassamode'] == '1') {
            $settings['ya_mode'] = self::MODE_KASSA;
            $settings['ya_moneymode'] = '0';
            $settings['ya_billingmode'] = '0';
        } elseif (isset($this->request->post['ya_moneymode']) && $this->request->post['ya_moneymode'] == '1') {
            $settings['ya_mode'] = self::MODE_MONEY;
            $settings['ya_billingmode'] = '0';
        } elseif (isset($this->request->post['ya_billingmode']) && $this->request->post['ya_billingmode'] == '1') {
            $settings['ya_mode'] = self::MODE_BILLING;
            $settings['ya_kassamode'] = '0';
            $settings['ya_moneymode'] = '0';
        } else {
            $settings['ya_mode'] = self::MODE_NONE;
            $settings['yandexmoney_status'] = '0';
        }

        $this->model_setting_setting->editSetting('yandexmoney', $settings);

        $this->data['success'] = $this->language->get('text_success');
        $updater = $this->sendStatistics();
        if ($updater) {
            $this->data['attention'] = $updater;
        } /* else {
            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        } */
    }
}
