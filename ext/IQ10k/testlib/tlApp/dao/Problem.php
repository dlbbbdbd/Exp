<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-7-23
 * Time: 13:34
 */

namespace tlApp\dao;

use \Exception;

class Problem extends BaseDao
{
    protected $table = DB_PREFIX . "_problem";


    /**
     * @param array $problem_info
     * @param bool $ret_id
     * @return int|null|string
     * @throws Exception
     */
    public function insert(Array $problem_info, $ret_id = false)
    {

//        problem_info = compact($problem, $option_num, $options,$answers,$language, $classification, $pro_type, $proSource, $hint);

        //插入 问题主体
        $pdo = $this->database->insert($this->table, [
            'problem' => $problem_info['problem'],
            'option_num' => $problem_info['option_num'],
            'options' => $problem_info['options'],
            'answers' => $problem_info['answers'],
            'language' => $problem_info['language'],
            'classification' => $problem_info['classification'],
            'pro_type' => $problem_info['pro_type'],
            'pro_source' => $problem_info['pro_source'],
            'time' => date('Y-m-d H:i:s'),
            'lastest' => date('Y-m-d H:i:s'),
            'total' => 0,
            'visible' => 1
        ]);


        $pid = $this->database->id();
        if (!is_numeric($pid) || $pid < 1) {
            throw new Exception(__FUNCTION__ . ' pid error', 500);
        }

        //插入 问题提示
        $table_hint = DB_PREFIX.'_hint';
        $pdo = $this->database->insert($table_hint, [
            'pid'=>$pid,
            'hint'=>$problem_info['hint'],
            'visible' => 1

        ]);

        $hid = $this->database->id();
        if (!is_numeric($hid) || $hid < 1) {
            throw new Exception(__FUNCTION__ . ' hid error', 500);
        }

        return $ret_id === false ? null : $pid;
    }

}