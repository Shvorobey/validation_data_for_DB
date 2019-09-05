<?php

    class Validator
    {
        public static function isEmpty($fieldName, $fieldValue)
        {
            if (empty($fieldValue)) {
                throw new Exception($fieldName." ERROR ");
            }
        }

        public static function isMinLength($fieldName, $fieldValue, $minValue)
        {
            $fieldValue = trim($fieldValue);
            if (strlen($fieldValue) < $minValue) {
                throw new Exception($fieldName.' '.str_replace('{count}', $minValue, FIELD_MIN_ERROR_MSG));
            }
            return true;
        }

        public static function isMaxLength($fieldName, $fieldValue, $maxValue)
        {
            $fieldValue = trim($fieldValue);
            if (strlen($fieldValue) > $maxValue) {
                throw new Exception($fieldName.' '.str_replace('{count}', $maxValue, FIELD_MAX_ERROR_MSG));
            }
            return true;
        }

        public static function isEmail($fieldName, $fieldValue)
        {
            if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
                throw new Exception($fieldName." ".INVALID_EMAIL);
            }
        }

        public static function makeRightPhone($phone)
        {
            $phone = preg_replace('/[^0-9]/', '', $phone);

            if (strlen($phone) == 12) {
                return '+'.$phone;
            }
            if (strlen($phone) == 11) {
                return '+3'.$phone;
            }
            if (strlen($phone) == 10) {
                return '+38'.$phone;
            }
            if (strlen($phone) == 9) {
                return '+380'.$phone;
            }
            return null;
        }

        public static function isValidCreditCard($fieldName, $fieldValue)
        {

            // Проверим являются ли все символы цифрми.
            if (!ctype_digit($fieldValue)) {

                throw new Exception($fieldName.' - в этом поле должно быть 16 цифр');
            }
            // Проверим количество символов в номере карты
            if (strlen($fieldValue) != 16) {
                throw new Exception('В номере карты должно быть 16 цифр');
            }

            // Проверка контрольной суммы номера карты
            $checksum = 0;

            for ($i = 16 - 1; $i >= 0; $i -= 2) {
                // Сложим все вторые цифры с конца
                $checksum += substr($fieldValue, $i, 1);
            }

            for ($i = 16 - 2; $i >= 0; $i -= 2) {
                // Сложим все вторые цифры с конца умноженные на 2
                $double = substr($fieldValue, $i, 1) * 2;

                // Если значение больше 10, то вычитаем 9
                $checksum += ($double >= 10) ? ($double - 9) : $double;
            }

            // Если контрольная сумма кратна 10, номер действителен
            if ($checksum % 10 !== 0) {
                throw new Exception($fieldName.' - ошибка при вводе данных');
            }
            return true;
        }

        public static function isValidCVV($fieldName, $fieldValue)
        {
            if (!preg_match('([0-9]{3})', $fieldValue)) {
                throw new Exception($fieldName.' - ошибка ввода данного поля');
            }
            return true;
        }

        public static function isInteger($fieldName, $fieldValue)
        {
            if (!is_int($fieldValue) || $fieldValue < 1) {
                throw new Exception($fieldName.' - не является целым положительным числом');
            }
            return true;
        }

        public static function isNumeric($fieldName, $fieldValue)
        {
            if (!is_numeric($fieldValue)) {
                throw new Exception($fieldName.' - не является числом');
            }
            return true;
        }

        public static function isBoolean($fieldName, $fieldValue)
        {
            if (filter_var($fieldValue, FILTER_VALIDATE_BOOLEAN)) {
                throw new Exception($fieldName.' - ошибка в формате данного поля');
            }
            return true;
        }

        public static function isMonth($fieldName, $fieldValue)
        {
            $fieldValue = (int)$fieldValue;
            if ($fieldValue < 1 || $fieldValue > 12) {
                throw new Exception($fieldName.' - ошибка ввода данного поля');
            }
            return true;
        }

        public static function isYear($fieldName, $fieldValue)
        {
            $max_year_end = (int)date('Y') + 11;
            $min_year_end = (int)date('Y');
            $fieldValue = (int)$fieldValue;
            if ($fieldValue < $min_year_end || $fieldValue > $max_year_end) {
                throw new Exception($fieldName.' - ошибка ввода данного поля');
            }
            return true;
        }

        public static function isValidMethod($fieldValue)
        {
            $arr = ['json', 'xml'];
            if (!in_array($fieldValue, $arr, true)) {
                throw new Exception('Не допустимый метод передачи данных');
            }
            return true;
        }
    }
