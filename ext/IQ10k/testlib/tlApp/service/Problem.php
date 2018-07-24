<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-7-23
 * Time: 13:24
 */
namespace tlApp\service;
use tlApp\model\Json;

class Problem extends BaseService
{

    /**
     * Problem constructor.
     */
    public function __construct()
    {
        $this->json = new Json();

    }

    /** 插入题目数据 然后返回json
     * @param array $problem_info
     * @return Json
     * @throws \Exception
     */
    public function post(Array $problem_info){
        //problem_info = compact($problem, $option_num, $language, $classification, $proType, $proSource, $hint);

        $dao = new \tlApp\dao\Problem();
        $pid = $dao->insert($problem_info,true);

        $retdata = (object)['pid'=>$pid];
        $this->json->setRetdata($retdata);

        return $this->json;
    }
}