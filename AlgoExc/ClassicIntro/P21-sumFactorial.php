<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2019-1-23
 * Time: 21:19
 *
 * 输入n, 计算 S = 1! + 2! + ... n! 的末六位（不含前导0）。
 * n < 10^6
 * n! 表示阶乘， 是前n个正整数之积
 * 样例输入：10
 * 样例输出：37913
 *
 */

define('MOD', 1000000);
define('RES', [1, 3, 9, 33, 153, 873, 5913, 46299, 409113, 37913, 954713, 956313, 977113,
    268313, 636313, 524313, 620313, 348313, 180313, 820313, 260313, 940313, 580313, 940313]);

//fscanf(STDIN, "%d", $num);

//time
for ($num = 1; $num <= 10000; $num*=10) {
    echo PHP_EOL . "num: $num:" . PHP_EOL;
    f1(5);
    for ($fname = 2; $fname <= 6; $fname++) {

        $func_str = 'f' . $fname;
        $start_time = microtime(true);
        $func_str($num);
        $end_time = microtime(true);

        $time = 1000 * ($end_time - $start_time);
        echo "$func_str -- time:$time ms" . PHP_EOL;
    }
}

/** with bug  eg: input 100
 * @param int $n
 * @return int
 */

function f1(int $n): int
{
    for ($fnum = 1, $sum = 0; $fnum <= $n; $fnum++) {
        for ($multi_num = 1, $fa_res = 1; $multi_num <= $fnum; $multi_num++) {
            $fa_res *= $multi_num;// 计算 fnum!
        }

        $sum += $fa_res; // 求和 重复到n
    }
    //输出sum值以及类型
//    echo 'sum-type:' . gettype($sum) . PHP_EOL;
//    echo 'sum:' . $sum . PHP_EOL;
    //输出data值以及类型
    $data = $sum % 1000000;
//    echo 'data-type:' . gettype($data) . PHP_EOL;
//    echo 'data:' . $data . PHP_EOL;
    return $data;
}


/** 提前取mod
 * @param int $n
 * @return int
 */
function f2(int $n): int
{
    for ($fnum = 1, $sum = 0; $fnum <= $n; $fnum++) {
        for ($multi_num = 1, $fa_res = 1; $multi_num <= $fnum; $multi_num++) {
            $fa_res = ($fa_res * $multi_num) % MOD;// 计算 fnum!
        }

        $sum = ($sum + $fa_res) % MOD; // 求和 重复到n 提前取mod
//        echo $fnum . '! mod = ' . $fa_res . PHP_EOL;
//        echo 'sum-mod:'.$sum . PHP_EOL;
    }
    return $sum;

}


/** 改进大数情况
 * @param int $n
 * @return int
 */
function f3(int $n): int
{
    if ($n > 24) $n = 24;//发现的规律

    for ($fnum = 1, $sum = 0; $fnum <= $n; $fnum++) {
        for ($multi_num = 1, $fa_res = 1; $multi_num <= $fnum; $multi_num++) {
            $fa_res = ($fa_res * $multi_num) % MOD;// 计算 fnum!
        }

        $sum = ($sum + $fa_res) % MOD; // 求和 重复到n
//        echo $fnum . '! mod = ' . $fa_res . PHP_EOL;
//        echo 'sum-mod:'.$sum . PHP_EOL;

    }
    return $sum;

}

/** 改进大数 保存中途结果减少循环
 * @param int $n
 * @return int
 */
function f4(int $n): int
{
    if ($n > 24) $n = 24;//发现的规律

    $fa_res = [];
    $fa_res[] = 1;

    for ($fnum = 1, $sum = 0; $fnum <= $n; $fnum++) {

        if (empty($fa_res[$fnum])) {
            $fa_res[$fnum] = ($fnum * $fa_res[$fnum - 1]) % MOD; // 利用上一次的保存结果
        }

        $sum = ($sum + $fa_res[$fnum]) % MOD; // 求和 重复到n
    }

    return $sum;

}

/** 用变量保存中间结果 + 取mod优化
 * @param int $n
 * @return int
 */
function f5(int $n): int
{
    if ($n > 24) $n = 24;//发现的规律
    $fa_res = 1;
    for ($fnum = 1, $sum = 0; $fnum <= $n; $fnum++) {
        if ($n > 20 && $sum > 999999) { //如果大数临界 并且sum是超过6位了 才有必要进行取余
            $fa_res = ($fnum * $fa_res) % MOD;
            $sum = ($sum + $fa_res) % MOD;
        } else {
            $fa_res = $fnum * $fa_res; // 利用上一次的保存结果
            $sum = $sum + $fa_res; // 求和 重复到n
        }
    }
    return $sum > 999999 ? $sum % MOD : $sum;
}

/** 直接读取结果
 * @param int $n
 * @return int
 */

function f6(int $n): int
{
    if ($n > 24) $n = 24;
    return RES[$n-1];
}





