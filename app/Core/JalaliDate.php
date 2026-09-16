<?php
declare(strict_types=1);
namespace Fandoogh\Core;
defined('ABSPATH') || exit;

/**
 * Dependency-free Jalali conversion for plugin-owned dates.
 *
 * Database timestamps stay UTC Gregorian. User-entered days are interpreted in
 * the WordPress timezone before their UTC query bounds are calculated.
 */
final class JalaliDate
{
    private const BREAKS = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];

    /** @return array{year:int,month:int,day:int,normalized:string} */
    public static function parse(string $value): array
    {
        $value = self::asciiDigits(trim($value));
        if (!preg_match('/^([0-9]{4})([-\/])([0-9]{2})\2([0-9]{2})$/D', $value, $matches)) {
            throw new \InvalidArgumentException('Invalid Jalali date format.');
        }
        $year = (int) $matches[1];
        $month = (int) $matches[3];
        $day = (int) $matches[4];
        if (!self::isValid($year, $month, $day)) throw new \InvalidArgumentException('Invalid Jalali date.');
        return ['year'=>$year, 'month'=>$month, 'day'=>$day, 'normalized'=>sprintf('%04d/%02d/%02d', $year, $month, $day)];
    }

    /** @return array{year:int,month:int,day:int} */
    public static function toGregorian(int $year, int $month, int $day): array
    {
        if (!self::isValid($year, $month, $day)) throw new \InvalidArgumentException('Invalid Jalali date.');
        return self::dayNumberToGregorian(self::jalaliToDayNumber($year, $month, $day));
    }

    /** @return array{year:int,month:int,day:int} */
    public static function fromGregorian(int $year, int $month, int $day): array
    {
        if (!checkdate($month, $day, $year)) throw new \InvalidArgumentException('Invalid Gregorian date.');
        return self::dayNumberToJalali(self::gregorianToDayNumber($year, $month, $day));
    }

    /** @return array{normalized:string,start:string,end:string} UTC database timestamps. */
    public static function utcDayBounds(string $value, ?\DateTimeZone $timezone = null): array
    {
        $date = self::parse($value);
        $gregorian = self::toGregorian($date['year'], $date['month'], $date['day']);
        $timezone ??= self::wordpressTimezone();
        $localStart = new \DateTimeImmutable(sprintf('%04d-%02d-%02d 00:00:00', $gregorian['year'], $gregorian['month'], $gregorian['day']), $timezone);
        $utc = new \DateTimeZone('UTC');
        return [
            'normalized'=>$date['normalized'],
            'start'=>$localStart->setTimezone($utc)->format('Y-m-d H:i:s'),
            'end'=>$localStart->modify('+1 day')->modify('-1 second')->setTimezone($utc)->format('Y-m-d H:i:s'),
        ];
    }

    public static function format(\DateTimeInterface $instant, ?\DateTimeZone $timezone = null, bool $withTime = false): string
    {
        $timezone ??= self::wordpressTimezone();
        $local = \DateTimeImmutable::createFromInterface($instant)->setTimezone($timezone);
        $date = self::fromGregorian((int) $local->format('Y'), (int) $local->format('n'), (int) $local->format('j'));
        $formatted = sprintf('%04d/%02d/%02d', $date['year'], $date['month'], $date['day']);
        return $withTime ? $formatted . ' ' . $local->format('H:i') : $formatted;
    }

    public static function formatUtc(string $timestamp, bool $withTime = true, ?\DateTimeZone $timezone = null): string
    {
        $utc = \DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $timestamp, new \DateTimeZone('UTC'));
        $errors = \DateTimeImmutable::getLastErrors();
        if (!$utc || (is_array($errors) && ($errors['warning_count'] || $errors['error_count'])) || $utc->format('Y-m-d H:i:s') !== $timestamp) {
            throw new \InvalidArgumentException('Invalid UTC timestamp.');
        }
        return self::format($utc, $timezone, $withTime);
    }

    public static function currentYear(?\DateTimeImmutable $now = null, ?\DateTimeZone $timezone = null): int
    {
        $now ??= new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        return (int) substr(self::format($now, $timezone), 0, 4);
    }

    public static function isLeapYear(int $year): bool
    {
        return $year >= self::BREAKS[0] && $year < self::BREAKS[count(self::BREAKS) - 1] && self::jalaliCalendar($year)['leap'] === 0;
    }

    private static function isValid(int $year, int $month, int $day): bool
    {
        if ($year < 1000 || $year >= self::BREAKS[count(self::BREAKS) - 1] || $month < 1 || $month > 12 || $day < 1) return false;
        $maximum = $month <= 6 ? 31 : ($month <= 11 ? 30 : (self::isLeapYear($year) ? 30 : 29));
        return $day <= $maximum;
    }

    private static function asciiDigits(string $value): string
    {
        return strtr($value, [
            '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
        ]);
    }

    private static function wordpressTimezone(): \DateTimeZone
    {
        if (function_exists('wp_timezone')) return \wp_timezone();
        $name = function_exists('wp_timezone_string') ? \wp_timezone_string() : 'UTC';
        return new \DateTimeZone($name !== '' ? $name : 'UTC');
    }

    /** @return array{leap:int,gy:int,march:int} */
    private static function jalaliCalendar(int $year): array
    {
        $breaks = self::BREAKS;
        if ($year < $breaks[0] || $year >= $breaks[count($breaks) - 1]) throw new \InvalidArgumentException('Jalali year is out of range.');
        $gregorianYear = $year + 621;
        $leapJalali = -14;
        $previous = $breaks[0];
        $jump = 0;
        for ($i = 1, $length = count($breaks); $i < $length; $i++) {
            $current = $breaks[$i];
            $jump = $current - $previous;
            if ($year < $current) break;
            $leapJalali += self::div($jump, 33) * 8 + self::div(self::mod($jump, 33), 4);
            $previous = $current;
        }
        $years = $year - $previous;
        $leapJalali += self::div($years, 33) * 8 + self::div(self::mod($years, 33) + 3, 4);
        if (self::mod($jump, 33) === 4 && $jump - $years === 4) $leapJalali++;
        $leapGregorian = self::div($gregorianYear, 4) - self::div((self::div($gregorianYear, 100) + 1) * 3, 4) - 150;
        $march = 20 + $leapJalali - $leapGregorian;
        if ($jump - $years < 6) $years = $years - $jump + self::div($jump + 4, 33) * 33;
        $leap = self::mod(self::mod($years + 1, 33) - 1, 4);
        if ($leap === -1) $leap = 4;
        return ['leap'=>$leap, 'gy'=>$gregorianYear, 'march'=>$march];
    }

    private static function jalaliToDayNumber(int $year, int $month, int $day): int
    {
        $calendar = self::jalaliCalendar($year);
        return self::gregorianToDayNumber($calendar['gy'], 3, $calendar['march']) + ($month - 1) * 31 - self::div($month, 7) * ($month - 7) + $day - 1;
    }

    /** @return array{year:int,month:int,day:int} */
    private static function dayNumberToJalali(int $dayNumber): array
    {
        $gregorian = self::dayNumberToGregorian($dayNumber);
        $year = $gregorian['year'] - 621;
        $calendar = self::jalaliCalendar($year);
        $first = self::gregorianToDayNumber($gregorian['year'], 3, $calendar['march']);
        $offset = $dayNumber - $first;
        if ($offset >= 0) {
            if ($offset <= 185) return ['year'=>$year, 'month'=>1 + self::div($offset, 31), 'day'=>self::mod($offset, 31) + 1];
            $offset -= 186;
        } else {
            $year--;
            $offset += 179;
            if ($calendar['leap'] === 1) $offset++;
        }
        return ['year'=>$year, 'month'=>7 + self::div($offset, 30), 'day'=>self::mod($offset, 30) + 1];
    }

    private static function gregorianToDayNumber(int $year, int $month, int $day): int
    {
        $number = self::div(($year + self::div($month - 8, 6) + 100100) * 1461, 4) + self::div(153 * self::mod($month + 9, 12) + 2, 5) + $day - 34840408;
        return $number - self::div(self::div($year + 100100 + self::div($month - 8, 6), 100) * 3, 4) + 752;
    }

    /** @return array{year:int,month:int,day:int} */
    private static function dayNumberToGregorian(int $dayNumber): array
    {
        $j = 4 * $dayNumber + 139361631;
        $j = $j + self::div(self::div(4 * $dayNumber + 183187720, 146097) * 3, 4) * 4 - 3908;
        $i = self::div(self::mod($j, 1461), 4) * 5 + 308;
        $day = self::div(self::mod($i, 153), 5) + 1;
        $month = self::mod(self::div($i, 153), 12) + 1;
        $year = self::div($j, 1461) - 100100 + self::div(8 - $month, 6);
        return ['year'=>$year, 'month'=>$month, 'day'=>$day];
    }

    private static function div(int $left, int $right): int { return intdiv($left, $right); }
    private static function mod(int $left, int $right): int { return $left - intdiv($left, $right) * $right; }
}
