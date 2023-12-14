<?php 
defined('BASEPATH') or exit ('no direct script acces allowed');

class Thevisit extends CI_Controller{ 
   
   function __construct()
   { 
        parent::__construct();
        $this->load->helper('url');

    }
    public function index()
    { 
        $data['judul'] = "Login"; 
        $data['user'] = ''; 
        $this->load->view('templates/aute_header', $data); 
        $this->load->view('autentifikasi/login'); 
        $this->load->view('templates/aute_footer');
    
    }
}