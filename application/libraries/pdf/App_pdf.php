<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/PDF_Signature.php');

abstract class App_pdf extends TCPDF
{
    use PDF_Signature;

    public $font_size = '';

    public $font_name = '';

    public $image_scale = 1.53;

    public $jpeg_quaility = 100;

    public $pdf_author = '';

    public $swap = false;

    public $footerY = -15;

    protected $languageArray = [
        'a_meta_charset' => 'UTF-8',
    ];

    protected $tag = '';

    protected $view_vars = [];

    private $formatArray = [];

    /**
     * This is true when last page is rendered
     * @var boolean
     */
    protected $last_page_flag = false;

    protected $ci;

    public function __construct()
    {
        $this->formatArray = $this->get_format_array();

        parent::__construct($this->formatArray['orientation'], 'mm', $this->formatArray['format'], true, 'UTF-8', false, false);

        /**
         * If true print TCPDF meta link.
         * @protected
         * @since 2.3.2
         */
        $this->tcpdflink = false;

        $this->ci = &get_instance();

        $this->setLanguageArray($this->languageArray);

        $this->swap       = get_option('swap_pdf_info');
        $this->pdf_author = get_option('companyname');

        $this->set_font_size($this->get_default_font_size());
        $this->set_font_name($this->get_default_font_name());

        if (defined('APP_PDF_MARGIN_LEFT') && defined('APP_PDF_MARGIN_TOP') && defined('APP_PDF_MARGIN_RIGHT')) {
            $this->SetMargins(APP_PDF_MARGIN_LEFT, APP_PDF_MARGIN_TOP, APP_PDF_MARGIN_RIGHT);
        }

        $this->SetAutoPageBreak(true, (defined('APP_PDF_MARGIN_BOTTOM') ? APP_PDF_MARGIN_BOTTOM : PDF_MARGIN_BOTTOM));

        $this->SetAuthor($this->pdf_author);
        $this->SetFont($this->get_font_name(), '', $this->get_font_size());
        $this->setImageScale($this->image_scale);
        $this->setJPEGQuality($this->jpeg_quaility);

        $this->AddPage($this->formatArray['orientation'], $this->formatArray['format']);

        if ($this->ci->input->get('print') == 'true') {
            // force print dialog
            $this->IncludeJS('print(true);');
        }

        $this->set_default_view_vars();

        hooks()->do_action('pdf_construct', ['pdf_instance' => $this, 'type' => $this->type()]);
    }

    abstract public function prepare();

    abstract protected function file_path();

    abstract protected function type();

    public function set_view_vars($vars, $value = null)
    {
        if (is_null($value) && is_array($vars)) {
            $this->view_vars = array_merge($this->view_vars, $vars);
        } else {
            $this->view_vars[$vars] = $value;
        }

        return $this;
    }

    public function get_view_vars($vars)
    {
        return $this->view_vars;
    }

    public function get_format_array()
    {
        return get_pdf_format('pdf_format_' . $this->type());
    }

    public function set_font_size($size)
    {
        $this->font_size = $size;

        return $this;
    }

    public function get_font_size()
    {
        return $this->font_size;
    }

    public function get_default_font_size()
    {
        $font_size = get_option('pdf_font_size');

        if ($font_size == '') {
            $font_size = 10;
        }

        return $font_size;
    }

    public function get_font_name()
    {
        return $this->font_name;
    }

    public function set_font_name($name)
    {
        $this->font_name = $name;

        return $this;
    }

    public function get_default_font_name()
    {
        $font = get_option('pdf_font');
        if ($font != '' && !in_array($font, get_pdf_fonts_list())) {
            $font = 'freesans';
        }

        return $font;
    }

    public function custom_fields()
    {
        $whereCF = ['show_on_pdf' => 1];
        if (is_custom_fields_for_customers_portal()) {
            $whereCF['show_on_client_portal'] = 1;
        }

        return get_custom_fields($this->type(), $whereCF);
    }

    public function Close()
    {
        $this->process_signature();

        hooks()->do_action('pdf_close', ['pdf_instance' => $this, 'type' => $this->type()]);

        $this->last_page_flag = true;

        parent::Close();
    }

