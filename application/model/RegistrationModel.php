<?php

/**
 * Class RegistrationModel
 *
 * Everything registration-related happens here.
 */
class RegistrationModel
{
	/**
	 * Handles the entire registration process for DEFAULT users (not for people who register with
	 * 3rd party services, like facebook) and creates a new user in the database if everything is fine
	 *
	 * @return boolean Gives back the success status of the registration
	 */
	 public static function registerNewCandidate(){
		$full_name = strip_tags(Request::post('fullname'));
		$user_name = strip_tags(Request::post('username'));
		$department = Request::post('department');
		
		$user_name  = str_replace('-', '', $user_name);

		
		
		$return = true;
		 if (empty($user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_FIELD_EMPTY'));
            $return = false;
        }
        // if username is too short (2), too long (64) or does not fit the pattern (aZ09)
        if (!preg_match('/^[a-zA-Z0-9]{2,64}$/', $user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN'));
            $return = false;
        }
		
		if (UserModel::doesUsernameAlreadyExist($user_name)) {
			Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_ALREADY_TAKEN'));
			$return = false;
		}
		if (!$return) return false;
		// write user data to database
		
		
		$batch = self:: getDefaultBatch();
		
		if (self::writeNewCandidateToDatabase($full_name,$user_name, $department, $batch)) {
			Session::add('feedback_positive', 'Registration Successful.');
			
		}else{
			Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CREATION_FAILED'));
            return false; // no reason not to return false here
			
		}
		
		$user_id = UserModel::getUserIdByUsername($user_name);
		if (!$user_id) {
			Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
			return false;
		}else{
			TestModel::insertAnswerSlot($user_id);
			//TestModel::insertAnswerOverall($user_id);
		}
		
		return true;
		
	 }
	 
	 public static function getDefaultBatch(){
	     $database = DatabaseFactory::getFactory()->getConnection();
	     
	     $query = $database->prepare("SELECT setting_num FROM psy_setting
                                     WHERE set_id = 1");
	     $query->execute();
	     
	     return $query->fetch()->setting_num;
	 }
	 
	
	 
	public static function registerNewUser()
	{
		// clean the input
		$user_name = strip_tags(Request::post('user_name'));
		$user_email = strip_tags(Request::post('user_email'));
		$user_email_repeat = strip_tags(Request::post('user_email_repeat'));
		$user_password_new = Request::post('user_password_new');
		$user_password_repeat = Request::post('user_password_repeat');

		// stop registration flow if registrationInputValidation() returns false (= anything breaks the input check rules)
		$validation_result = self::registrationInputValidation(Request::post('captcha'), $user_name, $user_password_new, $user_password_repeat, $user_email, $user_email_repeat);
		if (!$validation_result) {
			return false;
		}

		// crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
		// @see php.net/manual/en/function.password-hash.php for more, especially for potential options
		$user_password_hash = password_hash($user_password_new, PASSWORD_DEFAULT);

        // make return a bool variable, so both errors can come up at once if needed
        $return = true;

		// check if username already exists
		if (UserModel::doesUsernameAlreadyExist($user_name)) {
			Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_ALREADY_TAKEN'));
			$return = false;
		}

		// check if email already exists
		if (UserModel::doesEmailAlreadyExist($user_email)) {
			Session::add('feedback_negative', Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN'));
			$return = false;
		}

        // if Username or Email were false, return false
        if (!$return) return false;

		// generate random hash for email verification (40 char string)
		$user_activation_hash = sha1(uniqid(mt_rand(), true));

		// write user data to database
		if (!self::writeNewUserToDatabase($user_name, $user_password_hash, $user_email, time(), $user_activation_hash)) {
			Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CREATION_FAILED'));
            return false; // no reason not to return false here
		}

		// get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
		$user_id = UserModel::getUserIdByUsername($user_name);

		if (!$user_id) {
			Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
			return false;
		}

		// send verification email
		if (self::sendVerificationEmail($user_id, $user_email, $user_activation_hash)) {
			Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED'));
			return true;
		}

		// if verification email sending failed: instantly delete the user
		self::rollbackRegistrationByUserId($user_id);
		Session::add('feedback_negative', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED'));
		return false;
	}

	public static function newCandidateInputValidation($user_name)
	{
        if (self::validateUserName($user_name)) {
            return true;
        }

		// otherwise, return false
		return false;
	}
	public static function registrationInputValidation($captcha, $user_name, $user_password_new, $user_password_repeat, $user_email, $user_email_repeat)
	{
        $return = true;

		// perform all necessary checks
		if (!CaptchaModel::checkCaptcha($captcha)) {
			Session::add('feedback_negative', Text::get('FEEDBACK_CAPTCHA_WRONG'));
            $return = false;
		}

        // if username, email and password are all correctly validated, but make sure they all run on first sumbit
        if (self::validateUserName($user_name) AND self::validateUserEmail($user_email, $user_email_repeat) AND self::validateUserPassword($user_password_new, $user_password_repeat) AND $return) {
            return true;
        }

		// otherwise, return false
		return false;
	}

    /**
     * Validates the username
     *
     * @param $user_name
     * @return bool
     */
    public static function validateUserName($user_name)
    {
        if (empty($user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_FIELD_EMPTY'));
            return false;
        }

        // if username is too short (2), too long (64) or does not fit the pattern (aZ09)
        if (!preg_match('/^[a-zA-Z0-9]{2,64}$/', $user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        return true;
    }

    /**
     * Validates the email
     *
     * @param $user_email
	 * @param $user_email_repeat
     * @return bool
     */
    public static function validateUserEmail($user_email, $user_email_repeat)
    {
        if (empty($user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_FIELD_EMPTY'));
            return false;
        }

		if ($user_email !== $user_email_repeat) {
			Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_REPEAT_WRONG'));
			return false;
		}

        // validate the email with PHP's internal filter
        // side-fact: Max length seems to be 254 chars
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        return true;
    }

    /**
     * Validates the password
     *
     * @param $user_password_new
     * @param $user_password_repeat
     * @return bool
     */
    public static function validateUserPassword($user_password_new, $user_password_repeat)
    {
        if (empty($user_password_new) OR empty($user_password_repeat)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_FIELD_EMPTY'));
            return false;
        }

        if ($user_password_new !== $user_password_repeat) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_REPEAT_WRONG'));
            return false;
        }

        if (strlen($user_password_new) < 6) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_TOO_SHORT'));
            return false;
        }

        return true;
    }

	/**
	 * Writes the new user's data to the database
	 *
	 * @param $user_name
	 * @param $user_password_hash
	 * @param $user_email
	 * @param $user_creation_timestamp
	 * @param $user_activation_hash
	 *
	 * @return bool
	 */
	public static function writeNewUserToDatabase($user_name, $user_password_hash, $user_email, $user_creation_timestamp, $user_activation_hash)
	{
		$database = DatabaseFactory::getFactory()->getConnection();

		// write new users data into database
		$sql = "INSERT INTO users (user_name, user_password_hash, user_email, user_creation_timestamp, user_activation_hash, user_provider_type)
                    VALUES (:user_name, :user_password_hash, :user_email, :user_creation_timestamp, :user_activation_hash, :user_provider_type)";
		$query = $database->prepare($sql);
		$query->execute(array(':user_name' => $user_name,
		                      ':user_password_hash' => $user_password_hash,
		                      ':user_email' => $user_email,
		                      ':user_creation_timestamp' => $user_creation_timestamp,
		                      ':user_activation_hash' => $user_activation_hash,
		                      ':user_provider_type' => 'DEFAULT'));
		$count =  $query->rowCount();
		if ($count == 1) {
			return true;
		}

		return false;
	}
	
	public static function writeNewCandidateToDatabase($full_name,$user_name, $department, $batch)
	{
		$database = DatabaseFactory::getFactory()->getConnection();
		// write new users data into database
		$sql = "INSERT INTO users (can_name, user_name, department, can_batch)
                    VALUES (:name, :user_name, :zone, :batch)";
		$query = $database->prepare($sql);
		$query->execute(array(':name' => $full_name,
							':user_name' => $user_name,
		                      ':zone' => $department,
		                      ':batch' => $batch
							  ));
		$count =  $query->rowCount();
		
		if ($count == 1) {
			return true;
		}

		return false;
	}

	/**
	 * Deletes the user from users table. Currently used to rollback a registration when verification mail sending
	 * was not successful.
	 *
	 * @param $user_id
	 */
	public static function rollbackRegistrationByUserId($user_id)
	{
		$database = DatabaseFactory::getFactory()->getConnection();

		$query = $database->prepare("DELETE FROM users WHERE user_id = :user_id");
		$query->execute(array(':user_id' => $user_id));
	}

	/**
	 * Sends the verification email (to confirm the account).
	 * The construction of the mail $body looks weird at first, but it's really just a simple string.
	 *
	 * @param int $user_id user's id
	 * @param string $user_email user's email
	 * @param string $user_activation_hash user's mail verification hash string
	 *
	 * @return boolean gives back true if mail has been sent, gives back false if no mail could been sent
	 */
	public static function sendVerificationEmail($user_id, $user_email, $user_activation_hash)
	{
		$body = Config::get('EMAIL_VERIFICATION_CONTENT') . Config::get('URL') . Config::get('EMAIL_VERIFICATION_URL')
		        . '/' . urlencode($user_id) . '/' . urlencode($user_activation_hash);

		$mail = new Mail;
		$mail_sent = $mail->sendMail($user_email, Config::get('EMAIL_VERIFICATION_FROM_EMAIL'),
			Config::get('EMAIL_VERIFICATION_FROM_NAME'), Config::get('EMAIL_VERIFICATION_SUBJECT'), $body
		);

		if ($mail_sent) {
			Session::add('feedback_positive', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_SUCCESSFUL'));
			return true;
		} else {
			Session::add('feedback_negative', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_ERROR') . $mail->getError() );
			return false;
		}
	}

	/**
	 * checks the email/verification code combination and set the user's activation status to true in the database
	 *
	 * @param int $user_id user id
	 * @param string $user_activation_verification_code verification token
	 *
	 * @return bool success status
	 */
	public static function verifyNewUser($user_id, $user_activation_verification_code)
	{
		$database = DatabaseFactory::getFactory()->getConnection();

		$sql = "UPDATE users SET user_active = 1, user_activation_hash = NULL
                WHERE user_id = :user_id AND user_activation_hash = :user_activation_hash LIMIT 1";
		$query = $database->prepare($sql);
		$query->execute(array(':user_id' => $user_id, ':user_activation_hash' => $user_activation_verification_code));

		if ($query->rowCount() == 1) {
			Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_ACTIVATION_SUCCESSFUL'));
			return true;
		}

		Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_ACTIVATION_FAILED'));
		return false;
	}
	
	public static function importData(){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * 
		FROM import
		";
        $query = $database->prepare($sql);
        $query->execute();
        $list =  $query->fetchAll();
		
		foreach($list as $row){
			$ic = $row->nric;
			$name = trim($row->name);
			$program = $row->program;
			$matric = $row->matric_no;
			self::importNewCandidate($name, $ic, $matric, $program);
		}
	}
	
	public static function importNewCandidate($name, $ic, $matric, $program){
		$zone = 5;
		$batch = 1;
		
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "INSERT INTO users (user_name, matric_no, program, can_name, can_batch, can_zone, user_creation_timestamp, user_provider_type)
                    VALUES (:user_name, :matric_no, :program, :con_name,  :con_batch, :con_zone, :user_creation_timestamp, :user_provider_type)";
		$query = $database->prepare($sql);
		$result = $query->execute(array(
								':user_name' => $ic,
								':con_name' => $name,
								':matric_no' => $matric,
								':program' => $program,
								':con_batch' => 1,
								':con_zone' => 5,
		                      ':user_creation_timestamp' => time(),
		                      ':user_provider_type' => 'DEFAULT'));
		

		
		if ($result) {

		}else{
			Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CREATION_FAILED'));
            return false; // no reason not to return false here
			
		}
		
		$user_id = UserModel::getUserIdByUsername($ic);
		if (!$user_id) {
			Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
			return false;
		}else{
			echo '('.$matric.' '.$name.') DONE<br />';
			TestModel::insertAnswerSlot($user_id);
			//TestModel::insertAnswerOverall($user_id);
		}
		

		
	 }
	 
}
