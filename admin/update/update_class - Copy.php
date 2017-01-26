<?php
class Update 
{	
	public $editing_menu = '<p>[<a href="/comic">Site Home</a>] [<a href="/main">Acount Management</a>] [<a href="/update">Update Home</a>] - [<a href="/update/article_update">New Article</a>] [<a href="/update/article_view">View/Edit Articles</a>] - [<a href="/update/comic_update">New Comic</a>] [<a href="/update/comic_view">View/Edit Comics</a>]</p>';
	
	function getComicIdeas() {
		global $database;
		$var;
		$query = "SELECT title, article, user, DATE_FORMAT(date,'%a, %M %D, %Y - %h:%i %p') AS f_date FROM content WHERE category = 'comic_ideas' ORDER BY date DESC";
		$stmt = $database->query($query);
		while($r = $stmt->fetch_assoc()){
			$var .= '<h3>' . $r['title'] . '</h3>
			' . $r['article'] .'
			<h6>By: ' . $r['user'] . ' | On: ' . $r['f_date'] . '</h6>
			<hr>
			<p>&nbsp;</p>';
		}
		$stmt->close();
		return $var;	
	}
	
	function articleView(){
		global $database;
		$catID = $this->remove_dash_and_uc($_GET['id']);
		$categoryMenu = $this->categoryMenu(); //local function to generate the categories menu
		//change to a proper function
		
		$var = '<h3>' . $catID . ' Articles
		' . $categoryMenu . '
		</h3>
		<table class="tableStyle">
		<tr>
		<th>Article</th><th>Category</th><th>Posted By</th><th>Date</th><th>View</th><th>Delete</th>
		</tr>';
		if(isset($_GET['id'])) {
			$_GET['id'] = $database->real_escape_string($_GET[id]); //escape data taken from the url
			//query for specific categories
			$query = "SELECT title, article, user, category, id, url, DATE_FORMAT(date,'%Y-%m-%d') AS f_date FROM content WHERE category = '$_GET[id]' ORDER BY date DESC LIMIT 500"; 
		} else {
			$query = "SELECT title, article, user, category, id, url, DATE_FORMAT(date,'%Y-%m-%d') AS f_date FROM content WHERE category != 'comic' ORDER BY date DESC LIMIT 500";
		}
		$stmt = $database->query($query);
		$i=1;
		while($r = $stmt->fetch_assoc()) {
			$var .= '<tr>
			<td><a href="/update/article_edit/' . $r['id']  . '">' . $r['title'] . '</a></td>
			<td>' . $r['category'] . '</td>
			<td>' . $r['user'] . '</td>
			<td>' . $r['f_date'] . '</td>
			<td><a href="/words/' . $r['url'] . '">View</a></td>
			<td><a href="/update/delete_article/' . $r['id'] . '">Delete</a></td></tr>';
		}
		$var .= '</table>';
		return $var;
	}
	
	function getArticleUpdateForm() {
		global $form;
		$var = '<form class="updateForm" name="updateForm" method="post" action="http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '">
		<p>Title<br>
		<input type="text" size="64" name="title" value="' . $form->getValue('title') . '"> ' . $form->getError('title') . '
		<input type="hidden" name="user" value="' . $_SESSION['username'] . '"></p>
		<p>Article</p> 
		<textarea id="article" name="article">' . $form->getValue('article') . '</textarea> ' . $form->getError('article');
		$var .= $this->getArticleCategory($form->getValue('category'));
		$var .= '<br>
		<p><input type="submit" value="Submit"> | <input type="button" value="Toggle Editor" onClick="toggleEditor(\'article\')"> | <input type="button" value="Achievement Code" onClick="addAchievement()"></p>
		</form>';
		return $var;		
	}
	
	//creates the category options menu.
	function getArticleCategory($default) {
		global $database;
		$var = '<p>Category<br>
		<select name="category">';
		if ($default == NULL) {
			$var .= '<option value="rough_draft">Category</option>';
		} else {
			$var .= '<option value="' . $default . '">' . $this->remove_dash_and_uc($default) . '</option>';
		}
		$query = "SELECT category, catURL FROM category WHERE category != 'Comic'"; 
		$stmt = $database->query($query);
		while($r = $stmt->fetch_assoc()) {
			$var .= '<option value="' . $r['catURL'] . '">' . $r['category'] . '</option>';
		}
		$stmt->close();
		$var .= '</select></p>';
		return $var;	
	}
	
