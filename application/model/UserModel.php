<?php

/**
 * UserModel
 * Handles all the PUBLIC profile stuff. This is not for getting data of the logged in user, it's more for handling
 * data of all the other users. Useful for display profile information, creating user lists etc.
 */
class UserModel
{
    /**
     * Gets an array that contains all the users in the database. The array's keys are the user ids.
     * Each array element is an object, containing a specific user's data.
     * The avatar line is built using Ternary Operators, have a look here for more:
     * @see http://davidwalsh.name/php-shorthand-if-else-ternary-operators
     *
     * @return array The profiles of all users
     */
	 
	
	 
    public static function getPublicProfilesOfAllUsers()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, user_name, user_email, user_active, user_has_avatar, user_deleted,can_name, can_zone, can_batch FROM users";
        $query = $database->prepare($sql);
        $query->execute();

        $all_users_profiles = array();

        foreach ($query->fetchAll() as $user) {

            // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
            // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
            // the user's values
            array_walk_recursive($user, 'Filter::XSSFilter');

            $all_users_profiles[$user->user_id] = new stdClass();
            $all_users_profiles[$user->user_id]->user_id = $user->user_id;
            $all_users_profiles[$user->user_id]->user_name = $user->user_name;
            $all_users_profiles[$user->user_id]->user_email = $user->user_email;
            $all_users_profiles[$user->user_id]->user_active = $user->user_active;
            $all_users_profiles[$user->user_id]->user_deleted = $user->user_deleted;
			$all_users_profiles[$user->user_id]->can_name = $user->can_name;
			$all_users_profiles[$user->user_id]->can_zone = $user->can_zone;
			$all_users_profiles[$user->user_id]->can_batch = $user->can_batch;
            
        }

