<?php

/** SS_ZIP class is designed to work with ZIP archives
 *
 * @author Yuriy Horobey, smiledsoft.com
 * @email info@smiledsoft.com
 *
 * Unicode Reminder メモ
 */
class ss_zip
{
    /** contains whole zipfile
     *
     * @see ss_zip::archive()
     * @see ss_zip::ss_zip()
     */
    public $zipfile = '';
    /** compression level    */
    public $complevel = 6;
    /** entry counter */
    public $cnt = 0;
    /** current offset in zipdata segment */
    public $offset = 0;
    /** index of current entry
     *
     * @see ss_zip::read()
     */
    public $idx = 0;
    /**
     * ZipData segment, each element of this array contains local file header plus zipped data
     */
    public $zipdata = [];
    /**    central directory array    */
    public $cdir = [];

    /**    constructor
     *
     * @param string $zipfile if not empty must contain path to valid zip file, ss_zip will try to open and parse it.
     *               If this parameter is empty, the new empty zip archive is created. This parameter has no meaning in
     *               LIGHT verion, please upgrade to PROfessional version.
     * @param int $complevel compression level, 1-minimal compression, 9-maximal, default is 6
     */
    public function __construct($zipfile = '', $complevel = 6)
    {
        $this->clear();
        if ($complevel < 1) {
            $complevel = 1;
        }
        if ($complevel > 9) {
            $complevel = 9;
        }
        $this->complevel = $complevel;
        $this->open($zipfile);
    }

    /**Resets the object, clears all the structures
     */
    public function clear()
    {
        $this->zipfile = "";
        $this->complevel = 6;
        $this->cnt = 0;
        $this->offset = 0;
        $this->idx = 0;
        $this->zipdata = [];
        $this->cdir = [];
    }

    /**opens zip file.
     * <center><hr nashade>*** This functionality is available in PRO version only. ***<br><a
     * href='http://smiledsoft.com/demos/phpzip/' target='_blank'>please upgrade </a><hr nashade></center> This
     * function opens file pointed by zipfile parameter and creates all necessary structures
     *
     * @param string $zipfile path to the file
     * @param boolean $append if true the newlly opened archive will be appended to existing object structure
     */
    public function open($zipfile, $append = false)
    {
    }


    /**saves to the disc or sends zipfile to the browser.
     *
     * @param string $zipfile path under which to store the file on the server or file name under which
     *                                     the
     *                             browser will receive it. If you are saving to the server, you are responsible to
     *                             obtain appropriate write permissions for this operation.
     * @param string $where indicates where should the file be sent
     *        <ul>
     *          <li>'f' -- filesystem </li>
     *          <li>'b' -- browser</li>
     *          <li>'r' -- raw</li>
     *        </ul>
     *        Please remember that there should not be any other output before you call this
     *        function. The only exception is that other headers may be sent. See <a
     *        href='http://php.net/header' target='_blank'>http://php.net/header</a>
     */
    public function save($zipfile, $where = 'f')
    {
        if (!$this->zipfile) {
            $this->archive();
        }
        $zipfile = trim($zipfile);

        if (strtolower(trim($where)) === 'f') {
            $this->_write($zipfile, $this->zipfile);
        } elseif (strtolower(trim($where)) === 'r') {
            $zipfile = basename($zipfile);
            print $this->archive();
        } else {
            $zipfile = basename($zipfile);
            header("Content-type: application/octet-stream");
            header("Content-disposition: attachment; filename=\"$zipfile\"");
            print $this->archive();
        }
    }

    /** adds data to zip file
     *
     * @param string $filename path under which the content of data parameter will be stored into the zip archive
     * @param string $data content to be stored under name given by path parameter
     *
     * @see ss_zip::add_file()
     */
    public function add_data($filename, $data = null)
    {

        $filename = trim($filename);
        $filename = str_replace('\\', '/', $filename);
        if ($filename[0] == '/') {
            $filename = substr($filename, 1);
        }

        if (($attr = (($datasize = strlen($data)) ? 32 : 16)) == 32) {
            $crc = crc32($data);
            $gzdata = gzdeflate($data, $this->complevel);
            $gzsize = strlen($gzdata);
            $dir = dirname($filename);
        } else {
            $crc = 0;
            $gzdata = '';
            $gzsize = 0;
        }

        $fnl = strlen($filename);
        $fh = "\x14\x00";    // ver needed to extract
        $fh .= "\x00\x00";    // gen purpose bit flag
        $fh .= "\x08\x00";    // compression method
        $fh .= "\x00\x00\x00\x00"; // last mod time and date
        $fh .=
            pack(
                "V3v2",
                $crc, //crc
                $gzsize, //c size
                $datasize, //unc size
                $fnl, //fname lenght
                0 //extra field length
            );


        //local file header
        $lfh = "PK\x03\x04";
        $lfh .= $fh . $filename;
        $zipdata = $lfh;
        $zipdata .= $gzdata;
        $zipdata .= pack("V3", $crc, $gzsize, $datasize);
        $this->zipdata[] = $zipdata;
        //Central Directory Record
        $cdir = "PK\x01\x02";
        $cdir .=
            pack(
                "va*v3V2",
                0,
                $fh,
                0,        // file comment length
                0,        // disk number start
                0,        // internal file attributes
                $attr,    // external file attributes - 'archive/directory' bit set
                $this->offset
            ) . $filename;

        $this->offset += 42 + $fnl + $gzsize;
        $this->cdir[] = $cdir;
        $this->cnt ++;
        $this->idx = $this->cnt - 1;
    }

