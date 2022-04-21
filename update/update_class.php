<?php
class Update 
{	
	
	// menu for admin area only
	public $editing_menu = '<p>[<a href="/comic">Site Home</a>] [<a href="/main">Acount Management</a>] [<a href="/update">Update Home</a>] - [<a href="/update/article_update">New Article</a>] [<a href="/update/article_view">View/Edit Articles</a>] - [<a href="/update/comic_update">New Comic</a>] [<a href="/update/comic_view">View/Edit Comics</a>]
	</p>';
	
	function __construct() {
		//session_start(); 
	}
	/*
	* Default paged displayed when entering the admin area.
	*/
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
	
	/*
	* Lists all articles
	*/
	function articleView(){
		global $database;
		$catID;
		if (!empty($_POST['id'])) {
			$_SESSION['page_id'] = $_POST['id'];
		}
		if(isset($_SESSION['page_id']) && $_SESSION['page_id'] != "all" ) {
			$postID = $database->real_escape_string($_SESSION['page_id']); //escape data taken from the url
			$catID = $this->remove_dash_and_uc($_SESSION['page_id']);
			//query for specific categories
			$query = "SELECT title, article, user, category, id, url, DATE_FORMAT(date,'%Y-%m-%d') AS f_date FROM content WHERE category = '$postID' ORDER BY date DESC LIMIT 500"; 
		} else {
			$query = "SELECT title, article, user, category, id, url, DATE_FORMAT(date,'%Y-%m-%d') AS f_date FROM content WHERE category != 'comic' ORDER BY date DESC LIMIT 500";
		}
		$_SESSION['page_id'] = NULL; 
		
		
		$categoryMenu = $this->categoryMenu(); //local function to generate the categories menu
		//change to a proper function
		$var = '<form id="articleView" action="/update/batch_delete/">
		' . $categoryMenu . '
		<div id="xhr">
		<h3>' . $catID . ' Articles</h3>
		<table class="tableStyle">
		<tr>
		<th>Article</th><th>Category</th><th>Posted By</th><th>Date</th><th>View</th><th>Delete</th>
		</tr>';
		
		$stmt = $database->query($query);
		while($r = $stmt->fetch_assoc()) {
			$var .= '<tr>
			<td><a href="/update/article_editor/' . $r['id']  . '">' . $r['title'] . '</a></td>
			<td>' . $r['category'] . '</td>
			<td>' . $r['user'] . '</td>
			<td>' . $r['f_date'] . '</td>
			<td><a href="/words/' . $r['url'] . '">View</a></td>
			<td>Delete: <input type="checkbox" name="delete[' . $r['id'] . ']" value="' . $r['id'] . '"></td></tr>';
		}
		$var .= '</table></div><input type="submit" value="Submit"></form>
		
		<script>
		$("tr:odd").css("background-color", "#fff2ba");
		$("tr:even").css("background-color", "#ffffff");
		
		/* attach a submit handler to the form */
		$("#articleView").submit(function(event) {
			/* stop form from submitting normally */
			event.preventDefault(); 
					
			/* get some values from elements on the page: */
			var $form = $( this ),
			url = $form.attr( "action" );
		
				/* Send the data using post and put the results in a div */
				$.post( url, $("#articleView").serialize(),function( data ) {
					var cat = $( data ).find( "#xhr" ).fadeIn(999);
					$( "#xhr" ).empty().append( cat );
					$("tr:odd").css("background-color", "#fff2ba");
					$("tr:even").css("background-color", "#ffffff");
			  });
		  });
		</script>';
		return $var;
	}
	
