<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/8/17
 * Time: 下午1:58
 */

require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\ActivationQuery;

class ActivationForSmsInquiry extends ActivationQuery
{
    public function main()
    {
        $this->setInquiryIndex();
        echo "-----------> start updateSendOnValue \n";
        $this->updateSendOnValue();
        
    }
}
$inquiry = new ActivationForSmsInquiry();
$inquiry->main();