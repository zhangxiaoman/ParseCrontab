<?php
/**
 * This is a simple script to parse crontab syntax to get the execution time
 * Eg.A:   $timestamp = Crontab::parse('12 * * * 1-5');
 * Eg.B:   $timestamp = Crontab::parse('30 12 * * * 1-5');
 *
 */

/**
 * Class Crontab
 */
class Crontab
{

    /**
     *  parse linux crontab & support sec
     *
     * @param string $crontab_string :
     *
     *      0    1    2    3    4    5
     *      *    *    *    *    *    *
     *      -    -    -    -    -    -
     *      |    |    |    |    |    |
     *      |    |    |    |    |    +----- day of week (0 - 6) (Sunday=0)
     *      |    |    |    |    +----- month (1 - 12)
     *      |    |    |    +------- day of month (1 - 31)
     *      |    |    +--------- hour (0 - 23)
     *      |    +----------- min (0 - 59)
     *      +------------- sec (0-59)
     * @param boolean $hide_past_sec 是否隐藏当前分钟内已经过去的 sec
     *
     * @return array second 当前分钟内执行是否需要执行任务,如果需要,则把需要在哪几秒执行返回
     *
     * @throws InvalidArgumentException 参数异常
     */
    static public function parse($crontab_string, $hide_past_sec = false)
    {
        if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i', trim($crontab_string))) {
            if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i', trim($crontab_string))) {
                throw new InvalidArgumentException("Invalid cron string: " . $crontab_string);
            }
        }
        $cron = preg_split("/[\s]+/i", trim($crontab_string));
        if (count($cron) == 5) {
            array_unshift($cron, 1);
        }
        $start = time();
        $date = array(
            'second'  => self::_parseCronNumbers($cron[0], 0, 59),
            'minutes' => self::_parseCronNumbers($cron[1], 0, 59),
            'hours'   => self::_parseCronNumbers($cron[2], 0, 23),
            'day'     => self::_parseCronNumbers($cron[3], 1, 31),
            'month'   => self::_parseCronNumbers($cron[4], 1, 12),
            'week'    => self::_parseCronNumbers($cron[5], 0, 6),
        );

        if (
            !in_array(intval(date('i', $start)), $date['minutes']) ||
            !in_array(intval(date('G', $start)), $date['hours']) ||
            !in_array(intval(date('j', $start)), $date['day']) ||
            !in_array(intval(date('w', $start)), $date['week']) ||
            !in_array(intval(date('n', $start)), $date['month'])

        ) {
            return array();
        }
        $currSec = date('s', $start);
        if (!$hide_past_sec) {
            return $date['second'];
        }
        foreach ($date['second'] as $k => $v) {
            if ($k > $currSec) {
                continue;
            }
            unset($date['second'][$k]);
        }

        return $date['second'];
    }

    /**
     * get a single cron style notation and parse it into numeric value
     *
     * @param string $s cron string element
     * @param int $min minimum possible value
     * @param int $max maximum possible value
     *
     * @return array
     */
    static protected function _parseCronNumbers($s, $min, $max)
    {
        $result = array();
        $v1 = explode(",", $s);
        foreach ($v1 as $v2) {
            $v3 = explode("/", $v2);
            $step = empty($v3[1]) ? 1 : $v3[1];
            $v4 = explode("-", $v3[0]);
            $_min = count($v4) == 2 ? $v4[0] : ($v3[0] == "*" ? $min : $v3[0]);
            $_max = count($v4) == 2 ? $v4[1] : ($v3[0] == "*" ? $max : $v3[0]);
            for ($i = $_min; $i <= $_max; $i += $step) {
                $result[$i] = intval($i);
            }
        }
        ksort($result);
        return $result;
    }
}