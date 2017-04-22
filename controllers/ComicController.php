<?php
require_once('../include/constants.php');
require_once('../include/autoloader.php');
class ComicController
{
	private $database;

	function __construct($database) //new Database(DB_SERVER, DB_USER, DB_PASS, DB_NAME)
    {
		$this->database = $database;
	}

	public function Archive() {
        try {
            $query = 'SELECT id, fileName, createdDate, title, description, titleAttr, altAttr, user FROM Comic';
            $stmt = $this->database->query($query);
            //http://stackoverflow.com/questions/15404232/php-array-json-encode-adding-extra-quotes

            $list = array();

            while($r = $stmt->fetch_assoc()) {
                $Comic = new Comic();
                $Comic->id = $r['id'];
                $Comic->fileName = $r['fileName'];
                $Comic->createdDate = $r['createdDate'];
                $Comic->title = $r['title'];
                $Comic->description = $r['description'];
                $Comic->titleAttr = $r['titleAttr'];
                $Comic->altAttr = $r['altAttr'];
                $Comic->user = $r['user'];
                //http://stackoverflow.com/questions/6739871/php-create-array-for-json
                //try this maybe I'm doing too many shortcuts, maybe I need an array of arrays
                $list[]  = $Comic->jsonSerialize();
            }

            //$stmt->close();
            $this->database->closeDB();
            return json_encode($list);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
	}

	public function Display() {
        try {
            $Comic = new Comic();
            $query = 'SELECT Id FROM Comic';
            $stmt = $this->database->query($query);
            $Comic->pagingLast = $stmt->num_rows;
            $Comic->pagingFirst = 1;
            $stmt->close();
            $page = 1;
            if (isset($_GET['page']))
            {
                if (ctype_digit($_GET['page'])) //test $_GET[page] for digits only
                {
                    $page = intval($_GET['page']);
                    if ($page >= $Comic->pagingLast || $page == 0)
                    {
                        $page = $Comic->pagingLast;
                    }
                }
            }
            else
            {
                $page = $Comic->pagingLast;
            }
            $Comic = $this->GetById($Comic, $page);
            return json_encode($Comic->jsonSerialize());
            //http://stackoverflow.com/questions/4697656/using-json-encode-on-objects-in-php-regardless-of-scope
            //http://jondavidjohn.com/show-non-public-members-with-json_encode/
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }	
	}
	
	public function GetById($Comic, $id) {
        try {
            //mysqli cannot have variables in the query directly use prepare()/bind_param()
            $query = "SELECT Id, FileName, DATE_FORMAT(CreatedDate,'%m-%d-%Y')
                AS CreatedDate, Title, Description, TitleAttr, AltAttr, User
                FROM comic
                WHERE Id = ?";
            $stmt = $this->database->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($Comic->id,
                $Comic->fileName,
                $Comic->createdDate,
                $Comic->title,
                $Comic->description,
                $Comic->titleAttr,
                $Comic->altAttr,
                $Comic->user);
            $stmt->fetch(); //actually gets database values into the variables above.
            $stmt->close();
            $this->database->closeDB();
            return $Comic;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            //error_log("Caught $e", 3, "E:\Root\xampp\htdocs\brocknbroll\errorlog.txt");
        }
	}

	public function Create()
	{
		$query = "INSERT INTO comic (AltAttr, Category, CreatedDate, Description, FileName, Id, Keywords, Title, TitleAttr, User)
		VALUES (?,?,NOW(),?,?,NULL,?,?,?,?)";
		$stmt = $this->database->prepare($query);
		$stmt->bind_param("ssssssss",
			$_POST['altAttr'],
			$_POST['category'],
			$_POST['createdDate'],
			$_POST['description'],
			$_POST['fileName'],
			$_POST['keywords'],
			$_POST['title'],
			$_POST['titleAttr'],
			$_POST['user']);
		$stmt->execute();

		//http://php.net/manual/en/mysqli.insert-id.php
		$stmt->close();
		$stmt->insert_id; //use to get just inserted id
		//http://stackoverflow.com/questions/359047/php-detecting-request-type-get-post-put-or-delete
		$this->database->closeDB();
	}

	public function Update()
	{
        $query = "UPDATE comic SET AltAttr = ?, Category = ?, Description = ?, FileName = ?, Keywords = ?, Title = ?, TitleAttr = ?, User = ? WHERE Id = ?";
		//VALUES (?,?,NOW(),?,?,NULL,?,?,?,?)";
		$stmt = $this->database->prepare($query);
		$stmt->bind_param("ssssssssi",
			$_POST['altAttr'],
			$_POST['category'],
			$_POST['description'],
			$_POST['fileName'],
			$_POST['keywords'],
			$_POST['title'],
			$_POST['titleAttr'],
			$_POST['user'],
            $_POST['id']);
		$stmt->execute();
		$this->database->closeDB();
        $Comic = new Comic();
        $Comic = $this->GetById($Comic, $_POST['id']);
        return json_encode($Comic->jsonSerialize());
	}

	public function Delete()
	{
		$this->database->closeDB();
	}
}
?>