	function batchDelete() {
		global $database;
		//make two step, ask first then delete
		if(!empty($_POST['delete'])){
			foreach ($_POST['delete'] as $key => $value) {
				$query = "DELETE FROM content WHERE id = $value LIMIT 1";
				$database->query($query);
			}
			$var = $this->articleView();
			return $var;
		}
		return $this->articleView();
	}
	/*
	Displays the form from getArticleEditForm and depending on the value of SERVER REQUEST METHOD 
	
	
	*/	
	function articleUpdate(){
		global $database, $form;
		//Need error checking for forms so required fields aren't posted blank
		$var;
		if ($_SERVER['REQUEST_METHOD'] != 'POST'){
			$var .= '<h2>New Article</h2>';
			$var .= $this->getArticleEditForm("/update/article_update");
		} else {
			foreach ($_POST as $key => $value){
				$form->setValue($key, $value);
				if (empty($value)) {
					$form->setError($key, 'Empty field');	
				}
			}
			if (empty($_POST['title']) || empty($_POST['article']) || empty($_POST['user'])) {
				$var .= '<h2>New Article</h2>';
				$var .= $this->getArticleEditForm("/update/article_update");
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
	/*
	Creates the form for article edits and updates. The form action is passed
	into the functions to allow variable form actions and function reuse. If existing form
	data is stored in the Form class it will be populated into the form.
	*/
	function getArticleEditForm($action) {//used for updates and edits
		global $form;
		///update/article_edit
		$var = '<div id="xhr"><form id="updateForm" method="post" action="' . $action . '">
		<h2>Article Edit</h2>
		<p>Title<br>
		<input type="text" size="64" name="title" value="' . $form->getValue('title') . '"> ' . $form->getError('title') . '
		<input type="hidden" name="user" value="' . $_SESSION['username'] . '">
		<input type="hidden" name="articleID" value="' . $_GET['id'] . '"></p>
		<p>Article</p> 
		<textarea id="article" name="article">' . $form->getValue('article') . '</textarea> ' . $form->getError('article');
		$var .= $this->getArticleCategory($form->getValue('category'));
		$var .= '<br>
		<p><input type="submit" value="Submit"> | <input type="button" value="Toggle Editor" onClick="toggleEditor(\'article\')"> | <input type="button" value="Achievement Code" onClick="addAchievement()"></p>
		</form></div>';
		return $var;		
	}
	/*
	ArticleEditForm calls the getArticleEditForm and and handles the response from submitting the form.
	Additionally form fields are populated via the Form class in case of an the form is not completely reset.
	*/
	function articleEditForm(){
		global $database, $form;
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
		$var .= $this->getArticleEditForm("/update/article_edit");
		$var .= '<script>
			/* attach a submit handler to the form */
			$("#updateForm").submit(function(event) {
				tinyMCE.triggerSave();
				/* stop form from submitting normally */
				event.preventDefault(); 
				/* get some values from elements on the page: */
				var $form = $( this ),
				titleD = $form.find( "input[name="title"]" ).val(),
				userD = $form.find( "input[name="user"]" ).val(),
				articleIDD = $form.find( "input[name="articleID"]" ).val(),
				articleD = $form.find( "textarea[name="article"]" ).val(),
				categoryD = $form.find( "select[name="category"]" ).val(),
				url = $form.attr( "action" );
				/* Send the data using post and put the results in a div */
				$.post( url, { title: titleD, user: userD, articleID: articleIDD, article: articleD, category: categoryD }, function( data ) {
					var cat = $( data ).find( ""#xhr" ).fadeIn(999);
					$( "#xhr" ).empty().append( cat );
				  }
				);
		  });
		</script>';
		return $var;
	}
	
	/*
	Article Edit gates $_POST data from the articleEditForm() and inserts it into the database. This function is called
	by jQuery and the return $var data is displayed in the #xhr container.
	*/
	function articleEdit() {
		global $database, $form;
			$url = $this->titleToUrl($_POST['title']);					
			$query = "UPDATE content SET title = ?, url = ?,  article = ?, category = ? WHERE id = ?";
			$stmt = $database->prepare($query);
			$stmt->bind_param("ssssi", $_POST['title'], $url, $_POST['article'], $_POST['category'], $_POST['articleID']);
			$stmt->execute();
			$affected_rows = $stmt->affected_rows;
			$stmt->close();
			$_SESSION['page_id'] = $_POST['category'];
			$var = '<div id="xhr">
			<h2>Article Edited</h2>
			<a href="update/article_view/">Back to ' . $_POST['category'] . ' view.</a>
			<p>' . $_POST['title'] . '</p>
			<p>' . $_POST['category'] . '</p>
			' . $_POST['article'] . '
			<p> URL ' . $url . '</p></div>';
			return $var;
	}
	/*
	Creates the category menu for the Words admin area. When an item is selected
	only the items for that category are displayed. Also uses a bit of jQuery
	to format the table and display the new results in the #xhr container.
	*/
	function categoryMenu(){
		//used for displaying subcategories on the article view page
		global $database;
		$query = "SELECT category, catURL, description FROM category WHERE category != 'Comic' ORDER BY category ASC";
		$stmt = $database->query($query);
		$var = '<select id="category" name="category" style="display:inline;width:333px;float:right; margin-top:10px;">';
		$var .= '<option value="all">All Articles</option>'; 
		while($r = $stmt->fetch_assoc()) {
			$var .= '<option value="' . $r['catURL'] . '">' . $r['category'] . '</option>';
		} 
		$stmt->close();
		$var .= '</select>
		<script>
			//http://api.jquery.com/selected-selector/
			/* attach a submit handler to the form */
			$("select").change(function () {
			var term = $("select option:selected").val();
			url = "/update/article_view/";
				/* Send the data using post and put the results in a div */
				$.post( url, { id: term }, function( data ) {
					var cat = $( data ).find( \'#xhr\' ).fadeIn(500);
					$( "#xhr" ).html( cat );
					$("tr:odd").css("background-color", "#fff2ba");
					$("tr:even").css("background-color", "#ffffff");
			  }
			);
		  });
		</script>';
		return $var;
	}
	/*
	Create a new comic. Add commentary and other comic information
	for the current user.
	*
	On load the comic update form is displayed. When it's submitted the data is
	inserted into the MySQL database. This is determined by the value of the
	SERVER REQUEST METHOD.
	*/
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
	/*
	* Simplly displays a lists of all comics and creates
	* links to edit a selected comic
	*
	*/
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
	/*
	* Allow you to edit existing comic enttries
	* primarily the commentary, titletxt, title and keywords.
	*/
	function comicEdit(){
		global $database;
		if (!empty($_GET[id]) && $_GET['page'] == 'comic_edit') {
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
	/*
	* Creates the select/option list for article categories
	* used in article creation and article editing
	*/
	function getArticleCategory($default) {
		//creates the category options menu.
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
	
	/* 
	* Converts the value to a url friendly value
	*/
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