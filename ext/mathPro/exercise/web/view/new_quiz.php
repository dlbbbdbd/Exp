<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2019-2-16
 * Time: 15:15
 */

require '../func/pm.php';
require '../func/check.php';
require '../func/quiz.php';

require '../db/Db.php';
require '../db/Answer.php';
require '../db/Option.php';
require '../db/Question.php';
require '../db/Quiz.php';
require '../db/Submit.php';
require '../db/User.php';

require '../config/params.php';
require '../config/db.php';
require '../config/Medoo.php';

//跳转相关参数
$is_end = false;
$second = 0;
$next_url = '';

try {

    //todo 0 拿参数 参数校验

    $uid = $_GET['uid'];//拿到
    $unit = $_GET['unit'];//拿到

//    $url = "./new_quiz.php?uid=$uid";
    $url = "./new_quiz.php?uid=$uid&unit=$unit";

    $qids = $_GET['qids'] ?? null;// 用户拿过来的题目id数组

    $answers = [];// 可能用循环-- 很多题的答案
    $size = empty($qids) ? 0 : sizeof($qids);
    for ($i = 0; $i < $size; $i++) {
        $input_name = 'Q' . (string)$i . 'Answer';
        if (!isset($_POST[$input_name])) {
            break;
        }
        $answers[] = $_POST[$input_name];
    }


    //todo 1 用uid 找 quiz-id
    $quiz_id = getQuizid($uid);

    //todo 2 if 有答案 --> 保存答案（里面会记录 判断对错 更新diff）-- 拿到了diff
    //todo 有如果有答案
    if (!empty($answers)) {
        //遍历数组里的每一个值 拿进来用
        for ($i = 0; $i < QUIZ_EACH_TIMES_QUESTION; $i++) {
            $qid = $qids[$i];
            $ans = $answers[$i];
            //一题题保存
            saveCurrentResult($uid, $qid, $ans, $quiz_id);
        }//保存完了

        //保存完 -》更新diff 拿到最新的diff
        $diff = updateDiff($uid, $quiz_id);
    } else {
        //如果没答案 第一次create的 取默认值
        $diff = getDiff($quiz_id);
    }

    //todo 3 判断结束（用diff、看次数） --> 结束就跳出去

    if ($diff == QUIZ_MAX_DIFF_LEVEL || isQuizOver($quiz_id)) {
        //结束quiz
        endAQuiz($quiz_id);
        //todo 跳
//        echo 'jump';
        $is_end = true;
        $second = 0;
        $next_url = "./quiz_result.php?uid=$uid&quiz_id=$quiz_id";

    }

    //todo 4 用diff出题
//    $questions = takeSCQuiz($diff);
    $questions = takeSCQuizWithUnit($diff, $unit);

    //todo 5 题目数据保存好 下面放到 html 展示
//    $url = "./new_quiz.php?uid=$uid";
    foreach ($questions as $key => $q) {
        $url = $url . '&qids[' . $key . ']=' . $q['qid'];
    }

} catch (Exception $e) {
    echo $e->getMessage();
    //todo 报错 --可能是跳转 可能重新来 输出报错结果
}


function jump(bool $is_end, int $second, string $url)
{
    if ($is_end == true) {
        echo '<meta http-equiv="refresh" content="' . $second . ';url=\'' . $url . '\'">';
    }
}

/** 输出问题和选项的html代码
 * @param $questions
 */
function echoQuestionContent($questions)
{

    if (!is_array($questions) || sizeof($questions) != QUIZ_EACH_TIMES_QUESTION) {
        //输出错误
        echo 'ERROR';
    } else {
        //输出html代码
        foreach ($questions as $key => $q) {
            echo '<br/>';

            if (strpos(trim($q['content']), 'png') !== false) {
                $ques_content_html = '<img src=' . ' "' . $q['content'] . '" ' . 'alt="" />';

            }else{
                $ques_content_html = $q['content'];
            }


            echo '<p class="main-form">' . $ques_content_html . '<br/></p>';//输出题目

            $optionArr = $q['optionArray'];
            foreach ($optionArr as $o) { //循环输出某题的n个选项
                //<label>
                //   <input name="Fruit" type="radio" value="A" /> A.苹果
                // </label>

                $input_name = 'Q' . (string)$key . 'Answer';
                $input_value = $o['key'];

                if (strpos(trim($o['content']), 'png') !== false) {
                    //有 说明是个图片
                    $input_text = '<img src=' . ' "' . $o['content'] . '" ' . 'alt="" />';
//                            <img src="/i/eg_tulip.jpg"  alt="上海鲜花港 - 郁金香" />
                } else {
                    $input_text = $o['key'] . '. ' . $o['content'];
                }
                $checked = ($o['key'] == 'A') ? 'checked' : '';//默认选A

                $label_tag =
                    '<label><input name="' . $input_name
                    . '" type="radio" value="' . $input_value . '" ' . $checked . ' />'
                    . $input_text . ' </label>';
                echo $label_tag;
            }
            echo '<br/>';
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php
    jump($is_end, $second, $next_url);
    ?>
    <title>答题页面</title>
    <link rel="stylesheet" type="text/css" href="../css/quiz.css"/>
</head>
<body>
<div class="outer-wrap">
    <div class="main-panel">

        <form class="main-form" action=" <?php echo $url ?? null ?>" method="post">
            <h3>THE QUIZ</h3>
            <hr/>
            <?php
            //            echo $url;
            $q = $questions ?? null;
            echoQuestionContent($q);
            ?>
            <br/>
            <input class="main-submit-input" type="submit" value="Submit"/>
        </form>


    </div>
</div>
</body>
</html>