        return $all_users_profiles;
    }
	public static function getAllZone()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT zone_id,zone_text FROM psy_zone
		ORDER BY zone_order ASC
		";
        $query = $database->prepare($sql);
        $query->execute();

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }
	
	public static function prosesTask($task,$param){
		switch($task){
			case "edituser":
			self::editUser($param);
			break;
			case "savebatch":
			self::saveShowingBatch();
			break;
			case "newcan":
			RegistrationModel::registerNewCandidate();
			break;
			case "trashcan":
			self::trashCan();
			break;
			//
		}
	}
	public static function editUser($user)
    {
		$ruser = self::editUserName($user);
        $second = self::editNameBatchZone($user);
		if($ruser and $second){
			Session::add('feedback_positive', 'Updating User Successful.');
		}
    }
	
	public static function editNameBatchZone($user){
		$database = DatabaseFactory::getFactory()->getConnection();
		$name = Request::post('fullname');
		$batch = Request::post('batch');
		$zone = Request::post('zone');
        $query = $database->prepare("UPDATE users 
		SET can_name = :name,
		can_batch = :batch,
		can_zone = :zone
		WHERE user_id = :user LIMIT 1");
        $result = $query->execute(array(
		':user' => $user,
		':name' => $name, 
		':batch' => $batch,
		':zone' => $zone
		));
        if ($result) {
            return true;
        }
        Session::add('feedback_negative', 'Updating User Failed!');
	}
	
	public static function getAllBatches()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT a.bat_id, a.bat_text 
		FROM psy_batch as a
		ORDER BY a.bat_id DESC
		";
        $query = $database->prepare($sql);
        $query->execute();

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }
	
	public static function getAllBatch()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT bat_id, bat_text FROM psy_batch
		ORDER BY bat_text DESC LIMIT 10
		";
        $query = $database->prepare($sql);
        $query->execute();

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }
	public static function getAllCandidates($batch=0)
    {
		if($batch==0){
			$batch = self::getShowingBatch();
		}
		//echo $batch;
        $database = DatabaseFactory::getFactory()->getConnection();
		
        $sql = "SELECT a.user_id, a.user_name, a.answer_status, a.finished_at,
		a.user_deleted, a.can_name, a.can_zone, a.can_batch, c.bat_text 
		FROM users as a
		INNER JOIN psy_batch as c
		ON c.bat_id = a.can_batch
		WHERE a.user_deleted = 0
		AND a.can_batch = :batch
		ORDER BY a.overall_status DESC, a.finished_at DESC
		";
        $query = $database->prepare($sql);
        $query->execute(array('batch'=>$batch));

        $all_users_profiles = array();

        foreach ($query->fetchAll() as $user) {
            array_walk_recursive($user, 'Filter::XSSFilter');
            $all_users_profiles[$user->user_id] = new stdClass();
            $all_users_profiles[$user->user_id]->user_id = $user->user_id;
            $all_users_profiles[$user->user_id]->user_name = $user->user_name;
            $all_users_profiles[$user->user_id]->user_deleted = $user->user_deleted;
			$all_users_profiles[$user->user_id]->can_name = $user->can_name;
		
			$all_users_profiles[$user->user_id]->can_batch = $user->bat_text;
			$all_users_profiles[$user->user_id]->can_zone_id = $user->can_zone;
			$all_users_profiles[$user->user_id]->can_batch_id = $user->can_batch;
			$all_users_profiles[$user->user_id]->finished_at = $user->finished_at;
			if($user->answer_status==0){
				$status="Not Started";
			}else if($user->answer_status==1){
				$status="Started";
			}else if($user->answer_status==3){
				$status="Submitted";
			}
			$all_users_profiles[$user->user_id]->status = $status;
			
            
        }

        return $all_users_profiles;
    }
	

    /**
     * Gets a user's profile data, according to the given $user_id
     * @param int $user_id The user's id
     * @return mixed The selected user's profile
     */
    public static function getPublicProfileOfUser($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, user_name, user_email, user_active, user_has_avatar, user_deleted
                FROM users WHERE user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));

        $user = $query->fetch();

        if ($query->rowCount() == 1) {
            if (Config::get('USE_GRAVATAR')) {
                $user->user_avatar_link = AvatarModel::getGravatarLinkByEmail($user->user_email);
            } else {
                $user->user_avatar_link = AvatarModel::getPublicAvatarFilePathOfUser($user->user_has_avatar, $user->user_id);
            }
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_USER_DOES_NOT_EXIST'));
        }

        // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
        // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
        // the user's values
        array_walk_recursive($user, 'Filter::XSSFilter');

        return $user;
    }

    /**
     * @param $user_name_or_email
     *
     * @return mixed
     */
    public static function getUserDataByUserNameOrEmail($user_name_or_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT user_id, user_name, user_email FROM users
                                     WHERE (user_name = :user_name_or_email OR user_email = :user_name_or_email)
                                           AND user_provider_type = :provider_type LIMIT 1");
        $query->execute(array(':user_name_or_email' => $user_name_or_email, ':provider_type' => 'DEFAULT'));

        return $query->fetch();
    }

    /**
     * Checks if a username is already taken
     *
     * @param $user_name string username
     *
     * @return bool
     */
    public static function doesUsernameAlreadyExist($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT user_id FROM users WHERE user_name = :user_name LIMIT 1");
        $query->execute(array(':user_name' => $user_name));
        if ($query->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Checks if a email is already used
     *
     * @param $user_email string email
     *
     * @return bool
     */
    public static function doesEmailAlreadyExist($user_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT user_id FROM users WHERE user_email = :user_email LIMIT 1");
        $query->execute(array(':user_email' => $user_email));
        if ($query->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Writes new username to database
     *
     * @param $user_id int user id
     * @param $new_user_name string new username
     *
     * @return bool
     */
    public static function saveNewUserName($user_id, $new_user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET user_name = :user_name WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(':user_name' => $new_user_name, ':user_id' => $user_id));
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    /**
     * Writes new email address to database
     *
     * @param $user_id int user id
     * @param $new_user_email string new email address
     *
     * @return bool
     */
    public static function saveNewEmailAddress($user_id, $new_user_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET user_email = :user_email WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(':user_email' => $new_user_email, ':user_id' => $user_id));
        $count = $query->rowCount();
        if ($count == 1) {
            return true;
        }
        return false;
    }
	public static function saveNewPassword($user_id, $password)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET user_password_hash = :pass WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(':pass' => $password, ':user_id' => $user_id));
        $count = $query->rowCount();
        if ($count == 1) {
            return true;
        }
        return false;
    }

    /**
     * Edit the user's name, provided in the editing form
     *
     * @param $new_user_name string The new username
     *
     * @return bool success status
     */
    public static function editUserName($user)
    {
		$new_user_name = Request::post('username');
		$all = self::getUserDataById($user);
		$old = $all->user_name;
		if($new_user_name == $old){
			Session::add('feedback_positive', 'There is no change for username.');
			return true;
		}
		
		
        // username cannot be empty and must be azAZ09 and 2-64 characters
        if (!preg_match("/^[a-zA-Z0-9]{2,64}$/", $new_user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        // clean the input, strip usernames longer than 64 chars (maybe fix this ?)
        $new_user_name = substr(strip_tags($new_user_name), 0, 64);

        // check if new username already exists
        if (self::doesUsernameAlreadyExist($new_user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_ALREADY_TAKEN'));
            return false;
        }

        $status_of_action = self::saveNewUserName($user, $new_user_name);
        if ($status_of_action) {
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
            return false;
        }
    }

    /**
     * Edit the user's email
     *
     * @param $new_user_email
     *
     * @return bool success status
     */
    public static function editUserEmail($new_user_email)
    {
        // email provided ?
        if (empty($new_user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_FIELD_EMPTY'));
            return false;
        }

        // check if new email is same like the old one
        if ($new_user_email == Session::get('user_email')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_SAME_AS_OLD_ONE'));
            return false;
        }

        // user's email must be in valid email format, also checks the length
        // @see http://stackoverflow.com/questions/21631366/php-filter-validate-email-max-length
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if (!filter_var($new_user_email, FILTER_VALIDATE_EMAIL)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        // strip tags, just to be sure
        $new_user_email = substr(strip_tags($new_user_email), 0, 254);

        // check if user's email already exists
        if (self::doesEmailAlreadyExist($new_user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN'));
            return false;
        }

        // write to database, if successful ...
        // ... then write new email to session, Gravatar too (as this relies to the user's email address)
        if (self::saveNewEmailAddress(Session::get('user_id'), $new_user_email)) {
            Session::set('user_email', $new_user_email);
            Session::set('user_gravatar_image_url', AvatarModel::getGravatarLinkByEmail($new_user_email));
            Session::add('feedback_positive', Text::get('FEEDBACK_EMAIL_CHANGE_SUCCESSFUL'));
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
        return false;
    }

    /**
     * Gets the user's id
     *
     * @param $user_name
     *
     * @return mixed
     */
    public static function getUserIdByUsername($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id FROM users WHERE user_name = :user_name  LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_name' => $user_name));

        // return one row (we only have one result or nothing)
        return $query->fetch()->user_id;
    }
	
	
	
	public static function saveShowingBatch()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
		$num = Request::post('shwbatch');
        $sql = "UPDATE psy_setting
		SET setting_num  = :num
		WHERE set_id = 1";
        $query = $database->prepare($sql);
        $query->execute(array(':num'=>$num));
		
		$open = Request::post('open');
        $sql = "UPDATE psy_setting
		SET setting_num  = :open
		WHERE set_id = 2";
        $query = $database->prepare($sql);
        $query->execute(array(':open'=>$open));
		
		Session::add('feedback_positive', 'Setting updated');

    }
	
	public static function getShowingBatch()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT setting_num FROM psy_setting 
		WHERE set_id = 1 LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute();

        // return one row (we only have one result or nothing)
        return $query->fetch()->setting_num;
    }
	
	public static function getOpenInterview()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT setting_num FROM psy_setting 
		WHERE set_id = 2 LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute();

        // return one row (we only have one result or nothing)
        return $query->fetch()->setting_num;
    }

    /**
     * Gets the user's data
     *
     * @param $user_name string User's name
     *
     * @return mixed Returns false if user does not exist, returns object with user's data when user exists
     */
	 //
    public static function getUserDataByUsername($user_name)
    {
		//echo ""
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT user_id, user_name,user_password_hash, user_active,user_deleted, user_suspension_timestamp, user_account_type, user_failed_logins, user_last_failed_login, answer_status
		FROM users
		WHERE user_name = :user_name
		LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_name' => $user_name));
        return $query->fetch();
    }
	public static function getUserDataByLogin($user_name)
    {
		//echo ""
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT user_id, user_name,user_password_hash, user_active,user_deleted, user_suspension_timestamp, user_account_type, user_failed_logins, user_last_failed_login, answer_status
		FROM users
		WHERE user_name = :user_name
		AND user_account_type = 1
		LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_name' => $user_name));
        return $query->fetch();
    }
	
	public static function getStudentDataByLogin($matric, $ic)
    {
		//echo ""
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT user_id, matric_no, program, user_name,user_password_hash, user_active,user_deleted, user_suspension_timestamp, user_account_type, user_failed_logins, user_last_failed_login, answer_status
		FROM users
		WHERE user_name = :user_name
		AND user_account_type = 1 AND matric_no = :matric
		LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_name' => $ic, ':matric' => $matric));
        return $query->fetch();
    }
	
	
	public static function changePassword(){
		$current = Request::post('user_password_current');
		$new = Request::post('user_password_new');//
		$repeat = Request::post('user_password_new_repeat');
		$result = self::getUserDataByUsername(Session::get('user_name'));
		
		if (!password_verify($current, $result->user_password_hash)) {
			Session::add('feedback_negative', 'Wrong current password'); 
			return false;
		}
		if ($new != $repeat) {
			Session::add('feedback_negative', 'Repeat password does not match'); 
			return false;
		}

		$user_password_hash = password_hash($new, PASSWORD_DEFAULT);
		
		if(self::saveNewPassword(Session::get('user_id'), $user_password_hash)){
			Session::add('feedback_positive', 'Update Password Successful');
		}
		
	}
	public static function trashCan(){
		$user = Request::post('trash_can');
		$database = DatabaseFactory::getFactory()->getConnection();
        $sql = "DELETE FROM users
		WHERE user_id = :user";
        $query = $database->prepare($sql);
		
        if($query->execute(array(':user' => $user))){
			Session::add('feedback_positive', 'Trashing Candidate Id '.$user.' Successful');
		}

		
	}
	public static function getUserDataById($user_id)
    {
		//echo ""
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT a.user_id, a.user_name, a.matric_no, a.department, a.program, a.can_zone, a.can_name, 
a.can_batch, a.answer_status, a.answer_last_saved,a.answer_status2, a.overall_status, 
a.finished_at, a.answer_last_saved2, a.question_last_saved, c.bat_text
		FROM users as a
		INNER JOIN psy_batch as c
		ON c.bat_id = a.can_batch
		WHERE user_id = :user
		LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user' => $user_id));
        return $query->fetch();
    }

    /**
     * Gets the user's data by user's id and a token (used by login-via-cookie process)
     *
     * @param $user_id
     * @param $token
     *
     * @return mixed Returns false if user does not exist, returns object with user's data when user exists
     */
    public static function getUserDataByUserIdAndToken($user_id, $token)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        // get real token from database (and all other data)
        $query = $database->prepare("SELECT user_id, user_name, user_email, user_password_hash, user_active,
                                          user_account_type,  user_has_avatar, user_failed_logins, user_last_failed_login
                                     FROM users
                                     WHERE user_id = :user_id
                                       AND user_remember_me_token = :user_remember_me_token
                                       AND user_remember_me_token IS NOT NULL
                                       AND user_provider_type = :provider_type LIMIT 1");
        $query->execute(array(':user_id' => $user_id, ':user_remember_me_token' => $token, ':provider_type' => 'DEFAULT'));

        // return one row (we only have one result or nothing)
        return $query->fetch();
    }
    
    public static function importData($batch, $zone){
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT *
		FROM data_import";
        $query = $database->prepare($sql);
        $query->execute();
        $list = $query->fetchAll();
        if($list){
            foreach($list as $can){
                $sql = "INSERT INTO users (can_name, user_name, can_batch, can_zone) VALUES (:name, :ic, :batch, :zone);";
                $query = $database->prepare($sql);
                $query->execute(array(':name'=>$can->can_name, ':ic' => $can->can_ic, ':zone' => $zone, ':batch' => $batch));
                
                $user_id = self::getUserIdByUsername($can->can_ic);
                $sql = "INSERT INTO psy_answers (can_id) VALUES (:id);";
                $query = $database->prepare($sql);
                $query->execute(array(':id' => $user_id));
                
                
                echo $can->can_name . ' -  ' . $can->can_ic. ': good <br />';
            }
        }
    }
}
