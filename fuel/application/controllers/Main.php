<?php
class Main extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Uutiset_model");
        $this->load->model("Oikeudet_model");
    }

    function index()
    {

        $vars = array();
        $vars['admins'] = $this->Oikeudet_model->users_in_group_id(1);
        $vars['message'] = $this->session->flashdata('message');
        $vars['tiedotukset'] = $this->Uutiset_model->hae_tiedotukset(5, 0);

        // Debug-tulostus
        log_message('debug', 'Admins: ' . print_r($vars['admins'], true));
        log_message('debug', 'Tiedotukset: ' . print_r($vars['tiedotukset'], true));

        $this->fuel->pages->render('home', $vars);
    }
    
    function yllapito(){
        $this->fuel->pages->render('yllapito/index');

    }
    
}
?>