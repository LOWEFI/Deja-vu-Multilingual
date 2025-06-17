<?php

// controllers/HomeController.php

class HomeController extends BaseController
{

    //private $base_model;

    // public function __construct(){
    // parent::__construct();
    // $this->base_model = $this->model('BaseModel');
    // }

    public function index()
    {
        $this->render('Home');
    }
}