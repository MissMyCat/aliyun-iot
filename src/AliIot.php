<?php
/**
 * Created by PhpStorm.
 * User: Mr_GooN
 * Date: 2017/10/19
 * Time: 14:08
 */
namespace Mrgoon\AliIot;
use Mrgoon\AliyunSmsSdk\Autoload;
use Mrgoon\AliyunSmsSdk\DefaultAcsClient;
use Mrgoon\AliyunSmsSdk\Profile\DefaultProfile;
use Mrgoon\Iot\Request\V20170420\QueryDeviceRequest;

class AliIot {
    private $_accessKey = '';
    private $_accessSecret = '';
    private $_client = '';

    public function __construct($accessKey, $accessSecret)
    {
        Autoload::config();
        $this->_accessKey = $accessKey;
        $this->_accessSecret = $accessSecret;
        $iClientProfile = DefaultProfile::getProfile("cn-shanghai", $this->_accessKey, $this->_accessSecret);
        $this->_client = new DefaultAcsClient($iClientProfile);
    }

    public function queryDevice($productKey, $currentPage, $pageSize)
    {
        $request = new QueryDeviceRequest();
        $request->setProductKey($productKey);
        $request->setCurrentPage($currentPage);
        $request->setPageSize($pageSize);
        return $this->_client->getAcsResponse($request);
    }

}