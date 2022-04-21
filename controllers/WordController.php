<?php
require_once('../autoloader.php');
class WordController 
{
	function __construct(){

	}

	public function WordArchives(){
		$database = new DatabaseController();
		$wordModel = new WordModel();
        $wordArchive = array();

		$query = "SELECT title, url, article, user, category, createdDate FROM content WHERE category != 'rough_draft' AND category != 'comic_ideas' AND category != 'comic' ORDER BY createdDate DESC";
		$stmt = $database->conn->query($query);
		if($stmt){
			$stmt->setFetchMode(PDO::FETCH_INTO, $wordModel);
			while($result = $stmt->fetch()){
				 $wordArchive[] = $result->JsonSerialize();
			}
		}
		return json_encode($wordArchive);
	}

	public function WordSearch($keywords){
        $database = new DatabaseController();
		$wordModel = new WordModel();
		$wordArchive = array();

		$query = "SELECT title, url, article, user, category, createdDate FROM content WHERE category != 'rough_draft' AND category != 'comic_ideas' AND category != 'comic' AND title LIKE :keywords ORDER BY createdDate DESC";
		$stmt = $database->conn->prepare($query);
		if($stmt){
			$stmt->setFetchMode(PDO::FETCH_INTO, $wordModel);
			$stmt->execute(['keywords' => $keywords]);
			while($result = $stmt->fetch()){
				 $wordArchive[] = $result->JsonSerialize();
			}
		}

		return json_encode($wordArchive);
	}

	public function GetArticle() 
	{
		$getPage = '';
		if(isset($_GET['page'])){
        	$getPage = $_GET['page'];
      	}
		
		$database = new DatabaseController();
		$wordModel = new WordModel();

		$query = "SELECT title, article, user, createdDate FROM content WHERE url = :url";
		$stmt = $database->conn->prepare($query);
		if($stmt){
			$stmt->setFetchMode(PDO::FETCH_INTO, $wordModel);
			$stmt->execute(['url' => $getPage]);
			$result = $stmt->fetch();
		}

		$list = $wordModel->JsonSerialize();
		return json_encode($list);		
	}

/*	function remove_dash_and_uc($var){
		$var = preg_replace('/[-\\_]/', " ", $var);
		$var = ucwords($var);
		return $var;
	}*/
}

class WordModel {
	public $title;
	public $url;
	public $article;
	public $createdDate;
	public $id;
	public $user;
	public $category;

	public function JsonSerialize() {
		$array = array(
			"title" => $this->title,
			"url" => $this->url,
			"article" => $this->article,
			"createdDate" => $this->createdDate,
			"id" => $this->id,
			"user" => $this->user,
			"category" => $this->category				
			);
		return $array;
	}
}
?>