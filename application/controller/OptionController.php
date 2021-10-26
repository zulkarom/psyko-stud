<?php

/**
 * The note controller: Just an example of simple create, read, update and delete (CRUD) actions.
 */
class OptionController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions. If all of your pages should only
        // be usable by logged-in users: Put this line into libs/Controller->__construct
        Auth::checkAuthentication();
    }

    /**
     * This method controls what happens when you move to /note/index in your app.
     * Gets all notes (of the user).
     */
    public function index()
    {
		
        $this->View->render('option/index', array(
		'user'=>UserModel::getUserDataById(Session::get('user_id')),
        ));
    }
	
	public function ujian()
    {
		
        $this->View->render('option/ujian', array(
		'user'=>UserModel::getUserDataById(Session::get('user_id')),
        ));
    }

   
}
