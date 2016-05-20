<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *   get/set has to be commited with save
 *   add/remove etc. is executed instantly
 ***************************************************************************/

require_once __DIR__ . '/const.inc.php';

class picture
{
    public $nPictureId = 0;
    public $rePicture;
    public $sFileExtension = '';
    public $bFilenamesSet = false;

    public static function pictureIdFromUUID($uuid)
    {
        $pictureid = sql_value("SELECT `id` FROM `pictures` WHERE `uuid`='&1'", 0, $uuid);

        return $pictureid;
    }

    public static function fromUUID($uuid)
    {
        $pictureid = picture::pictureIdFromUUID($uuid);
        if ($pictureid == 0) {
            return null;
        }

        return new picture($pictureid);
    }

    public function __construct($nNewPictureId = ID_NEW)
    {
        global $opt;

        $this->rePicture = new rowEditor('pictures');
        $this->rePicture->addPKInt('id', null, false, RE_INSERT_AUTOINCREMENT);
        $this->rePicture->addString('uuid', '', false, RE_INSERT_AUTOUUID);
        $this->rePicture->addInt('node', 0, false);
        $this->rePicture->addDate('date_created', time(), true, RE_INSERT_IGNORE);
        $this->rePicture->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
        $this->rePicture->addString('url', '', false);
        $this->rePicture->addString('title', '', false);
        $this->rePicture->addDate('last_url_check', 0, true);
        $this->rePicture->addInt('object_id', null, false);
        $this->rePicture->addInt('object_type', null, false);
        $this->rePicture->addString('thumb_url', '', false);
        $this->rePicture->addDate('thumb_last_generated', 0, false);
        $this->rePicture->addInt('spoiler', 0, false);
        $this->rePicture->addInt('local', 0, false);
        $this->rePicture->addInt('unknown_format', 0, false);
        $this->rePicture->addInt('display', 1, false);
        $this->rePicture->addInt('mappreview', 0, false);
        $this->rePicture->addInt('seq', 0, false);

        $this->nPictureId = $nNewPictureId + 0;

        if ($nNewPictureId == ID_NEW) {
            $this->rePicture->addNew(null);

            $sUUID = mb_strtoupper(sql_value("SELECT UUID()", ''));
            $this->rePicture->setValue('uuid', $sUUID);
            $this->rePicture->setValue('node', $opt['logic']['node']['id']);
        } else {
            $this->rePicture->load($this->nPictureId);

            $sFilename = $this->getFilename();
            $fna = mb_split('\\.', $sFilename);
            $this->sFileExtension = mb_strtolower($fna[count($fna) - 1]);

            $this->bFilenamesSet = true;
        }
    }

    public function exist()
    {
        return $this->rePicture->exist();
    }