	function articleUpdate(){
		global $database, $form;
		//Need error checking for forms so required fields aren't posted blank
		$var;
		if ($_SERVER['REQUEST_METHOD'] != 'POST'){
			$var .= '<h2>New Article</h2>';
			$var .= $this->getArticleUpdateForm();
		} else {
			foreach ($_POST as $key => $value){
				$form->setValue($key, $value);
				if (empty($value)) {
					$form->setError($key, 'Empty field');	
				}
			}
			if (empty($_POST['title']) || empty($_POST['article']) || empty($_POST['user'])) {
				$var .= '<h2>New Article</h2>';
				$var .= $this->getArticleUpdateForm();
			} else {	
				$url = $this->titleToUrl($_POST[title]);
				$query = "INSERT INTO content (title, url, article, date, id, user, category) VALUES (?, ?, ?, NOW(), NULL, ?, ?)";
				$stmt = $database->prepare($query);
				$stmt->bind_param("sssss", $_POST['title'], $url, $_POST['article'], $_POST['user'], $_POST['category']);
				$stmt->execute();
				$stmt->close();
				$var .= '<h2>Article Updated</h2>
				<p>' . $_POST['title'] . '</p>
				<p>' . $_POST['category'] . '</p> 
				<p>' . $_POST['article'] . '</p>
				<p>' . $_POST['user'] . '</p>
				<p>' . $url . '</p>';
			}
		}
		return $var;
	}
	
	function getArticleEditForm() {
		global $form;
		$var = '<form class="updateForm" name="updateForm" method="post" action="http://' . $_SERVER['SERVER_NAME']. $_SERVER['REQUEST_URI'] . '">
		<p>Title<br>
		<input type="text" size="64" name="title" value="' . $form->getValue('title') . '"> ' . $form->getError('title') . '
		<input type="hidden" name="user" value="' . $_SESSION['username'] . '"></p>
		<p>Article</p> 
		<textarea id="article" name="article">' . $form->getValue('article') . '</textarea> ' . $form->getError('article');
		$var .= $this->getArticleCategory($form->getValue('category'));
		$var .= '<br>
		<p><input type="submit" value="Submit"> | <input type="button" value="Toggle Editor" onClick="toggleEditor(\'article\')"> | <input type="button" value="Achievement Code" onClick="addAchievement()"></p>
		</form>';
		return $var;		
	}
	
	
	function articleEdit(){
		global $database, $form;
		$var;
		//title 	url 	article 	date 	id 	user 	category
		//try if $a[user] = $_SESSION[username] to security check edits
		if (!empty($_GET['id']) && $_GET['page'] == 'article_edit') {
			if ($_SERVER['REQUEST_METHOD'] != 'POST'){
				$_GET['id'] = $database->real_escape_string($_GET['id']);
				$query = "SELECT title, article, user, category FROM content WHERE id = ?";
				$stmt = $database->prepare($query);
				$stmt->bind_param("i", $_GET['id']);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($title, $article, $user, $category);
				$stmt->fetch();
				$stmt->close();			
				$form->setValue('title', $title);
				$form->setValue('article', $article);
				$form->setValue('user', $user);
				$form->setValue('category', $category);
				$var .= '<h2>Article Edit</h2>';
				$var .= $this->getArticleEditForm();
			} else {
				foreach ($_POST as $key => $value){
					$form->setValue($key, $value);
					if (empty($value)) {
						$form->setError($key, 'Empty field');	
					}
				}
				if (empty($_POST['title']) || empty($_POST['article']) || empty($_POST['user'])) {
					$var .= '<h2>Article Edit</h2>';
					$var .= $this->getArticleUpdateForm();
				} else {	
					$url = $this->titleToUrl($_POST[title]);					
					$query = "UPDATE content SET title = ?, url = ?,  article = ?, category = ? WHERE id = ?";
					$stmt = $database->prepare($query);
					$stmt->bind_param("ssssi", $_POST['title'], $url, $_POST['article'], $_POST['category'], $_GET['id']);
					$stmt->execute();
					$stmt->close();
					//MySQL update query
					$var .= '<p>' .  $this->categoryMenu() . '</p>
					<h2>Article Edited</h2>
					<p>' . $_POST['title'] . '</p>
					<p>' . $_POST['category'] . '</p>
					' . $_POST['article'] . '
					<p>' . $url . '</p>';
				}
			} 
		} else {
			$var .= '<p>No page to edit, please try again.</p>';
		}
		return $var;
	}
	
