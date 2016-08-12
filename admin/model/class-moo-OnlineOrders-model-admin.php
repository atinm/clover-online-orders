<?php
/**
 * Created by PhpStorm.
 * User: Intents Coder
 * Date: 10/16/2015
 * Time: 3:22 PM
 */

class moo_OnlineOrders_Admin_Model {
    
    private $token;
    private $db;
    private $url_api;

    function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->url_api = "http://api.smartonlineorders.com/";
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    public function checkToken()
    {
       $url = "checktoken";
       return $this->callApi($url,$this->token);

    }


    private function callApi($url,$accesstoken)
    {

        $headr = array();
        $headr[] = 'Accept: application/json';
        $headr[] = 'X-Authorization: '.$accesstoken;
        $url=  $this->url_api.$url;
        //cURL starts
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_HTTPGET,true);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER,false);
        $reply = curl_exec($crl);

        //error handling for cURL
        if ($reply === false) {
            print_r('Curl error: ' . curl_error($crl));
            return false;
        }
        $info = curl_getinfo($crl);
        curl_close($crl);
        //   var_dump($reply);
        return $reply;
        if($info['http_code']==200)return $reply;
        return false;
    }

    private function callApi_Post($url,$accesstoken,$fields_string)
    {

        $headr = array();
        $headr[] = 'Accept: application/json';
        $headr[] = 'X-Authorization: '.$accesstoken;
        $url=  $this->url_api.$url;

        //cURL starts
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_POST,true);
        curl_setopt($crl,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER,false);
        $reply = curl_exec($crl);

        //error handling for cURL
        if ($reply === false) {
            print_r('Curl error: ' . curl_error($crl));
            return false;
        }

        $info = curl_getinfo($crl);
        curl_close($crl);
       // var_dump($reply);

        if($info['http_code']==200)return $reply;
        return false;
    }
}