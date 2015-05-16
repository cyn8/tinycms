<?php 

namespace Home\Model;
use Think\Model;

class UserModel extends Model {
	public function checkEmailPassword($email, $password) {
		$result = $this -> getByemail($email);

		if($result != NULL) {
			if($result['password'] != $password) {
				return false;
			} else {
				return $result;
			}
			
		} else {
			return false;
		}
	}
}