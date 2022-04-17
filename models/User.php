<?php
class User implements JsonSerializable {
	public $email = '';
	public $id = '';
	public $password = '';
	public $timeStamp = '';
	public $userLevel = '';
	public $userName = '';

	public function jsonSerialize() {
		$json = [
			'email' => $this->email,
			'id' => $this->id,
			'password' => $this->password,
			'timeStamp' => $this->timeStamp,
			'userLevel' => $this->userLevel,
			'userName' => $this->userName];
        return $json;
    }
}
?>
