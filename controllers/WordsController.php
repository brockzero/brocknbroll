<?php
/*
http://us.php.net/manual/en/pdo.prepare.php for when MySQL is updated past 5.1.17
*/
class Words 
{
	private $getPage = "";
	function __construct()
	{
		global $database;
		if(isset($_GET['page'])) 
		{
			$getPage = $_GET['page'];
		}
		if (empty($getPage) || $getPage == 'category')
		{
			$this->title = 'Words';
		} 
		else 
		{
			//Ths Query gets all the data I need for a single comics page.
			//need to harden $_GET[page] with stripslahes or something
			$query = "SELECT title, article, user, DATE_FORMAT(date,'%a, %M %D, %Y - %h:%i %p') AS f_date FROM content WHERE url = ?";
			$stmt = $database->prepare($query);
			$stmt->bind_param("s",$getPage);
		    $stmt->execute();
			$stmt->store_result(); //This is IMPORTANT if you are binding a longtext field
			$stmt->bind_result($this->title, $this->article, $this->user, $this->f_date);
			$stmt->fetch();
			$stmt->free_result();
			$stmt->close();
			if(empty($this->title))
			{
				$this->pageTitle = 'Words';
				unset($_GET['page']);
				unset($getPage);
			}
		}
	}

	private function getArchives()
	{	
		global $database;	
		$var =  '<h2>' . $this->title . '</h2>
		<div id="wordCategorySelect"><select id="category" name="category">
		<option value="all">All</option>';
		$query = "SELECT category, catURL FROM category WHERE category != 'Comic' AND category != 'Comic Ideas' AND category != 'Rough Draft'";
		$stmt = $database->query($query);
		while($r=$stmt->fetch_assoc()) {
			$var .= '<option value="' . $r['catURL'] . '">' . $r['category'] . '</option>';
		}
		$stmt->close();
		$var .= '</select></div>
		<div id="xhr">
		<table class="tableStyle">
		<tr>
		<th>Article</th><th>Category</th><th>Date</th>
		</tr>';
		if (empty($_POST['category']) || $_POST['category'] == 'all' ) 
		{
			$query = "SELECT title, url, article, user, category, DATE_FORMAT(date,'%Y-%m-%d') AS f_date FROM content WHERE category != 'rough_draft' AND category != 'comic_ideas' AND category != 'comic' ORDER BY date DESC";
			$stmt = $database->query($query);
			while($r = $stmt->fetch_assoc()) 
			{
				$var .= '<tr>
				<td><a href="/words/' . $r['url'] . '">' . $r['title'] . '</a></td><td>' . $this->remove_dash_and_uc($r['category']) . '</td><td>' . $r['f_date'] . '</td>
				</tr>';
			}
		} 
		else 
		{
			$query = "SELECT title, url, article, user, category, DATE_FORMAT(date,'%Y-%m-%d') AS f_date FROM content WHERE category = ? ORDER BY date DESC";
			$stmt = $database->prepare($query);
			$stmt->bind_param("s", $_POST['category']);
			$stmt->execute();
			$stmt->bind_result($r[title], $r[url], $r[article], $r[user], $r[category], $r[f_date]);
			while($stmt->fetch()) 
			{
				$var .= '<tr>
				<td><a href="/words/' . $r[url] . '">' . $r[title] . '</a></td><td>' . $this->remove_dash_and_uc($r[category]) . '</td><td>' . $r[f_date] . '</td>
				</tr>';
			}
		}
		$var .= '</table></div>';
		
		// I am not sure why this works
		//because it doesn't seem to work on another page
		//look back into $.post or try with $.ajax
		//the grief this caused me is inexcusable.
		$var .= '<script>
		$("table.tableStyle tr:odd").css("background-color", trColorOdd);
			//http://api.jquery.com/selected-selector/
			/* attach a submit handler to the form */
			$("select").change(function () {
			var term = $("select option:selected").val();
			url = "/words/";
		
				/* Send the data using post and put the results in a div */
				$.post( url, { category: term }, function( data ) {
					var cat = $( data ).find( "#xhr" ).fadeIn(500);
					$( "#xhr" ).html( cat );
					$("tr:odd").css("background-color", trColorOdd);
			  });
		  });
		</script>';
		return $var;
	}

	public function getArticle() 
	{
		if(!empty($_GET['page']))
		{
			//$this->pageCategory = $this->r[category]; //not used atm
			$var = '<div id="wordsContainer">
			<h2>' . $this->title . '</h2>
			' . $this->article . '
			<h6>By: <a href="/contact">' . $this->user . '</a> | On: ' . $this->f_date . '</h6></div>';
			return $var;		
		} 
		else 
		{
			return $this->getArchives();		
		}				
	}

	function remove_dash_and_uc($var)
	{
		$var = preg_replace('/[-\\_]/', " ", $var);
		$var = ucwords($var);
		return $var;
	}
}
?>