<?php

/**
 * RajaOngkir PHP Client
 * Documentation on RajaOngkir http://rajaongkir.com/dokumentasi
 */
class RajaOngkir {

    /**
     * @var string 
     */
    private static $apiKey;

    /**
     * @var string 
     */
    private static $host = "http://rajaongkir.com/";

    /**
     * @var RESTClient
     */
    public $rest;

    /**
     * Construct
     * @param string $apiKey API Key Anda sebagaimana yang tercantum di akun panel RajaOngkir
     * @param string[] $additionalHeaders Header tambahan seperti android-key, ios-key, dll
     */
    public function __construct($apiKey, $additionalHeaders = []) {
        self::$apiKey = $apiKey;
        $this->rest = new RESTClient;
        $this->rest->initialize(array('server' => self::$host));
        $this->rest->set_header('Content-Type', 'application/x-www-form-urlencoded');
        $this->rest->set_header('key', self::$apiKey);
        foreach ($additionalHeaders as $key => $value) {
            $this->restrest->set_header($key, $value);
        }
    }

    /**
     * Fungsi untuk mendapatkan data propinsi di Indonesia
     * @param integer $provinceID ID propinsi, jika NULL tampilkan semua propinsi
     * @return object Object yang berisi informasi response, terdiri dari: code, headers, body, raw_body.
     */
    function getProvince($provinceID = null) {
        $response = CJSON::decode($this->rest->get('api/province', ['id' => $provinceID]));
        return isset($response['rajaongkir']['results']) ? $response['rajaongkir']['results'] : null;
    }

    /**
     * Fungsi untuk mendapatkan data kota di Indonesia
     * @param integer $provinceID ID propinsi
     * @param integer $cityID ID kota, jika ID propinsi dan kota NULL maka tampilkan semua kota
     * @return object Object yang berisi informasi response, terdiri dari: code, headers, body, raw_body.
     */
    function getCity($provinceID = null, $cityID = null) {
        $response = CJSON::decode($this->rest->get('api/city', ['id' => $cityID, 'province' => $provinceID]));
        return isset($response['rajaongkir']['results']) ? $response['rajaongkir']['results'] : null;
    }

    /**
     * Fungsi untuk mendapatkan data ongkos kirim
     * @param integer $origin ID kota asal
     * @param integer $destination ID kota tujuan
     * @param integer $weight Berat kiriman dalam gram
     * @param string $courier Kode kurir, jika NULL maka tampilkan semua kurir
     * @return object Object yang berisi informasi response, terdiri dari: code, headers, body, raw_body.
     */
    function getCost($origin, $destination, $weight, $courier = null) {
        $response = CJSON::decode($this->rest->post('api/cost', [
                            'origin' => $origin,
                            'destination' => $destination,
                            'weight' => $weight,
                            'courier' => $courier,
        ]));
        return isset($response['rajaongkir']['results']) ? $response['rajaongkir']['results'] : null;
    }

}
