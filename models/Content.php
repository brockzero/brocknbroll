<?php
class Content implements JsonSerializable {
	public $article = '';
	public $category = '';
	public $createdDate = '';
	public $id = '';
	public $title = '';
	public $url = '';
	public $user = '';

	public function jsonSerialize() {
        return [
					'article' => $this->article,
					'category' => $this->category,
					'createdDate' => $this->createdDate,
					'id' => $this->id,
		      		'title' => $this->title,
					'url' => $this->url,
					'user' => $this->user
        ];
    }
}
?>
