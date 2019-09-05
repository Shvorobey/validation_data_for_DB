<?php

    require_once 'Validator.php';
    require_once 'PDO_DB.php';

    class Data

    {
        const TABLE = 'request';

        public static function addNewData($data)
        {
            self::validateData($data);

            foreach ($data as $key => $val) {
                $data[$key] = trim($data[$key]);
                $data[$key] = strip_tags($data[$key]);
                $data[$key] = htmlspecialchars($data[$key]);
                $data[$key] = stripslashes($data[$key]);
            }

            if (self::getData($data['hash']) !== null) {
                throw new Exception("Hash exist");
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
            PDO_DB::del_id(self::TABLE, $hash, false, 'hash', 'hash');
        }

        public static function isHashExist($hash)
        {
            $stm = PDO_DB::prepare("SELECT * FROM ".self::TABLE." WHERE hash=? LIMIT 1", [$hash]);
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
            self::validateData($data);

            $data['phone_num'] = Validator::makeRightPhone($data['phone_num']);

            if ($data['phone_num'] == null) {
                throw new Exception("Some message");
            }

            if (strcasecmp($this_data['email'], $data['email']) !== 0) {
                if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    if (self::getDataByEmail($data['email']) !== null) {
                        throw new Exception(THIS_EMAIL_ALREADY_EXISTS);
                    }
                } else {
                    throw new Exception(INCORRECT_EMAIL_ERROR_MSG);
                }
            } else {
                unset($data['email']);
            }

            if (is_uploaded_file($_FILES['image']['tmp_name'])) {
                $img_resource = @imagecreatefromstring(file_get_contents($_FILES['image']['tmp_name']));
                $dir = '/upload-files/datas/'.date('Y/m/d/');
                $filename = $data['id'].microtime(true).'.jpg';

                if ($img_resource) {

                    if (!file_exists(ROOT.$dir)) {
                        @mkdir(ROOT.$dir, 0755, true);
                    }
                    if ($this_data['image'] && stristr($this_data['image'], 'upload-files')) {
                        @unlink(ROOT.$this_data['image']);
                    }

                    imageinterlace($img_resource, true);
                    imagejpeg($img_resource, ROOT.$dir.$filename, 100);
                    imagedestroy($img_resource);
                    $data['image'] = $dir.$filename;
                }
            }

            if (empty($data['image']) && $data['selected-image']) {

                $data['image'] = '/assets/img/edit/'.$data['selected-image'].'.png';

                if ($this_data['image'] && stristr($this_data['image'], 'upload-files')) {
                    @unlink(ROOT.$this_data['image']);
                }
            }

            unset($data['selected-image']);
            $data['spam'] = (isset($data['spam'])) ? 1 : 0;

            PDO_DB::update($data, self::TABLE, $hash, 'hash');

            $data = self::getData($hash);

            $_SESSION['auth']['login'] = $data['login'];
            
            return true;
        }

        private static function validateData($data)
        {
            Validator::isMinLength('Phone number', $data['msisdn'], 9);
            Validator::isMinLength('data_id', $data['data_id'], 1);

            Validator::isMaxLength('Phone number', $data['msisdn'], 13);
            Validator::isMaxLength('data_id', $data['data_id'], 30);
        }
    }
