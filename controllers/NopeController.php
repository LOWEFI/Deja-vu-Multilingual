<?php

// controllers/NopeController.php

class NopeController extends BaseController
{

    //private $base_model;

    // public function __construct(){
    // parent::__construct();
    // $this->base_model = $this->model('BaseModel');
    // }

    public function index()
    {
        $nope_text = Parsedown(Language::get('nope_text'));

        $this->render('Nope', [
            'nope_text_html' => $nope_text
        ]);
    }
}