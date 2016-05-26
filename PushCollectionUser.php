<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/26
 * Time: 下午6:12
 */
require __DIR__. '/Bootstrap.php';

use MiddlewareSpace\QueryUnRegisterCollection;

class PushCollectionUser extends QueryUnRegisterCollection
{
    public function insertPointUserInfo()
    {
        $this->getPointPackUnRegisters();
    }

    public function showDiagramsForCollection()
    {
        $rankAge['18 ~ 21'] = $this->showFollowersCollection(18, 21);
        $rankAge['22 ~ 25'] = $this->showFollowersCollection(22, 25);
        $rankAge['26 ~ 29'] = $this->showFollowersCollection(26, 29);
        $rankAge['30 ~ 35'] = $this->showFollowersCollection(30, 35);

        foreach ($rankAge as $ageRank => $ageCollections) {
            foreach ($ageCollections as $followers => $count) {
                echo sprintf("%s;%s;%d\n", $ageRank, $followers, $count);
            }
        }

    }

    public function showFollowersCollection($minAge = 0, $maxAge = 0)
    {
        $param['minAge'] = $minAge;
        $param['maxAge'] = $maxAge;
        $param['minFc'] = 6;
        $param['maxFc'] = 9;
        $rankCollection['0609'] = $this->queryCollectionItems($param);
        $param['minFc'] = 10;
        $param['maxFc'] = 19;
        $rankCollection['1019'] = $this->queryCollectionItems($param);
        $param['minFc'] = 20;
        $param['maxFc'] = 1000;
        $rankCollection['2000'] = $this->queryCollectionItems($param);
        return $rankCollection;
    }
}
$pushCollection = new PushCollectionUser();
$pushCollection->insertPointUserInfo();
//$pushCollection->showDiagramsForCollection();