	function deleteArticle(){
		global $database;
		$var = '<h2>Delete</h2>';
		if (!empty($_GET[id])) {
			if ($_SERVER['REQUEST_METHOD'] != 'POST'){
				$query = "SELECT title, article FROM content WHERE id = ?";
				$stmt = $database->prepare($query);
				$stmt->bind_param("i",$_GET['id']);
				$stmt->execute();
				$stmt->store_result(); //needed for longer results, more research
				$stmt->bind_result($title, $article);
				$stmt->fetch();
				$stmt->close();
				$var .= '<h3>' . $title . '</h3>
				' . $article . '
				<form method="post" action="http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '">
				<input type="hidden" name="title" value="' . $title . '">
				<p><input type="submit" value="Delete Article?"></p>
				</form>';		
			} else {
				$query = "DELETE FROM content WHERE id = ? LIMIT 1";
				$stmt = $database->prepare($query);
				$stmt->bind_param("i",$_GET['id']);
				$stmt->execute();
				$stmt->close();
				$var .= '<p>' . $_POST['title'] . ' was deleted!</p>
				<p>Return to <a href="/update/article_view">Article View</a></p>';
			}
		} else {
			$var .= '<p>Content was not found. Could not Delete</p>';
		}
		return $var;
	}
	
	function comicUpdate(){
		global $database;
		if ($_SERVER['REQUEST_METHOD'] != 'POST'){
			$query = "SELECT cmc_comic FROM comics";
			$stmt = $database->query($query);
			$next_row = $stmt->num_rows + 1;
			if ($next_row < 1000) {
				$next_row = "0".$next_row;
			} 
			$stmt->close();
			$var = '<h2>Comic Update</h2>
			<form  name="updateForm" method="post" action="http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '">
			<p>Title</p>
			<p><input type="text" size="64" name="title"></p>
			<p>Commentary</p>
			<textarea id="article" name="cmc_post" style="width:780px;height:400px;"></textarea>
			<p><input type="button" value="Toggle Editor" onClick="toggleEditor(\'article\')"></p>
			<input type="hidden" name="user" value="' . $_SESSION['username'] . '">
			<p>Title Attribute</p>
			<textarea name="titletxt" style="width:780px;height:50px;"></textarea>
			<p>Comic</p>
			<p><input type="text" size="64" name="comic" value="' . $next_row . '.jpg"></p>
			<p>Keywords</p>
			<p><input type="text" size="64" name="keywords"></p>
			<p><input type="submit" value="Submit Comic"> </p>
			</form>';
			return $var;
		} else {
			$query = "INSERT INTO comics (cmc_comic , cmc_title , cmc_post , cmc_titletxt , cmc_keywords, cmc_date, cmc_id, cmc_user) VALUES (?,?,?,?,?,NOW(),NULL,?)";
			$stmt = $database->prepare($query);
			$stmt->bind_param("ssssss",$_POST['comic'],$_POST['title'],$_POST['cmc_post'],$_POST['titletxt'],$_POST['keywords'],$_POST['user']);
			$stmt->execute();
			$stmt->close();
			$var = '<h2>Comic Updated</h2>
			<h5>Inserted into comic table</h5>
			<p>Title: ' . $_POST['title'] . '</p>
			<p>User: ' . $_POST['user'] . '</p>
			<p style="text-align: center;"><img src="/comics/' . $_POST['comic'] . '"></p>
			<p>Keywords: ' . $_POST['keywords'] . '</p>
			<div>Commentary</div>
			' . $_POST['commentary'] . '
			<div>Title Attribute</div>
			<p>' . $_POST['titletxt'] . '</p>';	
			return $var;
		}
	}
	
	function comicView(){
		global $database;
		$var = '<h2>Comic List</h2>
		<table class="tableStyle">
		<tr>
		<th>Title</th><th>Posted By</th><th>Date</th>
		</tr>';
		// get the page and data
		$query = "SELECT cmc_title, cmc_user, cmc_id, DATE_FORMAT(cmc_date,'%Y-%m-%d') AS f_date FROM comics ORDER BY cmc_id DESC";
		$stmt = $database->query($query);
		while($r = $stmt->fetch_assoc()) {
			$var .= '<tr>
			<td><a href="/update/comic_edit/' . $r['cmc_id'] . '">' . $r['cmc_title'] . '</a></td>
			<td>' . $r[cmc_user] . '</td>
			<td>' . $r[f_date] . '</td></tr>';
		}
		$var .= '</table>';
		return $var;
	}
	
