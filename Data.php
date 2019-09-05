<?php

    require_once 'Validator.php';
    require_once 'PDO_DB.php';

    class Data

    {
        const TABLE = 'request';

        public static function addNewData($data)
        {
//            self::validateData($data);

            foreach ($data as $key => $val) {
                $data[$key] = trim($data[$key]);
                $data[$key] = strip_tags($data[$key]);
                $data[$key] = htmlspecialchars($data[$key]);
                $data[$key] = stripslashes($data[$key]);
            }

            if (self::getData($data['hash']) !== null) {
                throw new Exception("Hash error");
            }
            try {
                PDO_DB::insert($data, self::TABLE);
            } catch (Exception $e) {
                echo $e;
            };

            return true;
        }

        public static function deleteData($hash)
        {
            PDO_DB::del_id(self::TABLE, $hash, false,'hash', 'hash');
        }

        public static function isHashExist($hash)
        {
            $stm = PDO_DB::prepare("SELECT * FROM " . self::TABLE . " WHERE hash=? LIMIT 1", [$hash]);
            $data = $stm->fetch();

            if ($data === false) {
                return false;
            }
            return true;
        }

        public static function getData($hash)
        {
            $arr = PDO_DB::row_by_hash(self::TABLE, $hash, 'hash');
            if (!$arr) {
                return null;
            }

            return $arr;
        }

        public static function updateData($data, $hash)
        {
            $this_data = PDO_DB::row_by_hash(self::TABLE, $hash, 'hash');
            if (!$this_data) {
                return null;
            }

            foreach ($data as $key => $val) {
                $data[$key] = trim($data[$key]);
                $data[$key] = strip_tags($data[$key]);
                $data[$key] = htmlspecialchars($data[$key]);
                $data[$key] = stripslashes($data[$key]);
            }
//            self::validateData($data);

            PDO_DB::update($data, self::TABLE, $hash, 'hash');

            return true;
        }

        private static function validateData($data)
        {
            Validator::isMinLength('Phone number', $data['msisdn'], 9);
            Validator::isMinLength('User Id', $data['user_id'], 1);

            Validator::isMaxLength('Phone number', $data['msisdn'], 13);
            Validator::isMaxLength('User Id', $data['user_id'], 30);
        }
    }
