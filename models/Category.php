<?php
class Category implements JsonSerializable {
	public $description = '';
	public $id = '';
	public $title = '';

	public function jsonSerialize() {
        return [
					'description' => $this->description,
					'id' => $this->id,
					'title' => $this->title
        ];
    }
}
?>