    /** adds a file to the archive
     *
     * @param string $filename contains valid path to file to be stored in the arcive.
     * @param string $storedasname the path under which the file will be stored to the archive.
     * If empty, the file will be stored under path given by filename parameter
     *
     * @see ss_zip::add_data()
     * @return void
     */
    public function add_file($filename, $storedasname = '')
    {
        $fh = fopen($filename, 'r');
        $data = fread($fh, filesize($filename));
        if (!trim($storedasname)) {
            $storedasname = $filename;
        }

        $this->add_data($storedasname, $data);
    }

    /** compile the archive.
     * This function produces ZIP archive and returns it.
     *
     * @return string with zip file
     */
    public function archive()
    {
        if (!$this->zipdata) {
            return '';
        }
        $zds = implode('', $this->zipdata);
        $cds = implode('', $this->cdir);
        $zdsl = strlen($zds);
        $cdsl = strlen($cds);
        $this->zipfile =
            $zds
            . $cds
            . "PK\x05\x06\x00\x00\x00\x00"
            . pack(
                'v2V2v',
                $this->cnt, // total # of entries "on this disk"
                $this->cnt, // total # of entries overall
                $cdsl, // size of central dir
                $zdsl, // offset to start of central dir
                0
            );              // .zip file comment length
        return $this->zipfile;
    }

    /** changes pointer to current entry.
     * Most likely you will always use it to 'rewind' the archive and then using read()
     * Checks for boundaries, so will not allow index to be set to values < 0 ro > last element
     *
     * @param int $idx the new index to which you want to rewind the archive curent pointer
     *
     * @return int idx the index to which the current pointer was actually set
     * @see ss_zip::read()
     */
    public function seek_idx($idx)
    {
        if ($idx < 0) {
            $idx = 0;
        }
        if ($idx >= $this->cnt) {
            $idx = $this->cnt - 1;
        }
        $this->idx = $idx;

        return $idx;
    }

    /** Reads an entry from the arcive which is pointed by inner index pointer.
     * <center><hr nashade>*** This functionality is available in PRO version only. ***<br><a
     * href='http://smiledsoft.com/demos/phpzip/' target='_blank'>please upgrade </a><hr nashade></center> The curent
     * index can be changed by seek_idx() method.
     *
     * @return array Returns associative array of the following structure
     * <ul>
     * <li>'idx'=>    index of the entry </li>
     * <li>'name'=>full path to the entry </li>
     * <li>'attr'=>integer file attribute of the entry </li>
     * <li>'attrstr'=>string file attribute of the entry <br>
     * This can be:
     * <ul>
     * <li>'file' if the integer attribute was 32</li>
     * <li>'dir'  if the integer attribute was 16 or 48</li>
     * <li>'unknown' for other values</li>
     * </ul>
     * </li>
     * </ul>
     * @see ss_zip::seek_idx()
     */
    public function read()
    {
    }


    /** Removes entry from the archive.
     * please be very carefull with this function, there is no undo after you save the archive
     *
     * @return bool true on success or false on failure
     *
     * @param int $idx
     */
    public function remove($idx)
    {
    }


    /** extracts data from the archive and return it as a string.
     * <center><hr nashade>*** This functionality is available in PRO version only. ***<br><a
     * href='http://smiledsoft.com/demos/phpzip/' target='_blank'>please upgrade </a><hr nashade></center> This
     * function returns data identified by idx parameter.
     *
     * @param int $idx index of the entry
     *
     * @return array returns associative array of the folloving structure:
     *            <ul>
     *            <li>'file' path under which the entry is stored in the archive</li>
     *            <li>'data' In case if the entry was file, contain its data. For directory entry this is empty</li>
     *            <li>'size' size of the data</li>
     *            <li>'error' the error if any has happened. The bit 0 indicates incorect datasize, bit 1 indicates CRC
     *            error</li>
     *            </ul>
     * @see ss_zip::extract_file
     */
    public function extract_data($idx)
    {
    }


    /** extracts the entry and creates it in the file system.
     * <center><hr nashade>*** This functionality is available in PRO version only. ***<br><a
     * href='http://smiledsoft.com/demos/phpzip/' target='_blank'>please upgrade </a><hr nashade></center>
     *
     * @param int $idx Index of the entry
     * @param string $path the first part of the path where the entry will be stored. So if this
     *               is '/my/server/path' and entry is arhived/file/path/file.txt then the function will attempt to
     *               store it under /my/server/path/arhived/file/path/file.txt You are responsible to ensure that you
     *               have write permissions for this operation under your operation system.
     */
    public function extract_file($idx, $path = ".")
    {
    }


    public function _check_idx($idx)
    {
        return $idx >= 0 and $idx < $this->cnt;
    }

    public function _write($name, $data)
    {
        $fp = fopen($name, "w");
        fwrite($fp, $data);
        fclose($fp);
    }
}

/** debug helper.
 * the only job for this function is take parameter $v and ouput it with print_r() preceding with < xmp > etc
 * The $l is a label like l=myvar
 */
function dbg($v, $l = 'var')
{
    echo "<xmp>$l=";
    print_r($v);
    echo "</xmp>";
}
