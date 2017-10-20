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
use Mrgoon\Iot\Request\V20170420\ApplyDeviceWithNamesRequest;
use Mrgoon\Iot\Request\V20170420\BatchGetDeviceStateRequest;
use Mrgoon\Iot\Request\V20170420\CreateProductRequest;
use Mrgoon\Iot\Request\V20170420\GetDeviceShadowRequest;
use Mrgoon\Iot\Request\V20170420\PubBroadcastRequest;
use Mrgoon\Iot\Request\V20170420\PubRequest;
use Mrgoon\Iot\Request\V20170420\QueryApplyStatusRequest;
use Mrgoon\Iot\Request\V20170420\QueryDeviceByNameRequest;
use Mrgoon\Iot\Request\V20170420\QueryDeviceRequest;
use Mrgoon\Iot\Request\V20170420\QueryPageByApplyIdRequest;
use Mrgoon\Iot\Request\V20170420\RegistDeviceRequest;
use Mrgoon\Iot\Request\V20170420\RRpcRequest;
use Mrgoon\Iot\Request\V20170420\UpdateDeviceShadowRequest;
use Mrgoon\Iot\Request\V20170420\UpdateProductRequest;

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

    /**
     * create product
     * @param $catId
     * @param $desc
     * @param $name
     * @return mixed
        {
        RequestId:8AE93DAB-958F-49BD-BE45-41353C6DEBCE,
        Success:true,
        ProductInfo:{
            ProductKey:...,
            CatId:56000,
            ProductName:工业产品
            }
        }
     */
    public function createProduct($catId, $desc, $name)
    {
        $request = new CreateProductRequest();
        $request->setCatId($catId);
        $request->setDesc($desc);
        $request->setName($name);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * update product with product key
     * @param $productKey
     * @param null $catId
     * @param null $productName
     * @param null $productDesc
     * @return mixed
        {
            "RequestId":"C4FDA54C-4201-487F-92E9-022F42387458",
            "Success":true,
        }
     */
    public function updateProduct($productKey, $catId = null, $productName = null, $productDesc = null)
    {
        $request = new UpdateProductRequest();
        $request->setProductKey($productKey);
        if (trim($catId)) {
            $request->setCatId($catId);
        }

        if (trim($productName)) {
            $request->setProductName($productName);
        }

        if (trim($productDesc)) {
            $request->setProductDesc($productDesc);
        }

        return $this->_client->getAcsResponse($request);
    }

    /**
     * query device list belong to the product
     * @param $productKey
     * @param $currentPage
     * @param $pageSize
     * @return mixed
        {
            PageCount:1,
            Data:{
                DeviceInfo:[
                    {
                        DeviceId:...,
                        DeviceName:...,
                        ProductKey:...,
                        DeviceSecret:...,
                        GmtCreate:Thu, 17-Nov-2016 02:08:12 GMT,
                        GmtModified:Thu, 17-Nov-2016 02:08:12 GMT
                    }
                ]
            },
            PageSize:10,
            Page:1,
            Total:9
            RequestId:06DC77A0-4622-42DB-9EE0-26A6E1FA08D3,
            Success:true,
        }
     */
    public function queryDevice($productKey, $currentPage, $pageSize)
    {
        $request = new QueryDeviceRequest();
        $request->setProductKey($productKey);
        $request->setCurrentPage($currentPage);
        $request->setPageSize($pageSize);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * register device with product key and device name
     * device name can be empty then system will set it as same as device id
     * @param $productKey
     * @param null $deviceName
     * @return mixed
        {
            "RequestId":"120F5EB3-7023-4F0C-B419-9303AB336885",
            "Success":true
            "DeviceId":"", //阿里云颁发的设备id 全局唯一
            "DeviceName":"",//设备名称，用户自定义或系统生成
            "DeviceSecret":"",//设备私钥
            "DeviceStatus":"",//预留状态字段
            "ErrorMessage":""//错误信息
        }
     */
    public function registerDevice($productKey, $deviceName = null)
    {
        $request = new RegistDeviceRequest();
        $request->setProductKey($productKey);
        $request->setDeviceName($deviceName);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * register a list of device
     * @param $productKey
     * @param array $deviceNames
     * @return mixed
        {
            "RequestId":"BB71E443-4447-4024-A000-EDE09922891E",
            "Success":true,
            "ApplyID":68
        }
     */
    public function applyDevices($productKey, Array $deviceNames)
    {
        $request = new ApplyDeviceWithNamesRequest();
        $request->setProductKey($productKey);
        $request->setDeviceNames($deviceNames);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * query the status that applied devices by apply id
     * @param $applyId
     * @return mixed
        {
            "RequestId":"BB71E443-4447-4024-A000-EDE09922891E",
            "Success":true,
            "Finish":true
        }
     */
    public function queryApplyStatus($applyId)
    {
        $request = new QueryApplyStatusRequest();
        $request->setApplyId($applyId);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * @param $applyId
     * @param $pageSize
     * @param $currentPage
     * @return mixed
        {
            "RequestId":"BB71E443-4447-4024-A000-EDE09922891E",
            "Success":true,
            Page:1,
            PageSize:10,
            PageCount:1,
            Total:4,
            ApplyDeviceList:{
                ApplyDeviceInfo:[
                    {DeviceId:...,DeviceName:...,DeviceSecret:...},
                    {DeviceId:...,DeviceName:...,DeviceSecret:...}
                ]
            }
        }
     */
    public function queryPageByAppId($applyId, $pageSize, $currentPage)
    {
        $request = new QueryPageByApplyIdRequest();
        $request->setApplyId($applyId);
        $request->setPageSize($pageSize);
        $request->setCurrentPage($currentPage);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * query device status by device name
     * @param $productKey
     * @param $deviceName
     * @return mixed
        {
            RequestId:07C19236-7CFC-4BF9-99AD-EA6054092FDB,
            DeviceInfo:{
                DeviceId:...,
                DeviceName:...,
                ProductKey:...,
                DeviceSecret:...,
                GmtCreate:Thu, 17-Nov-2016 02:08:12 GMT,
                GmtModified:Thu, 17-Nov-2016 02:08:12 GMT
            },
            Success:true
        }
     */
    public function queryDeviceByName($productKey, $deviceName)
    {
        $request = new QueryDeviceByNameRequest();
        $request->setProductKey($productKey);
        $request->setDeviceName($deviceName);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * query the list of device status by device names
     * @param $productKey
     * @param array $deviceNames
     * @return mixed
        {
            DeviceStatusList:{
                DeviceStatus:[
                    {Status:UNACTIVE, DeviceName:...},
                    {Status:UNACTIVE, DeviceName:...}
                ]
            },
            RequestId:"1A540BD7-176C-42D4-B3C0-A2C549DD00A3",
            Success:true
        }
     */
    public function queryDeviceByNames($productKey, Array $deviceNames)
    {
        $request = new BatchGetDeviceStateRequest();
        $request->setProductKey($productKey);
        $request->setDeviceName($deviceNames);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * publish message to topic
     * @param $productKey
     * @param $topicFullName
     * @param $content | must base64
     * @param int $qos | default level 0, can be 0 or 1
     * @return mixed
        {
            "RequestId":"BB71E443-4447-4024-A000-EDE09922891E",
            "Success":true,
            "MessageId":889455942124347329
        }
     */
    public function pub($productKey, $topicFullName, $content, $qos = 0)
    {
        $request = new PubRequest();
        $request->setProductKey($productKey);
        $request->setTopicFullName($topicFullName);
        $request->setMessageContent($content);
        $request->setQos($qos);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * send message to device and get sync response
     * @param $productKey
     * @param $deviceName
     * @param $content | must encode by base64
     * @param $timeout
     * @return mixed
        {
            "RequestId":"41C4265E-F05D-4E2E-AB09-E031F501AF7F",
            "Success":true,
            "RrpcCode":"SUCCESS",
            "PayloadBase64Byte":"d29ybGQgaGVsbG8="
            "MessageId":889455942124347392
        }
     */
    public function rRpc($productKey, $deviceName, $content, $timeout)
    {
        $request = new RRpcRequest();
        $request->setProductKey($productKey);
        $request->setDeviceName($deviceName);
        $request->setRequestBase64Byte($content);
        $request->setTimeout($timeout);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * publish broadcast message to a topic
     * @param $productKey
     * @param $content
     * @param $topicFullName
     * @return mixed
        {
            "RequestId":"BB71E443-4447-4024-A000-EDE09922891E",
            "Success":true,
        }
     */
    public function pubBroadcast($productKey, $content, $topicFullName)
    {
        $request = new PubBroadcastRequest();
        $request->setProductKey($productKey);
        $request->setMessageContent($content);
        $request->setTopicFullName($topicFullName);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * get device shadow message by devive name
     * @param $productKey
     * @param $deviceName
     * @return mixed
        {
            "RequestId":"BB71E443-4447-4024-A000-EDE09922891E",
            "Success":true,
            "ShadowMessage":{...}
        }
     */
    public function getDeviceShadow($productKey, $deviceName)
    {
        $request = new GetDeviceShadowRequest();
        $request->setProductKey($productKey);
        $request->setDeviceName($deviceName);
        return $this->_client->getAcsResponse($request);
    }

    /**
     * update device shadowMessage
     * @param $productKey
     * @param $deviceName
     * @param array $shadowMessage
        {
            "RequestId":"BB71E443-4447-4024-A000-EDE09922891E",
            "Success":true
        }
     */
    public function updateDeviceShadow($productKey, $deviceName, Array $shadowMessage)
    {
        $request = new UpdateDeviceShadowRequest();
        $request->setProductKey($productKey);
        $request->setDeviceName($deviceName);
        $request->setShadowMessage(json_encode($shadowMessage, JSON_UNESCAPED_UNICODE));
    }
}