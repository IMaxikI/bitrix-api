<?php

declare(strict_types=1);

class Validator
{
    public function validateData($data): array
    {
        $mess = [];

        $this->validateString($data['name'], 'name', $mess);
        $this->validateString($data['second_name'], 'second_name', $mess);
        $this->validateString($data['last_name'], 'last_name', $mess);
        $this->validatePhone($data['phone'], 'phone', $mess);
        $this->validateDate($data['birthday'], 'birthday', $mess);

        return $mess;
    }

    private function validateString($str, $field, &$mess): void
    {
        $str = trim($str);

        if (strlen($str) < 2 || strlen($str) > 50) {
            $mess[$field] = 'Длина строки должна быть от 2 до 50 символов!';
            return;
        }

        if (!preg_match('/^[А-яёЁ]{1,50}$/u', $str)) {
            $mess[$field] = 'Допускается вводить только буквы кириллицы, без пробелов!';
        }
    }

    private function validateDate($date, $field, &$mess): void
    {
        $dateParts = explode('-', $date);
        if (count($dateParts) !== 3) {
            $mess[$field] = 'Недопустимая дата!';
            return;
        }

        $currentDate = new DateTime();
        $currentDate->setTime(0, 0, 0);

        $day = intval($dateParts[2]);
        $month = intval($dateParts[1]);
        $year = intval($dateParts[0]);

        if (!checkdate($month, $day, $year)) {
            $mess[$field] = 'Недопустимая дата!';
            return;
        }

        $maxDate = new DateTime();
        $maxDate->modify('-16 years');
        $minDate = new DateTime();
        $minDate->modify('-100 years');

        $dateToCheck = new DateTime("$year-$month-$day");
        if ($dateToCheck < $minDate || $dateToCheck > $maxDate || $dateToCheck > $currentDate) {
            $mess[$field] = 'Недопустимая дата!';
        }
    }

    private function validatePhone($phone, $field, &$mess): void
    {
        if (!preg_match('/^\+375\d{9}$/', $phone)) {
            $mess[$field] = 'Недопустимый номер телефона. Пример: +375123456789';
        }
    }
}