<?php 
class ComicModel implements JsonSerializable {
	public $cmc_id = '';
	public $cmc_comic = '';
	public $category = '';
	public $cmc_date = '';
	public $cmc_keywords = '';
	public $cmc_post = '';
	public $cmc_title = '';
	public $cmc_titletxt = '';
	public $cmc_user = '';
	public $pagingLast = 1;
 
	public function JsonSerialize() {
		$array = array(
			//"altAttr" => $this->altAttr,
			"category" => $this->category,
			"createdDate" => $this->cmc_date,
			"description" => $this->cmc_post,
			"fileName" => $this->cmc_comic,
			"id" => $this->cmc_id,
			"keywords" => $this->cmc_keywords,
			//"pagingFirst" => $this->pagingFirst,
			"pagingLast" => $this->pagingLast,
			"title" => $this->cmc_title,
			"titleAttr" => $this->cmc_titletxt,
			"user" => $this->cmc_user
			);
		return $array;
	}
}
?>