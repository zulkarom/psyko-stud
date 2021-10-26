<?php

/**
 * NoteModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class TestModel
{
    /**
     * Get all notes (notes are just example data that the user has created)
     * @return array an array with several objects (the results)
     */
	
	public static function checkAnswerStatus(){
		$result = UserModel::getUserDataById(Session::get('user_id'));
		return $result->answer_status;
		
	}
	
	public static function checkAnswerStatus2(){
		$result = UserModel::getUserDataById(Session::get('user_id'));
		return $result->answer_status2;
		
	}
	
    public static function getAllQuestions()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT a.que_id, a.que_text, a.que_text_bi, b.cat_text, b.cat_text_bi
		FROM psy_question as a
		INNER JOIN psy_question_cat as b
		ON a.display_cat = b.cat_id
		";
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }
	public static function getGradeCat()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT gcat_id, gcat_text 
		FROM psy_grade_cat
		ORDER BY gcat_order ASC";
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }
	public static function getAllQuestionsId()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT que_id
		FROM psy_question
		";
        $query = $database->prepare($sql);
        $query->execute();

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
		
    }
	public static function getAllQuestionsIdCat($cat)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT que_id
		FROM psy_question
		WHERE grade_cat = :cat
		";
        $query = $database->prepare($sql);
        $query->execute(array(':cat'=>$cat));
        return $query->fetchAll();
		
    }
	public static function getAnswersByCat($can,$cat)
    {
        $listQuestion = self:: getAllQuestionsIdCat($cat);
		$array = array();
		foreach($listQuestion as $q){
			$obj = new stdClass();
			$obj->quest = $q->que_id;
			$obj->answer = self::getOneAnswer($can,$q->que_id);
			$array[] = $obj;
		}
		
		return $array;
    }
	
	
	public static function insertAnswerSlot($can)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO psy_answers (can_id) VALUES (:can);
		";
        $query = $database->prepare($sql);
        $query->execute(array(':can'=>$can));
    }
	public static function insertAnswerOverall($can)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO psy_answers_overall (can_id) VALUES (:can);
		";
        $query = $database->prepare($sql);
        $query->execute(array(':can'=>$can));
    }
	
	
	public static function updateLastSaved($time,$quest){
		//echo $quest;
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "UPDATE users SET answer_last_saved = :time, question_last_saved = :quest
		WHERE user_id = :user";
		$query = $database->prepare($sql);
        $query->execute(array(
		':user'=>Session::get('user_id'),':time'=>$time,
		':quest'=>$quest
		));
	}
	
	public static function updateLastSaved2($time,$quest){
		//echo $quest;
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "UPDATE users SET answer_last_saved2 = :time
		WHERE user_id = :user";
		$query = $database->prepare($sql);
        $query->execute(array(
		':user'=>Session::get('user_id'),':time'=>$time
		));
	}
	
	public static function uploadAnswers($user){
	 $database = DatabaseFactory::getFactory()->getConnection();
		$data = Request::post('data');
		$all_row = explode("\n",$data);
		 $array = array(':can'=>$user);
        $sql = "UPDATE psy_answers SET ";
		$co = 1;
		foreach($all_row as $row){
			$arr = explode(",",$row);
			if(count($arr)==2){
				$question = $arr[0];
				$answer = $arr[1];
				if($co==1){$c="";}else{$c=", ";}
				$sql .= $c."q".$question." = :q".$question;
				$jwb = $answer;
				if($jwb == "" or $jwb == null){
					$jwb = -1;
				}
				$array[':q'.$question] = $jwb;
			}
			
		$co++;
		}
		$sql .= " WHERE can_id = :can";
		//echo $sql;
         $query = $database->prepare($sql);
        if(!$query->execute($array)){
			echo 0;
		}else{
			self::setStatus(3,$user);
			echo 1;
		}
		
	}
	
	public static function saveAllAnswers($last)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
		self::updateLastSaved(Request::post('time'),Request::post('qlast'));
		$total = count(self::getAllQuestions()); 
		$array = array(':can'=>Session::get('user_id'));
        $sql = "UPDATE psy_answers SET ";
		$co = 1;
		for($i=1;$i<=$total;$i++){
			if($i >= $last){
				if($co==1){$c="";}else{$c=", ";}
				$sql .= $c."q".$i." = :q".$i;
				$jwb = Request::post('q'.$i);
				if($jwb == "" or $jwb == null){
					$jwb = -1;
				}
				$array[':q'.$i] = $jwb;
				$co++;
			}
		}
		$sql .= " WHERE can_id = :can";
        $query = $database->prepare($sql);
        if(!$query->execute($array)){
			echo 0;
		}else{
			$action = Request::post('aksi');
			if ($action ==0){
				self::setStatus(3, Session::get('user_id'));
			}
			echo 1;
		}
    }
	
	
	
	public static function saveEssay($last)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
		
		self::updateLastSaved2(Request::post('time'),Request::post('qlast'));
		
		$total = count(self::getAllQuestions()); 
		
		$array = array(':can'=>Session::get('user_id'), ':idea' => Request::post('karangan'));
        $sql = "UPDATE psy_answers SET biz_idea = :idea WHERE can_id = :can";
        $query = $database->prepare($sql);
        if(!$query->execute($array)){
			echo 0;
		}else{
			$action = Request::post('aksi');
			if ($action ==0){
				self::setStatus2(3, Session::get('user_id'));
			}
			echo 1;
		}
    }
	
	public static function processOverallStatus($status, $which, $user_id){
		//if 1 cari yg satu lg  - klu 0 set 1
		//if 3 cari yg satu lg - klu 3 set 3
		
		$user = UserModel::getUserDataById($user_id);
		
		if(($status == 1 and $which == 1) 
			or ($status == 1 and $which == 2 and $user->answer_status == 0)){
			self::setOverallStatus(1,$user_id);
		}
		
		if(($status == 3 and $which == 1) or ($status == 3 and $which == 2 and $user->answer_status == 3)){
			self::setOverallStatus(3,$user_id);
			self::setFinishTime($user_id);
		}
	}
	
	public static function setFinishTime($user)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "UPDATE users
			SET finished_at = :time
			WHERE user_id = :user ;";
        $query = $database->prepare($sql);
        $query->execute(array(':time'=>time(),':user'=>$user));
    }
	
	public static function setFinishTime2($user)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "UPDATE users
			SET finished_at = user_last_login_timestamp
			WHERE user_id = :user ;";
        $query = $database->prepare($sql);
        $query->execute(array(':user'=>$user));
    }
	
	public static function setStatus($status,$user)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "UPDATE users
			SET answer_status = :status
			WHERE user_id = :user ;";
        $query = $database->prepare($sql);
        $query->execute(array(':status'=>$status,':user'=>$user));
		self::processOverallStatus($status, 1, $user);
    }
	
	public static function setStatus2($status,$user)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "UPDATE users
			SET answer_status2 = :status
			WHERE user_id = :user ;";
        $query = $database->prepare($sql);
        $query->execute(array(':status'=>$status,':user'=>$user));
		self::processOverallStatus($status, 2, $user);
    }
	
	public static function setOverallStatus($status,$user)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "UPDATE users
			SET overall_status = :status
			WHERE user_id = :user ;";
		$arr = array(':status'=>$status,':user'=>$user);
		//echo $sql;print_r($arr);die();
        $query = $database->prepare($sql);
        $query->execute($arr);
    }
	
	
	/* public static function updateSubTotal($can,$cat,$jum)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "UPDATE psy_answers_overall
			SET c".$cat." = :jum
			WHERE can_id = :can ;";
        $query = $database->prepare($sql);
        $query->execute(array(':can'=>$can,':jum'=>$jum));
    } */
	public static function getSubTotal($can,$cat){
		$result = self::getAllQuestionsIdCat($cat);
		$jum = 0;
		foreach($result as $row){
			$ans = self::getOneAnswer($can,$row->que_id);
			if($ans == 1 or $ans == 0){
				$jum += $ans;
			}
			 
		}
		return $jum;
		
	}
	
	private static function filterResultAnswers($batch=0,$zone=0,$status=9,$can=0){
		if($batch==0){
			$bb = UserModel::getShowingBatch();
			$where_batch = "AND a.can_batch = ".$bb;
		}else{
			$where_batch ="AND a.can_batch = ".$batch;
		}
		if($zone==0){
			$where_zone ="";
		}else{
			$where_zone ="AND a.can_zone = ".$zone;
		}
		if($status==9){
			$where_status ="";
		}else{
			$where_status ="AND a.answer_status = ".$status;
		}
		if($can==0){
			$where_can ="";
		}else{
			$where_can ="AND a.user_id = ".$can;
		}
		
		return $where_batch." ".$where_zone." ".$where_status." ".$where_can ;
	}
	
	
	private static function sortResultAnswers(){
		$sort="ORDER BY a.answer_status DESC ";
		$set_sort = self::getGradeCat();
		foreach($set_sort as $sr){
			$id = $sr->gcat_id;
			$sort .=", c".$id." DESC ";
		}
		return $sort;
	}
	
	private static function columResultAnswers(){
		$result = self::getGradeCat();
		$colum = "a.user_id, a.user_name, a.can_name, a.department, a.can_zone, a.can_batch,  c.bat_text ,
a.answer_status, a.overall_status, a.finished_at, ";
		$c=1;
		foreach($result as $row){
		if($c==1){$comma="";}else{$comma=", ";}
			$quest = self::getAllQuestionsIdCat($row->gcat_id);
			$i=1;
			$jumq = count($quest);
			$colum .= $comma;
			foreach($quest as $rq){
				if($i == $jumq){$plus = "";}else{$plus=" + ";}
				$colum .= "IF(q".$rq->que_id ." > 0,1,0) ". $plus ;
			$i++;
			}
			$colum .= " as c". $row->gcat_id;
		$c++;	
		}
		return $colum;
	}
	
	public static function getAllCandidatesResult($batch=0,$zone=0,$status=9,$can=0, $sorting = 1)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
		if($sorting == 1){
			$sort = self::sortResultAnswers();
		}else{
			$sort  = 'ORDER BY a.finished_at DESC';
		}
		
		$filter = self::filterResultAnswers($batch,$zone,$status,$can);
		$colum = self::columResultAnswers();
        $sql = "SELECT  ".$colum."
		FROM users as a
		
		INNER JOIN psy_batch as c
		ON c.bat_id = a.can_batch
		INNER JOIN psy_answers as d
		ON a.user_id = d.can_id
		WHERE a.user_deleted = 0
		".$filter."
		".$sort."
		";
		//echo $sql;
        $query = $database->prepare($sql);
        $query->execute();

        $all_users_profiles = array();
		//print_r($query->fetchAll());
        foreach ($query->fetchAll() as $user) {
            array_walk_recursive($user, 'Filter::XSSFilter');

            $all_users_profiles[$user->user_id] = new stdClass();
            $all_users_profiles[$user->user_id]->user_id = $user->user_id;
            $all_users_profiles[$user->user_id]->user_name = $user->user_name;
			$all_users_profiles[$user->user_id]->can_name = $user->can_name;
			$all_users_profiles[$user->user_id]->department = $user->department;
			$all_users_profiles[$user->user_id]->can_batch = $user->bat_text;
			if($user->overall_status==0){
				$status="Not Started";
			}else if($user->overall_status==1){
				$status="Started";
			}else if($user->overall_status==3){
				$status="Submitted";
			}
			$all_users_profiles[$user->user_id]->status = $status;
			$all_users_profiles[$user->user_id]->finished_at = $user->finished_at;
			$all_users_profiles[$user->user_id]->c1 = $user->c1;
			$all_users_profiles[$user->user_id]->c2 = $user->c2;
			$all_users_profiles[$user->user_id]->c3 = $user->c3;
			$all_users_profiles[$user->user_id]->c4 = $user->c4;
			$all_users_profiles[$user->user_id]->c5 = $user->c5;
			$all_users_profiles[$user->user_id]->c6 = $user->c6;
			$total = $user->c1 + $user->c2 + $user->c3 + $user->c4 + $user->c5 + $user->c6;
			$all_users_profiles[$user->user_id]->total = $total;
            
        }

        return $all_users_profiles;
    }
	
	public static function getAllAnswersCat($can,$cat){
		
		$database = DatabaseFactory::getFactory()->getConnection();
		$colum =  self::getColumCat($cat);
        $sql = "SELECT ".$colum." FROM psy_answers WHERE can_id = :can 
		LIMIT 1";
		//echo $sql;
        $query = $database->prepare($sql);
        $query->execute(array(':can' => $can));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
		
	}
	
	public static function getEssay($can){
		
		$database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT biz_idea FROM psy_answers WHERE can_id = :can 
		LIMIT 1";
		//echo $sql;
        $query = $database->prepare($sql);
        $query->execute(array(':can' => $can));

        // fetch() is the PDO method that gets a single result
        return $query->fetch()->biz_idea;
		
	}
	
	public static function getOneAnswer($can,$quest){
		//echo $can;
		$database = DatabaseFactory::getFactory()->getConnection();
		$colum = "q".$quest;
        $sql = "SELECT ".$colum." FROM psy_answers WHERE can_id = :can 
		LIMIT 1";
		//echo $sql;
        $query = $database->prepare($sql);
        $query->execute(array(':can' => $can));
        return $query->fetch()->$colum;
		
	}
	
	public static function getEssayAnswer($can){
		//echo $can;
		$database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT biz_idea FROM psy_answers WHERE can_id = :can 
		LIMIT 1";
		//echo $sql;
        $query = $database->prepare($sql);
        $query->execute(array(':can' => $can));
        return $query->fetch()->biz_idea;
		
	}
	
	public static function getColumCat($cat){
		$result = self::getAllQuestionsCat($cat);
		$i=0;
		$colum = "";
		$where = "";
		foreach($result as $row){
			if($i==0){$c="";}else{$c=" + ";}
			$colum .= $c."q".$row->que_id ;
			$where .= " AND q".$row->que_id ." <> -1" ;
		$i++;
		}
		$colum .= " as jumlah ";
		return $colum;
	}
	
	
	


    public static function getNote($note_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, note_id, note_text FROM notes WHERE user_id = :user_id AND note_id = :note_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id'), ':note_id' => $note_id));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    /**
     * Set a note (create a new one)
     * @param string $note_text note text that will be created
     * @return bool feedback (was the note created properly ?)
     */
    public static function createNote($note_text)
    {
        if (!$note_text || strlen($note_text) == 0) {
            Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_CREATION_FAILED'));
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO notes (note_text, user_id) VALUES (:note_text, :user_id)";
        $query = $database->prepare($sql);
        $query->execute(array(':note_text' => $note_text, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_CREATION_FAILED'));
        return false;
    }

    /**
     * Update an existing note
     * @param int $note_id id of the specific note
     * @param string $note_text new text of the specific note
     * @return bool feedback (was the update successful ?)
     */
    public static function updateNote($note_id, $note_text)
    {
        if (!$note_id || !$note_text) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE notes SET note_text = :note_text WHERE note_id = :note_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':note_id' => $note_id, ':note_text' => $note_text, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_EDITING_FAILED'));
        return false;
    }

    /**
     * Delete a specific note
     * @param int $note_id id of the note
     * @return bool feedback (was the note deleted properly ?)
     */
    public static function deleteNote($note_id)
    {
        if (!$note_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "DELETE FROM notes WHERE note_id = :note_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':note_id' => $note_id, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_DELETION_FAILED'));
        return false;
    }
	
	public static function readjusting(){
		//update overall status
		//klu 3 & 3 = 3 //update finished time
		
		//klu salah satu 1 = 1
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT *
		FROM users
		WHERE user_account_type = 1
		";
        $query = $database->prepare($sql);
        $query->execute();
        $list =  $query->fetchAll();
		
		foreach($list as $row){
			$id = $row->user_id;
			if($row->answer_status == 3){
				self::setOverallStatus(3 ,$id);
				self:: setFinishTime2($id);
			}else if($row->answer_status == 1){
				self::setOverallStatus(1 ,$id);
			}
		}
	}
	
	public static function inggeris()
    {
		//echo 'ccc';die();
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT *
		FROM soalan
		";
        $query = $database->prepare($sql);
        $query->execute();
        $all =  $query->fetchAll();
		foreach($all as $q){
			$id = $q->id;
			$text = $q->text;
			echo $text;
			$sql = "UPDATE psy_question SET que_text_bi = :text WHERE que_id = :id";
			$query = $database->prepare($sql);
			$query->execute(array(':id' => $id, ':text' => $text));
			echo 'good';
		}
    }
}
