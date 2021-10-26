<?php

class AdminController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // special authentication check for the entire controller: Note the check-ADMIN-authentication!
        // All methods inside this controller are only accessible for admins (= users that have role type 7)
        Auth::checkAdminAuthentication();
    }

    /**
     * This method controls what happens when you move to /admin or /admin/index in your app.
     */
    public function index($batch=0,$task="",$param=0)
    {
		UserModel::prosesTask($task,$param);
	    $this->View->renderAdmin('admin/index', array(
			'users' => UserModel::getAllCandidates($batch),
			'zone' => UserModel::getAllZone(),
			'batch'=> UserModel::getAllBatch(),
			'sbatch'=>$batch,
			'dbatch'=>UserModel::getShowingBatch()
			)
	    );
    }
	public function batch($task="",$param=0)
    {
		UserModel::prosesTask($task,$param);
	    $this->View->renderAdmin('admin/batch', array(
			'batch'=>UserModel::getAllBatches(),
			'show'=>UserModel::getShowingBatch(),
			'open'=>UserModel::getOpenInterview()
			)
	    );
    }
	
	public function recalculate(){
		//update overall status
		//klu 3 & 3 = 3 //update finished time
		
		TestModel::readjusting();
		//klu salah satu 1 = 1
		
	}
	
	public function trash()
    {
		UserModel::trashCan();
	    $this->View->renderAdmin('admin/index', array(
			    'users' => UserModel::getAllCandidates())
				
	    );
    }
	public function allresult($batch=0,$zone=0,$status=9, $can=0, $sorting = 1)
    {
	    $this->View->renderAdmin('admin/allresult', array(
			    'users' => TestModel::getAllCandidatesResult($batch, $zone, $status, $can, $sorting),
				'gcat'=>TestModel::getGradeCat(),
				'batch'=>UserModel::getAllBatch(),
				'zone'=>UserModel::getAllZone(),
				'sbatch'=>$batch,
				'szone'=>$zone,
				'sstatus'=>$status,
				'ssorting'=>$sorting,
				'dbatch'=>UserModel::getShowingBatch()
				)
	    );
    }
	public function allpdfresult($batch=0,$zone=0,$status=9, $can=0)
    {
	    $this->View->renderPdf('admin/allpdfresult', array(
			    'users' => TestModel::getAllCandidatesResult($batch, $zone, $status, $can),
				'gcat'=>TestModel::getGradeCat(),
				'batch'=>UserModel::getAllBatch(),
				'zone'=>UserModel::getAllZone(),
				'sbatch'=>$batch,
				'szone'=>$zone,
				'sstatus'=>$status
				)
	    );
    }
	public function renewpass(){
		UserModel::changePassword();
		 $this->View->renderAdmin('admin/changepass');
	}
	public function newCan()
    {
		RegistrationModel::registerNewCandidate();
	    $this->View->renderAdmin('admin/index', array(
			    'users' => UserModel::getAllCandidates())
	    );
    }
	public function newCanForm()
    {
	    $this->View->renderAdmin('admin/newcanform', array(
			    'zone' => UserModel::getAllZone(),
				'batch'=> UserModel::getAllBatch()
				)
	    );
    }
	public function changepass()
    {
	    $this->View->renderAdmin('admin/changepass');
    }
	public function individual($id=0){
		$this->View->renderWithoutHeaderAndFooter('admin/individualresult',
		array(
		'id'=>$id,
		'user'=>UserModel::getUserDataById($id) ,
		'gcat'=>TestModel::getGradeCat(),
		'essay' => TestModel::getEssayAnswer($id),
		));
	}
	
	public function resultpdf($id=0){
		$this->View->renderPdf('admin/individualpdfresult',
		array(
		'id'=>$id,
		'user'=>UserModel::getUserDataById($id) ,
		'gcat'=>TestModel::getGradeCat(),
		'essay' => TestModel::getEssayAnswer($id),
		));
	}
	
	public function uploadanswers($user=0){
		TestModel::uploadAnswers($user);
	}
	public function downloadoffline(){
		Redirect::to("ksdj23o8headuh2o8374/offline.xls");
	}
	
	public function importdata(){
	    $batch = 2;
	    $zone = 5; // online
	    UserModel::importData($batch, $zone) ;
	}


}
