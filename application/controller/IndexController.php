<?php

class IndexController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handles what happens when user moves to URL/index/index - or - as this is the default controller, also
     * when user moves to /index or enter your application at base level
     */
    public function index()
    {
        $this->View->render('login/index', array(
			'open'=>UserModel::getOpenInterview()
		));
    }
	 public function en()
    {
        $this->View->render('login/english', array(
			'open'=>UserModel::getOpenInterview()
		));
    }
	public function ujian(){
		$this->View->renderPsi('login/psikometrik', array(
			'open'=>UserModel::getOpenInterview()
		));
	}
	public function admin()
    {
        $this->View->render('login/admin');
    }
	
	public function import999888()
    {
        RegistrationModel::importData();
    }
	
	public function updateTrimName999888()
    {
        RegistrationModel::trimImportData();
    }
}
