<?php

namespace Ali;

use anlutro\cURL\cURL;
use function Couchbase\defaultDecoder;

class aliSms
{
    /**
     * 阿里云一定要使用UTC时间
     */
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';

    protected $AccessSecret     = '';
    protected $Signature        = '';

    /**
     * @param string $AccessSecret
     * @return aliSms
     */
    public function setAccessSecret(string $AccessSecret): aliSms
    {
        $this->AccessSecret = $AccessSecret;
        return $this;
    }
    protected $param            = '';

    protected $RequestMethod    = 'GET';
    protected $Action           = 'SendSms';
    protected $AccessKeyId      = '';
    protected $Format           = 'json';
    protected $RegionId         = '';
    protected $SignatureMethod  = 'HMAC-SHA1';
    protected $SignatureNonce   = '';
    protected $SignatureVersion = '1.0';
    protected $Timestamp        = '';
    protected $Version          = '2017-05-25';
    protected $RequestUrl       = 'http://dysmsapi.aliyuncs.com';

    protected $PhoneNumbers     = '';
    protected $TemplateCode     = '';
    protected $TemplateParam    = '';

    protected $SignName         = '';

    /**
     * @var string
     */
    protected $RequestStr;


    public function __construct()
    {

        $this->setTimestamp();
        $this->setSignatureNonce();
    }



    public function send($phone,$param,$code,$autograph){

        $this->PhoneNumbers = $phone;
        $this->TemplateCode = $code;
        $this->TemplateParam= json_encode(["code"=>"$param"]);
        $this->SignName     = $autograph;
        $curl = new cURL();
        $back = $curl->get($str = "{$this->RequestUrl}?Signature={$this->sign()}&{$this->RequestStr}");



        return $back->body;


    }

    private function curl(){
        $curl = new cURL();

    }



    /**
     * @param string $Action
     * @return aliSms
     */
    public function setAction(string $Action): aliSms
    {
        $this->Action = $Action;
        return $this;
    }


    /**
     * @param string $AccessKeyId
     * @return aliSms
     */
    public function setAccessKeyId(string $AccessKeyId): aliSms
    {
        $this->AccessKeyId = $AccessKeyId;
        return $this;
    }

    /**
     * @param string $Format
     * @return aliSms
     */
    public function setFormat(string $Format): aliSms
    {
        $this->Format = $Format;
        return $this;
    }

    /**
     * @param string $RegionId
     * @return aliSms
     */
    public function setRegionId(string $RegionId): aliSms
    {
        $this->RegionId = $RegionId;
        return $this;
    }

    /**
     * @param string $SignatureMethod
     * @return aliSms
     */
    public function setSignatureMethod(string $SignatureMethod): aliSms
    {
        $this->SignatureMethod = $SignatureMethod;
        return $this;
    }

    /**
     * @param string $SignatureNonce
     * @return aliSms
     */
    public function setSignatureNonce(string $SignatureNonce = null): aliSms
    {
        if($SignatureNonce == null)$SignatureNonce = uniqid();
        $this->SignatureNonce = $SignatureNonce;
        return $this;
    }

    /**
     * @param string $SignatureVersion
     * @return aliSms
     */
    public function setSignatureVersion(string $SignatureVersion): aliSms
    {
        $this->SignatureVersion = $SignatureVersion;
        return $this;
    }

    /**
     * @param string $Timestamp
     * @return aliSms
     */
    public function setTimestamp(string $Timestamp = null): aliSms
    {
        if($Timestamp == null) $Timestamp = (date('Y-m-d\TH:i:s\Z',time()));
        $this->Timestamp = $Timestamp;
        return $this;
    }

    /**
     * @param string $Version
     * @return aliSms
     */
    public function setVersion(string $Version): aliSms
    {
        $this->Version = $Version;
        return $this;
    }


    private function sign(): string
    {
        $this->param = [
            'Action'            => $this->Action,
            'AccessKeyId'       => $this->AccessKeyId,
            'Format'            => $this->Format,
            'SignatureMethod'   => $this->SignatureMethod,
            'SignatureNonce'    => $this->SignatureNonce,
            'SignatureVersion'  => $this->SignatureVersion,
            'Timestamp'         => $this->Timestamp,
            'Version'           => $this->Version,
            'PhoneNumbers'      => $this->PhoneNumbers,
            'TemplateCode'      => $this->TemplateCode,
            'TemplateParam'     => $this->TemplateParam,
            'SignName'          => $this->SignName,
        ];
        ksort($this->param);
        $queryStr   = '';
        foreach ($this->param as $key =>$val){
            $queryStr.= urlencode($key);
            $queryStr.= '=';
            $queryStr.= urlencode($val);
            $queryStr.= '&';
        }
        $this->RequestStr = rtrim($queryStr,'&');
        $signStr = rtrim($queryStr,'&');
        $signStr = strtoupper($this->RequestMethod).'&'.urlencode('/').'&'.urlencode($signStr);

        return urlencode(base64_encode(hash_hmac('sha1',$signStr,$this->AccessSecret.'&',true)));
    }


}