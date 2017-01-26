<?php
class Comic implements JsonSerializable {
	public $altAttr = '';
	public $category = '';
	public $createdDate = '';
	public $description = '';
	public $fileName = '';
	public $id = '';
	public $keywords = '';
	public $pagingFirst = '';
	public $pagingLast = '';
	public $title = '';
	public $titleAttr = '';
	public $user = '';

	public function jsonSerialize() {
        $array = array(
					"altAttr" => $this->altAttr,
					"category" => $this->category,
					"createdDate" => $this->createdDate,
					"description" => $this->description,
					"fileName" => $this->fileName,
					"id" => $this->id,
					"keywords" => $this->keywords,
					"pagingFirst" => $this->pagingFirst,
					"pagingLast" => $this->pagingLast,
					"title" => $this->title,
					"titleAttr" => $this->titleAttr,
					"user" => $this->user
        		);
		return $array;
    }
}
?>