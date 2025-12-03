<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// Include the main TCPDF library (search for correct path)
require_once(APPPATH . 'third_party/tcpdf/tcpdf.php');

class Pdf extends TCPDF
{
    public function __construct()
    {
        parent::__construct();
    }
}