    public function Header()
    {
		if( $_SERVER['SERVER_NAME']  == '34.88.252.59' || get_option('pdf_header_image') != ''){ 
            $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+5, PDF_MARGIN_RIGHT);
            $orint = $this->get_format_array();
            if($orint['orientation'] == 'L') {
                $from_left = 10; 
                $align = 'C';
            }else 
            {
                $from_left = 10; 
                $align='C';
            }
            if(get_option('pdf_header_image') != '' &&  $this->type() != 'legalapprove'){
                $headerImage = get_option('pdf_header_image');
                $headerPath =  FCPATH.'uploads/company/'.$headerImage;
                $headerExists = file_exists($headerPath);
                if($headerExists){
                    // Logo
                    //$companyUploadPath         = get_upload_path_by_type('company');
                    //$image_file =$companyUploadPath . get_option('company_logo_dark'); //FCPATH.'media/alsehilogo.png';

                    $this->Image($headerPath, $from_left, 2, get_option('pdf_logo_width'), 30, 'PNG', '', 'T', false, 300,$align, false, false, 0, false, false, false);
                    $this->setPrintHeader(true);
                    $this->setPrintFooter(true);
                            $this->SetHeaderMargin(PDF_MARGIN_HEADER);
                    $this->SetFooterMargin(PDF_MARGIN_FOOTER);

                    $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                }
            }else{
                 $headerImage = get_option('company_logo_dark');
                $headerPath =  FCPATH.'uploads/company/'.$headerImage;
                $headerExists = file_exists($headerPath);
                if($headerExists){
                   $this->Image($headerPath, $from_left, 2, 50, 30, 'jpg', '', 'T', false, 300,'R', false, false, 0, false, false, false);
                    $this->setPrintHeader(true);
                    $this->setPrintFooter(true);
                            $this->SetHeaderMargin(PDF_MARGIN_HEADER);
                    $this->SetFooterMargin(PDF_MARGIN_FOOTER);

                    $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                }
            }
            
        }
        hooks()->do_action('pdf_header', ['pdf_instance' => $this, 'type' => $this->type()]);
       
    }

    public function Footer()
    {
         // Position from bottom
        $this->SetY($this->footerY);

        $this->SetFont($this->get_font_name(), '', $this->get_font_size());

        hooks()->do_action('pdf_footer', ['pdf_instance' => $this, 'type' => $this->type()]);


        

        if(($this->type() == 'invoice' &&  get_option('show_pdf_footer_image_invoice') == 1)
            || ($this->type() == 'estimate' && get_option('show_pdf_footer_image_estimate') == 1)||($this->type() == 'proposal' &&get_option('show_pdf_footer_image_proposal') == 1) || $this->type() == 'contract' ||  $this->type() == 'legalapprove') {

            $footerImage = get_option('pdf_footer_image');
            $footerPath = FCPATH.'uploads/company/'.$footerImage;
            $footerExists = file_exists($footerPath);
            if($footerExists){
                //$this->Image($footerPath, 0, 272,210, 0, " ", "", "T", false, 300, "", false, false, 0, false, false, false);
                
                $this->Image($footerPath,6, 278, 188,18, "", "", "B", true, 300, "L", false, false, 0, false, false, false);
                /*
                Image( filename, left, top, width, height, type, link, align, resize, dpi, align, ismask, imgmask, border, fitbox, hidden, fitonpage)

                filename : name of the file containing the image
                left : from left
                top: from top
                width: width of the image. Zero for automatically calculated
                height : height of the image. Zero for automatically calculated.
                type: JPG, JPEG, PNG. If not specified, the type is inferred from the file extension.
                link:URL or identifier returned by AddLink()
                align: T (top), M (middle), B (bottom), N (next line)
                resize: true/false. If true resize (reduce) the image to fit :w and :h (requires RMagick library); if false do not resize; if 2 force resize in all cases (upscaling and downscaling).
                dpi: dot-per-inch resolution used on resize. Recommended 300
                align: align L (left), C (center), R (right)
                ismask: true if this image is a mask, false otherwise
                imgmask: image object returned by this function or false.
                border: 0: no border (default) 1: frame or a string containing some or all of the following characters (in any order): L: left T: top R: right B: bottom
                fitbox: If true scale image dimensions proportionally to fit within the (:w, :h) box.
                hidden: true/false
                fitonpage: if true the image is resized to not exceed page dimensions.
                    */
            }

        }
        $this->SetY(-15);
        if (get_option('show_page_number_on_pdf') == 1) {
            $this->SetFont($this->get_font_name(), 'I', 8);
            $this->SetTextColor(142, 142, 142);
            $this->Cell(0, 15, $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
    }

    public function fix_editor_html($content)
    {
        // Add <br /> tag and wrap over div element every image to prevent overlaping over text
        $content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $content);
        // Fix BLOG images from TinyMCE Mobile Upload, could help with desktop too
        $content = preg_replace('/data:image\/jpeg;base64/m', '@', $content);

        // Replace <img src="" width="100%" height="auto">
        $content = preg_replace('/width="(([0-9]*%)|auto)"|height="(([0-9]*%)|auto)"/mi', '', $content);

        // Add cellpadding to all tables inside the html
        $content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="4">', $content);

        // Remove white spaces cased by the html editor ex. <td>  item</td>
        $content = preg_replace('/[\t\n\r\0\x0B]/', '', $content);
        $content = preg_replace('/([\s])\1+/', ' ', $content);

        // Tcpdf does not support float css we need to adjust this here
        $content = str_replace('float: right', 'text-align: right', $content);
        $content = str_replace('float: left', 'text-align: left', $content);

        // Tcpdf does not support float css we need to adjust this here
        $content = str_replace('float: right', 'text-align: right', $content);
        $content = str_replace('float: left', 'text-align: left', $content);

        // Image center
        $content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $content);

        return $content;
    }

    protected function load_language($client_id)
    {
        load_pdf_language($client_id);

        return $this;
    }

    protected function get_file_path()
    {
        return hooks()->apply_filters($this->type() . '_pdf_build_path', $this->file_path());
    }

    protected function build()
    {
        _bulk_pdf_export_maybe_tag($this->tag, $this);

        if ($path = $this->get_file_path()) {

            // Backwards compatible
            $pdf = $this;
            $CI  = $this->ci;

            // The view vars, also backwards compatible
            extract($this->view_vars);
            include($path);
        }

        if (ob_get_length() > 0 && ENVIRONMENT == 'production') {
            ob_end_clean();
        }

        return $this;
    }

    private function set_default_view_vars()
    {
        $this->set_view_vars([
            'pdf_custom_fields' => $this->custom_fields(),
            'swap'              => $this->swap,
            'font_size'         => $this->get_font_size(),
            'font_name'         => $this->get_font_name(),
        ]);
    }

    public function with_number_to_word($client_id)
    {
        $this->ci->load->library('app_number_to_word', [ 'clientid' => $client_id ], 'numberword');

        return $this;
    }

    /**
    * Unset all class variables except the following critical variables.
    *
    * @param $destroyall (boolean) if true destroys all class variables, otherwise preserves critical variables.
    * @param $preserve_objcopy (boolean) if true preserves the objcopy variable
    *
    * @since 4.5.016 (2009-02-24)
    */
    public function _destroy($destroyall = false, $preserve_objcopy = false)
    {
        // restore internal encoding
        if (isset($this->internal_encoding) and !empty($this->internal_encoding)) {
            mb_internal_encoding($this->internal_encoding);
        }

        if (isset(self::$cleaned_ids[$this->file_id])) {
            $destroyall = false;
        }

        if ($destroyall and !$preserve_objcopy) {
            self::$cleaned_ids[$this->file_id] = true;
            // remove all temporary files
            if ($handle = @opendir(K_PATH_CACHE)) {
                while (false !== ($file_name = readdir($handle))) {
                    $fullPath = K_PATH_CACHE . $file_name;
                    if (strpos($file_name, '__tcpdf_' . $this->file_id . '_') === 0 && file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }

                closedir($handle);
            }

            if (isset($this->imagekeys)) {
                foreach ($this->imagekeys as $file) {
                    if (strpos($file, K_PATH_CACHE) === 0 && file_exists($file)) {
                        @unlink($file);
                    }
                }
            }
        }

        $preserve = [
            'file_id',
            'internal_encoding',
            'state',
            'bufferlen',
            'buffer',
            'cached_files',
            'imagekeys',
            'sign',
            'signature_data',
            'signature_max_length',
            'byterange_string',
            'tsa_timestamp',
            'tsa_data',
        ];

        foreach (array_keys(get_object_vars($this)) as $val) {
            if ($destroyall or !in_array($val, $preserve)) {
                if ((!$preserve_objcopy or ($val != 'objcopy')) and ($val != 'file_id') and isset($this->$val)) {
                    unset($this->$val);
                }
            }
        }
    }
}
