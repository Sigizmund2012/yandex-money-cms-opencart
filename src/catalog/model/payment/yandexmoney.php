<?php

class ModelPaymentYandexMoney extends Model
{
    public function getMethod($address, $total)
    {
        $this->language->load('payment/yandexmoney');

        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '"
            . (int)$this->config->get('ya_idZone') . "' AND country_id = '" . (int)$address['country_id']
            . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')"
        );

        if ($total == 0) {
            $status = false;
        } elseif (!$this->config->get('ya_idZone')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();
        if ($status) {
            $mode = $this->config->get('ya_mode');
            if ($mode != 3) {
                $text = ($this->config->get('ya_kassamode') == '1' && $this->config->get(
                        'ya_namePaySys'
                    )) ? $this->config->get('ya_namePaySys') : $this->language->get('text_title');
            } else {
                $text = 'Яндекс.Платежка (банковские карты, кошелек)';
            }
            $method_data = array(
                'code'       => 'yandexmoney',
                'title'      => $text,
                'sort_order' => (int)$this->config->get('ya_sortOrder')
            );
        }

        return $method_data;
    }
}
