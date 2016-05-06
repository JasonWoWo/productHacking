<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/6
 * Time: 下午7:07
 */
include __DIR__ ."/middleware/Authorize.php";
class DeviceAuthorize extends Authorize
{
    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }
    
    public function deviceAuthorizeMain()
    {
        echo "----current: " . date('Y-m-d H:i:s') . "------> start \n ";
        echo "------------> start 0 --------> \n";
        $current = new \DateTime();
        $current->modify('-1 days');
        echo "||||| 0======= fetch_new_device ------> \n";
        $this->fetch_new_device($current->getTimestamp());
        echo "||||| 1======= getMongoAuthorize ------> \n";
        $this->getMongoAuthorize();
        echo "||||| 2======= getAuthorize_class ------> \n";
        $this->getAuthorize();
        echo "||||| 3======= printData ------> \n";
        $this->printData();
        echo "----current: " . date('Y-m-d H:i:s') . "------> end  \n ";
    }
}
$device = new DeviceAuthorize();
$device->deviceAuthorizeMain();