    public static function allowedExtension($sFilename)
    {
        global $opt;

        if (strpos($sFilename, ';') !== false) {
            return false;
        }
        if (strpos($sFilename, '.') === false) {
            return false;
        }

        $sExtension = mb_strtolower(substr($sFilename, strrpos($sFilename, '.') + 1));

        if (strpos(';' . $opt['logic']['pictures']['extensions'] . ';', ';' . $sExtension . ';') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function setFilenames($sFilename)
    {
        global $opt;

        if ($this->bFilenamesSet == true) {
            return;
        }
        if (strpos($sFilename, '.') === false) {
            return;
        }

        $sExtension = mb_strtolower(substr($sFilename, strrpos($sFilename, '.') + 1));
        $this->sFileExtension = $sExtension;

        $sUUID = $this->getUUID();

        $this->setUrl($opt['logic']['pictures']['url'] . $sUUID . '.' . $sExtension);
        //$this->setThumbUrl($opt['logic']['pictures']['thumb_url'] . substr($sUUID, 0, 1) . '/' . substr($sUUID, 1, 1) . '/' . $sUUID . '.' . $sExtension);

        $this->bFilenamesSet = true;
    }

    public function getPictureId()
    {
        return $this->nPictureId;
    }

    private function setArchiveFlag($bRestoring, $original_id = 0)
    {
        global $login;

        // This function determines if an insert, update oder deletion at pictures table
        // ist to be recorded for vandalism recovery, depending on WHO OR WHY the
        // operation is done. Other conditions, depending on the data, are handled
        // by triggers.
        //
        // Data is passed by ugly global DB variables, so try call this function as
        // close before the targetet DB operation as possible.

        if ($this->getObjectType() == 1) {
            /*
            $owner_id = sql_value("SELECT `user_id` FROM `caches` WHERE `cache_id`=
                                     IFNULL((SELECT `cache_id` FROM `cache_logs` WHERE `id`='&1'),
                                       (SELECT `cache_id` FROM `cache_logs_archived` WHERE `id`='&1'))",
                                                       0, $this->getObjectId());
            */
            $logger_id = sql_value(
                "SELECT
                    IFNULL((SELECT `user_id` FROM `cache_logs` WHERE `id`='&1'),
                           (SELECT `user_id` FROM `cache_logs_archived` WHERE `id`='&1'))",
                0,
                $this->getObjectId()
            );
            $archive = ($bRestoring || $login->userid != $logger_id);
        } else {
            $archive = true;
        }

        sql("SET @archive_picop=" . ($archive ? "TRUE" : "FALSE"));
        sql_slave("SET @archive_picop=" . ($archive ? "TRUE" : "FALSE"));

        sql("SET @original_picid='&1'", $original_id);
        sql_slave("SET @original_picid='&1'", $original_id);

        // @archive_picop and @original_picid are evaluated by trigger functions
    }

    private function resetArchiveFlag()
    {
        sql("SET @archive_picop=FALSE");
        sql("SET @original_picid=0");
        sql_slave("SET @archive_picop=FALSE");
        sql_slave("SET @original_picid=0");
    }

    public function getUrl()
    {
        return $this->rePicture->getValue('url');
    }

    public function setUrl($value)
    {
        return $this->rePicture->setValue('url', $value);
    }

    public function getThumbUrl()
    {
        return $this->rePicture->getValue('thumb_url');
    }

    public function setThumbUrl($value)
    {
        return $this->rePicture->setValue('thumb_url', $value);
    }

    public function getTitle()
    {
        return $this->rePicture->getValue('title');
    }

    public function setTitle($value)
    {
        if ($value != '') {
            return $this->rePicture->setValue('title', $value);
        } else {
            return false;
        }
    }

    public function getSpoiler()
    {
        return $this->rePicture->getValue('spoiler') != 0;
    }

    public function setSpoiler($value)
    {
        return $this->rePicture->setValue('spoiler', $value ? 1 : 0);
    }

    public function getLocal()
    {
        return $this->rePicture->getValue('local') != 0;
    }

    public function setLocal($value)
    {
        return $this->rePicture->setValue('local', $value ? 1 : 0);
    }

    public function getUnknownFormat()
    {
        return $this->rePicture->getValue('unknown_format') != 0;
    }

    public function setUnknownFormat($value)
    {
        return $this->rePicture->setValue('unknown_format', $value ? 1 : 0);
    }

    public function getDisplay()
    {
        return $this->rePicture->getValue('display') != 0;
    }

    public function setDisplay($value)
    {
        return $this->rePicture->setValue('display', $value ? 1 : 0);
    }

    public function getMapPreview()
    {
        return $this->rePicture->getValue('mappreview') != 0;
    }

    public function setMapPreview($value)
    {
        return $this->rePicture->setValue('mappreview', $value ? 1 : 0);
    }

    public function getFilename()
    {
        // works intendently before bFilenameSet == true !
        global $opt;

        if (mb_substr($opt['logic']['pictures']['dir'], - 1, 1) != '/') {
            $opt['logic']['pictures']['dir'] .= '/';
        }

        $url = $this->getUrl();
        $fna = mb_split('\\/', $url);

        return $opt['logic']['pictures']['dir'] . end($fna);
    }

    public function getThumbFilename()
    {
        global $opt;

        if (mb_substr($opt['logic']['pictures']['thumb_dir'], - 1, 1) != '/') {
            $opt['logic']['pictures']['thumb_dir'] .= '/';
        }

        $url = $this->getUrl();
        $fna = mb_split('\\/', $url);
        $filename = end($fna);

        $dir1 = mb_strtoupper(mb_substr($filename, 0, 1));
        $dir2 = mb_strtoupper(mb_substr($filename, 1, 1));

        return $opt['logic']['pictures']['thumb_dir'] . $dir1 . '/' . $dir2 . '/' . $filename;
    }

    public function getLogId()
    {
        if ($this->getObjectType() == OBJECT_CACHELOG) {
            return $this->getObjectId();
        } else {
            return false;
        }
    }

    public function isVisibleOnCachePage()
    {
        if ($this->getObjectType() != OBJECT_CACHELOG) {
            return null;
        } else {
            $rs = sql(
                "SELECT `id`
                 FROM `cache_logs`
                 WHERE `cache_id`='&1'
                 ORDER BY `date`, `id` DESC
                 LIMIT &2",
                $this->getCacheId(),
                MAX_LOGENTRIES_ON_CACHEPAGE
            );
        }
        $firstlogs = false;
        while ($r = sql_fetch_assoc($rs)) {
            if ($r['id'] == $this->getLogId()) {
                $firstlogs = true;
            }
        }

        sql_free_result($rs);

        return $firstlogs;
    }

    public function getCacheId()
    {
        if ($this->getObjectType() == OBJECT_CACHELOG) {
            return sql_value("SELECT `cache_id` FROM `cache_logs` WHERE `id`='&1'", false, $this->getObjectId());
        } elseif ($this->getObjectType() == OBJECT_CACHE) {
            return $this->getObjectId();
        } else {
            return false;
        }
    }

    public function getObjectId()
    {
        return $this->rePicture->getValue('object_id');
    }

    public function setObjectId($value)
    {
        return $this->rePicture->setValue('object_id', $value + 0);
    }

    public function getObjectType()
    {
        return $this->rePicture->getValue('object_type');
    }

    public function setObjectType($value)
    {
        return $this->rePicture->setValue('object_type', $value + 0);
    }

    public function getUserId()
    {
        if ($this->getObjectType() == OBJECT_CACHE) {
            return sql_value("SELECT `caches`.`user_id` FROM `caches` WHERE `caches`.`cache_id`='&1'", false, $this->getObjectId());
        } elseif ($this->getObjectType() == OBJECT_CACHELOG) {
            return sql_value("SELECT `cache_logs`.`user_id` FROM `cache_logs` WHERE `cache_logs`.`id`='&1'", false, $this->getObjectId());
        } else {
            return false;
        }
    }

    public function getNode()
    {
        return $this->rePicture->getValue('node');
    }

    public function setNode($value)
    {
        return $this->rePicture->setValue('node', $value);
    }

    public function getUUID()
    {
        return $this->rePicture->getValue('uuid');
    }

    public function getLastModified()
    {
        return $this->rePicture->getValue('last_modified');
    }

    public function getDateCreated()
    {
        return $this->rePicture->getValue('date_created');
    }

    public function getPosition()
    {
        return $this->rePicture->getValue('seq');
    }

    public function getAnyChanged()
    {
        return $this->rePicture->getAnyChanged();
    }

    // Test if the picture can be discarded as duplicate.
    // This is a quick test for Ocprop dups and may be extended for any
    // picture uploads by comparing the file sizes and contents.

    public function is_duplicate()
    {
        global $ocpropping;

        return $ocpropping &&
        sql_value(
            "
            SELECT COUNT(*) FROM `pictures`
            WHERE `object_type`='&1' AND `object_id`='&2' AND `title`='&3'",
            0,
            $this->getObjectType(),
            $this->getObjectId(),
            $this->getTitle()
        ) > 0;
    }

    // return true if successful (with insert)
    public function save($restore = false, $original_id = 0, $original_url = "")
    {
        $undelete = ($original_id != 0);

        if ($undelete) {
            if ($this->bFilenamesSet == true) {
                return false;
            } else {
                // restore picture file
                $this->setUrl($original_url);        // set the url, so that we can
                $filename = $this->getFilename();    // .. retreive the file path+name
                $this->setFilenames($filename);      // now set url(s) from the new uuid
                try {
                    rename($this->deleted_filename($filename), $this->getFilename());
                } catch (Exception $e) {
                    // @todo implement login
                }
            }
        }

        if ($this->bFilenamesSet == false) {
            return false;
        }

        $this->setArchiveFlag($restore, $original_id);
        $bRetVal = $this->rePicture->save();
        $this->resetArchiveFlag();

        if ($bRetVal) {
            $this->nPictureId = $this->rePicture->getValue('id');
            if ($this->getObjectType() == OBJECT_CACHE && $this->getMapPreview()) {
                sql(
                    "UPDATE `pictures` SET `mappreview`= 0
                     WHERE `object_type`='&1' AND `object_id`='&2' AND `id`!='&3'",
                    OBJECT_CACHE,
                    $this->getObjectId(),
                    $this->getPictureId()
                );
            }
            sql_slave_exclude();
        }

        return $bRetVal;
    }

    public function delete($restore = false)
    {
        // see also removelog.php, 'remove log pictures'

        global $opt;

        // delete record, image and thumb
        $this->setArchiveFlag($restore);
        sql("DELETE FROM `pictures` WHERE `id`='&1'", $this->nPictureId);
        $this->resetArchiveFlag();
        $filename = $this->getFilename();

        // archive picture if picture record has been archived
        if (sql_value("SELECT `id` FROM `pictures_modified` WHERE `id`='&1'", 0, $this->getPictureId()) != 0) {
            try {
                rename($filename, $this->deleted_filename($filename));
            } catch (Exception $e) {
                // @todo implement login
            }
        } else {
            try {
                unlink($filename);
            } catch (Exception $e) {
                // @todo implement login
            }
        }

        try {
            unlink($this->getThumbFilename());
        } catch (Exception $e) {
            // @todo implement login
        }

        return true;
    }

    private function deleted_filename($filename)
    {
        $fna = mb_split('\\/', $filename);
        $fna[] = end($fna);
        $fna[count($fna) - 2] = 'deleted';
        $dp = "";
        foreach ($fna as $fp) {
            $dp .= "/" . $fp;
        }

        return substr($dp, 1);
    }

    public function allowEdit()
    {
        global $login;

        $login->verify();

        if (sql_value("SELECT COUNT(*) FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&1') AND `caches`.`cache_id`='&2'", 0, $login->userid, $this->getCacheId()) == 0) {
            return false;
        } elseif ($this->getUserId() == $login->userid) {
            return true;
        }

        return false;
    }

    public function getPageLink()
    {
        if ($this->getObjectType() == OBJECT_CACHELOG) {
            $pl = 'viewcache.php?cacheid=' . urlencode($this->getCacheId());
            if (!$this->isVisibleOnCachePage()) {
                $pl .= "&log=A";
            }
            $pl .= "#log" . urlencode($this->getLogId());
        } elseif ($this->getObjectType() == OBJECT_CACHE) {
            $pl = 'editcache.php?cacheid=' . urlencode($this->getCacheId()) . '#pictures';
        } else {
            $pl = false;
        }

        return $pl;
    }

    /*
        Shrink picture to a specified maximum size. If present Imagemagick extension will be used, if not gd.
        Imagick is sharper, faster, need less memory and supports more types.
        For gd size is limited to 5000px (memory consumption).
        i prefer FILTER_CATROM because its faster but similiar to lanczos see http://de1.php.net/manual/de/imagick.resizeimage.php
        parameter:
        $tmpfile: full name of uploaded file
        $longSideSize:  if longer side of picture > $longSideSize, then it will be prop. shrinked to
        returns: true if no error occur, otherwise false
    */
    public function rotate_and_shrink($tmpFile, $longSideSize)
    {
        global $opt;
        if (extension_loaded('imagick')) {
            try {
                $image = new Imagick();
                $image->readImage($tmpFile);
                $this->imagick_rotate($image);
                $w = $image->getImageWidth();
                $h = $image->getImageHeight();
                $image->setImageResolution(PICTURE_RESOLUTION, PICTURE_RESOLUTION);
                $image->setImageCompression(Imagick::COMPRESSION_JPEG);
                $image->setImageCompressionQuality(PICTURE_QUALITY);
                $image->stripImage(); //clears exif, private data
                //$newSize=$w<$h?array($w*$longSideSize/$h,$longSideSize):array($longSideSize,$h*$longSideSize/$w);
                if (max($w, $h) > $longSideSize) {
                    $image->resizeImage($longSideSize, $longSideSize, imagick::FILTER_CATROM, 1, true);
                }
                $result = $image->writeImage($this->getFilename());
                $image->clear();
            } catch (Exception $e) {
                if ($image) {
                    $image->clear();
                }
                if ($opt['debug'] & DEBUG_DEVELOPER) {
                    die($e);
                }
                $result = false;
            }

            return $result;
        } elseif (extension_loaded('gd')) {
            $imageNew = null;
            try {
                $image = imagecreatefromstring(file_get_contents($tmpFile));
                $w = imagesx($image);
                $h = imagesy($image);
                if (max($w, $h) > 5000) {
                    throw new Exception("Image too large >5000px");
                }
                if (max($w, $h) <= $longSideSize) {
                    $result = imagejpeg($image, $this->getFilename(), PICTURE_QUALITY);
                } else {
                    $newSize = $w < $h ? [
                        $w * $longSideSize / $h,
                        $longSideSize
                    ] : [
                        $longSideSize,
                        $h * $longSideSize / $w
                    ];
                    $imageNew = imagecreatetruecolor($newSize[0], $newSize[1]);
                    imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $newSize[0], $newSize[1], $w, $h);
                    $result = imagejpeg($imageNew, $this->getFilename(), PICTURE_QUALITY);
                    imagedestroy($imageNew);
                }
                imagedestroy($image);
            } catch (Exception $e) {
                if ($image) {
                    imagedestroy($image);
                }
                if ($imageNew) {
                    imagedestroy($imageNew);
                }
                if ($opt['debug'] & DEBUG_DEVELOPER) {
                    die($e);
                }
                $result = false;
            }

            return $result;
        } else {
            return false;
        }
    }

    // rotate image according to EXIF orientation
    public function rotate($tmpFile)
    {
        if (extension_loaded('imagick')) {
            try {
                $image = new Imagick();
                $image->readImage($tmpFile);
                if ($this->imagick_rotate($image)) {
                    $image->stripImage(); // clears exif, private data
                    $image->writeImage($this->getFilename());
                    $image->clear();

                    return true;
                } else {
                    $image->clear();
                }
            } catch (Exception $e) {
                if ($image) {
                    $image->clear();
                }
            }
        }

        return move_uploaded_file($tmpFile, $this->getFilename());
    }

    public function imagick_rotate(&$image)
    {
        $exif = $image->getImageProperties();
        if (isset($exif['exif:Orientation'])) {
            switch ($exif['exif:Orientation']) {
                case 3:
                    return $image->rotateImage(new ImagickPixel(), 180);
                case 6:
                    return $image->rotateImage(new ImagickPixel(), 90);
                case 8:
                    return $image->rotateImage(new ImagickPixel(), - 90);
            }
        }

        return false;
    }

    public function up()
    {
        $prevpos = sql_value(
            "
            SELECT MAX(`seq`)
            FROM `pictures`
            WHERE `object_type`='&1' AND `object_id`='&2' AND `seq`<'&3'",
            0,
            $this->getObjectType(),
            $this->getObjectId(),
            $this->getPosition()
        );

        if ($prevpos) {
            $maxpos = sql_value(
                "
                SELECT MAX(`seq`)
                FROM `pictures`
                WHERE `object_type`='&1' AND `object_id`='&2'",
                0,
                $this->getObjectType(),
                $this->getObjectId()
            );

            // swap positions with the previous pic
            sql(
                "
                UPDATE `pictures`
                SET `seq`='&2'
                WHERE `id`='&1'",
                $this->getPictureId(),
                $maxpos + 1
            );
            sql(
                "
                UPDATE `pictures` SET `seq`='&4'
                WHERE `object_type`='&1' AND `object_id`='&2' AND `seq`='&3'",
                $this->getObjectType(),
                $this->getObjectId(),
                $prevpos,
                $this->getPosition()
            );
            sql(
                "
                UPDATE `pictures`
                SET `seq`='&2'
                WHERE `id`='&1'",
                $this->getPictureId(),
                $prevpos
            );
            $this->rePicture->setValue('seq', $prevpos);

            return true;
        }

        return false;
    }
}
