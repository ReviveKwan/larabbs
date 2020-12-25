<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
    private $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
    private $appid;
    private $key;
    private $text;

    public function __construct($text)
    {
        $this->text = $text;
        $this->appid = env('BAIDU_TRANSLATE_APPID');
        $this->key = env('BAIDU_TRANSLATE_KEY');
    }

    public function translate()
    {
        if(empty($this->appid) || empty($this->key)) {
            return $this->pinyin();
        }

        $http = new Client();
        $response = $http->get($this->str_query());
        $result = json_decode($response->getBody(), true);

        // 尝试获取获取翻译结果
        if (isset($result['trans_result'][0]['dst'])) {
            return Str::slug($result['trans_result'][0]['dst']);
        } else {
            // 如果百度翻译没有结果，使用拼音作为后备计划。
            return $this->pinyin();
        }

    }

    private function pinyin()
    {
        return Str::slug(app(Pinyin::class)->permalink($this->text));
    }

    private function str_query()
    {

        $salt = time();
        $sign = md5($this->appid. $this->text . $salt . $this->key);

        // 构建请求参数
        $query = http_build_query([
            "q"     =>  $this->text,
            "from"  => "zh",
            "to"    => "en",
            "appid" => $this->appid,
            "salt"  => $salt,
            "sign"  => $sign,
        ]);

        return $this->api.$query;
    }

}
