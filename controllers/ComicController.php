<?php
require_once('../autoloader.php');
class ComicController
{
    public function __construct()
    {
    }

    private function GetMaxPage($database)
    {
        $maxPage = 1;
        $query = "SELECT cmc_id FROM comics";
        if ($stmt = $database->conn->query($query)) {
            $maxPage = $stmt->rowCount();
        }
        return $maxPage;
    }

    private function GetPage($maxPage)
    {
        $pageNum = $maxPage;
        if (isset($_GET['page'])) {
            $getPage = $_GET['page'];
            if (ctype_digit($getPage)) {
                if ($getPage > 0) {
                    if ($getPage >= $maxPage) {
                        $pageNum = $maxPage;
                    } else {
                        $pageNum = $getPage;
                    }
                }
            }
        }
        return $pageNum;
    }

    public function GetComic()
    {
        $database = new DatabaseController();
        $comicModel = new ComicModel();
        $maxPage = $this->GetMaxPage($database);
        $comicId = $this->GetPage($maxPage);

        $comicModel->pagingLast = $maxPage;
        
        $query = "SELECT cmc_id, cmc_comic, cmc_date, cmc_title, cmc_post, cmc_titletxt, cmc_user FROM comics WHERE cmc_id =:cmc_id";
        $stmt = $database->conn->prepare($query);
        if ($stmt) {
            $stmt->setFetchMode(PDO::FETCH_INTO, $comicModel);
            $stmt->execute(['cmc_id' => $comicId]);
            $result = $stmt->fetch();
        }

        $list = $comicModel->JsonSerialize();
        return json_encode($list);
    }

    public function ComicArchives()
    {
        $database = new DatabaseController();
        $comicModel = new ComicModel();
        $comicsArchive = array();

        $query = "SELECT cmc_title, cmc_id, cmc_keywords, cmc_date FROM comics ORDER BY cmc_id DESC";
        $stmt = $database->conn->query($query);
        if ($stmt) {
            $stmt->setFetchMode(PDO::FETCH_INTO, $comicModel);
            while ($result = $stmt->fetch()) {
                $comicsArchive[] = $result->JsonSerialize();
            }
        }
        return json_encode($comicsArchive);
    }

    public function ComicSearch($keywords)
    {
        $database = new DatabaseController();
        $comicModel = new ComicModel();
        $comicsArchive = array();

        $query = "SELECT cmc_title, cmc_id, cmc_keywords, cmc_date FROM comics WHERE cmc_keywords LIKE :keywords";
        $stmt = $database->conn->prepare($query);
        if ($stmt) {
            $stmt->setFetchMode(PDO::FETCH_INTO, $comicModel);
            $stmt->execute(['keywords' => $keywords]);
            while ($result = $stmt->fetch()) {
                $comicsArchive[] = $result->JsonSerialize();
            }
        }

        return json_encode($comicsArchive);
    }
}
