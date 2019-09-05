<?php

class Validator
{
    public static function isMinLength($fieldName, $fieldValue, $minValue)
    {
        $fieldValue = trim($fieldValue);
        if (strlen($fieldValue) < $minValue) {
            throw new Exception($fieldName ."Не может быть короче".$minValue."символов");
        }
        return true;
    }

    public static function isMaxLength($fieldName, $fieldValue, $maxValue)
    {
        $fieldValue = trim($fieldValue);
        if (strlen($fieldValue) > $maxValue) {
            throw new Exception($fieldName . "Не может быть длиннее".$maxValue."символов");
        }
        return true;
    }
}