	function comicEdit(){
		global $database;
		if (!empty($_GET[id]) && $_GET['page'] == 'comic_edit') {
			//$_GET[id] = $database->real_escape_string($_GET[id]);
			if ($_SERVER['REQUEST_METHOD'] != 'POST'){
				
				/* look into using joins or subqueries to get this into one query if possible */
				$query = "SELECT cmc_comic, cmc_post, cmc_titletxt, cmc_title, cmc_keywords FROM comics WHERE cmc_id = ?";
				$stmt = $database->prepare($query);
				$stmt->bind_param("i",$_GET['id']);
				$stmt->execute();
				$stmt->bind_result($cmc_comic, $cmc_post, $cmc_titletxt, $cmc_title, $cmc_keywords);
				$stmt->fetch();
				$stmt->close();
				$var = '<h2>Comic Edit</h2>
				<form name="updateForm" method="post" action="http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '">
				<p>Title</p>
				<p><input type="text" size="64" name="title" value="' . $cmc_title . '"></p>
				<p>Commentary</p>
				<textarea id="article" name="cmc_post" style="width:780px;height:400px;">' . $cmc_post . '</textarea>
				<p><input type="button" value="Toggle Editor" onClick="toggleEditor(\'article\')"></p>
				<p>Title Attribute</p>
				<textarea name="titletxt" style="width:780px;height:50px;">' . $cmc_titletxt .'</textarea>
				<p>Comic</p>
				<input type="text" size="64" name="comic" value="' . $cmc_comic . '">
				<p>Keywords</p>
				<input type="text" size="64" name="keywords" value="' . $cmc_keywords . '">
				<p><input type="submit" value="Submit Comic"></p>
				<p><img src="comic/' . $cmc_comic . '" alt="' . $cmc_keywords . '"></p>';
				return $var;
			} else {
				//cmc_comic 	cmc_title 	cmc_post 	cmc_titletxt 	cmc_keywords 	cmc_date 	cmc_id 	cmc_user 	category
				$query = "UPDATE comics SET cmc_comic = ?, cmc_title = ?, cmc_post = ?, cmc_titletxt = ?, cmc_keywords = ? WHERE cmc_id = ?";
				$stmt = $database->prepare($query);
				$stmt->bind_param("sssssi",$_POST['comic'],$_POST['title'],$_POST['cmc_post'],$_POST['titletxt'],$_POST['keywords'],$_GET['id']);
				$stmt->execute();
				$stmt->close();
				$var = '<h2>Comic Edited</h2>
				<p><strong>Title: </strong>' . $_POST['title'] . '</p>
				<p style="text-align:center;"><img src="comic/' . $_POST['comic'] .'"></p>
				<h6>Commentary:</h6>
				' . $_POST['cmc_post'] . '
				<h6>Title Attribute:</h6>
				<p>' . $_POST['titletxt'] . '</p>
				<hr>
				<p>Keywords: ' . $_POST['keywords'] . '</p>';
				return $var;	
			} 
		} else {
			return '<p>No page to edit, please try again.</p>';
		}
	}
	
	function categoryMenu(){
		global $database;
		$query = "SELECT category, catURL, description FROM category WHERE category != 'Comic' ORDER BY category ASC";
		$stmt = $database->query($query);
		$var = '<select id="category" name="category" style="width:333px;float:right; margin-top:10px;">
		<option value="" onClick="parent.location=\'/update/article_view/\'">All Articles</option>'; 
		while($r = $stmt->fetch_assoc()) {
			$var .= '<option value="' . $r['catURL'] . '" onClick="parent.location=\'/update/article_view/' . $r['catURL'] . '\'">' . $r['category'] . '</option>';
		} 
		$stmt->close();
		$var .= '</select>';
		return $var;
	}
	
	function titleToUrl($var) {
		$var = preg_replace("/[\\040]/i", "-", $var);
		$allowed = "/[^a-z0-9\\-\\_]/i";
		$var = preg_replace($allowed,"",$var);
		$var = strtolower($var);
		return $var;
	}
	
	function insert_dash($var){
		$var = preg_replace('/ /', "-", $var);
		return $var;
	}
	
	function remove_dash($var){
		$var = preg_replace('/[-\\_]/', " ", $var);
		return $var;
	}
	
	function remove_dash_and_uc($var){
		$var = preg_replace('/[-\\_]/', " ", $var);
		$var = ucwords($var);
		return $var;
	}
}
?>