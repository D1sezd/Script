<?php

namespace EmanPlugin; 

class MyApi {
    public $api_url;

    public function list_vacancies($post, $vid = 0) { // *list_vacancies
        global $wpdb;

        $ret = array();

        if (!is_object($post)) {
            return false;
        }

        $page = 0;
        $found = false;
        l1:
        $params = "status=all&id_user=" . $this->self_get_option('superjob_user_id') . "&with_new_response=0&order_field=date&order_direction=desc&page={$page}&count=100";
        $res = $this->api_send($this->api_url . '/hr/vacancies/?' . $params);
        $res_o = json_decode($res);
        if ($res !== false && is_object($res_o) && isset($res_o->objects)) {
            $ret = array_merge($res_o->objects, $ret);
            if ($vid > 0) // Для конкретной вакансии, иначе возвращаем все
                foreach ($res_o->objects as $key => $value) {
                    if ($value->id == $vid) {
                        $found = $value;
                        break;
                    }
                }

            if ($found === false && $res_o->more) {
                $page++;
                goto l1;
            } else {
                if (is_object($found)) {
                    return $found;
                } else {
                    return $ret;
                }
            }
        } else {
            return false;
        }

        return false;
    }    
    public function api_send($url) {
        $ch = curl_init($url); // Инициализация cURL

        // Установка параметров
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Вернуть ответ как строку
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Можно откл проверку SSL
    
        // Отправка запроса и получение ответа
        $response = curl_exec($ch);
    
        // Проверка на ошибки
        if ($response === false) {
            throw new Exception(curl_error($ch));
        }
    
        // Завершение cURL
        curl_close($ch);
    
        return $response;
    }
    
    public function self_get_option($option_name) {     
        $option_value = get_option($option_name); // Можно получить опции из настроек плагина, пример получения из WordPress
            return $option_value;
    }
    
}
