<?php

// controllers/LanguagesController.php

class ForumsController extends BaseController
{

    //private $base_model;

    // public function __construct(){
    // parent::__construct();
    // $this->base_model = $this->model('BaseModel');
    // }

    public function index()
    {
        $languages = $this->get_languages();

        $this->render('Forums', [
            'languages' => $languages
        ]);
    }
}