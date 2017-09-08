<?php

namespace okapi\core\Response;

use okapi\lib\ClsTbsZip;

class OkapiZIPHttpResponse extends OkapiHttpResponse
{
    public $zip;

    public function __construct()
    {
        $this->zip = new ClsTbsZip();
        $this->zip->CreateNew();
    }

    public function print_body()
    {
        $this->zip->Flush(ClsTbsZip::TBSZIP_DOWNLOAD|ClsTbsZip::TBSZIP_NOHEADER);
    }

    public function get_body()
    {
        $this->zip->Flush(ClsTbsZip::TBSZIP_STRING);
        return $this->zip->OutputSrc;
    }

    public function get_length()
    {
        # The _EstimateNewArchSize() method returns *false* if archive
        # size can not be calculated *exactly*, which causes display()
        # method to skip Content-Length header, and triggers chunking
        return $this->zip->_EstimateNewArchSize();
    }

    public function display()
    {
        $this->allow_gzip = false;
        parent::display();
    }
}
