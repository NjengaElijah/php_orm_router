<?php

include_once "DBConn.php";

class SQLQuery extends DBConn
{
    public static $results;
    public static $error;
    public static $errorMessage;
    public static $query;
    public static function nonQuery($sql)
    {
        self::$query = $sql;
		$exec = mysqli_query(self::getConnection(),$sql);
        if($exec) {
            return true;
        } 
		else{
			throw new Exception("<br>An error occured running the query. <br><hr>".$sql."<hr>".mysqli_error(self::getConnection()));
		}
        self::closeConnection();
        return false;
    }
	public static function sanitize($input)
	{
		return mysqli_real_escape_string(self::getConnection(),$input);
	}	
    /*
     * @return Array Of Results
     *  */
    public static function query($sql)
    {
        self::$query = $sql;
        try {
            $result = self::getConnection()->query($sql);
			
            $arr = array();
			if($result){
				if (@mysqli_num_rows($result) > 0) {
					while ($row = mysqli_fetch_assoc($result)) {
						$arr[]  =  $row;
					}
				}
			}
			//$arr = mysqli_fetch_array($result);
			//print_r($arr[0]);
            return $arr;
            self::closeConnection();
        } catch (Exception $e) {
            self::$error = $e;
            self::$errorMessage = $e->getMessage();
            throw $e;
        }
    }
    public static function insert($tableName, $assocDataArr)
    {
        try {

            $conn = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $cols = "";
            $params = "";
            // print_r($assocDataArr);
            foreach ($assocDataArr as $key => $value) {
                if ($key != 'id' && $key != 'table' && $key != 'foreign_key_name') {
                    if ($cols != "") {
                        $cols .= ',';
                    }
                    if ($params != "") {
                        $params .= ',';
                    }
                    $cols .= "`$key`";
                    $params .= ":$key";
                }
            }
            $stmt = $conn->prepare("INSERT INTO `$tableName` ($cols) VALUES ($params)");

            foreach ($assocDataArr as $key => $value) {
                if ($key != 'id' && $key != 'table' && $key != 'foreign_key_name') {
                    // echo ':' . $key . ' -> ' . $value;
                    $stmt->bindParam(':' . $key, $assocDataArr[$key]);
                }
            }
            $stmt->execute();
            $id = $conn->lastInsertId();
            $conn = null;
            return $id;
        } catch (Exception $e) {
            self::$error = $e;
            self::$errorMessage = $e->errorInfo[2];
        }
    }
	public static function PaginatedQuery($query,$path,$results_key = "results",$chunk = 20)
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$page = $page < 1 ? 1 : $page;
		
		$total_results = (SQLQuery::query($query));
		
		$total_results = count($total_results) ? intval(count($total_results)) : 0; 	
		
		$last_page = ceil($total_results / $chunk);
		
		$from = 1;
		$to = $chunk; 
		$prev_url = null;
		$next_url = null;
		$next_page = null;
		$prev_page = null;
		if($last_page > 1)
		{
			//we have more than one page 
			$next_page = ($page < $last_page) ? $page + 1 :  null;
			
			if($page == $last_page)
			{
				$next_page = null;
			}
			
			$prev_page = ($page > 1) ? $page - 1 : null;
			
		}
		//unset the last / fromt the url if it exists 
		$path = substr($path, -1) == '/' ? substr($path, 0, -1) : $path;
		if($next_page != null)
		{
			$next_page = $path."/?page=".$next_page;
		}
		if($prev_page != null)
		{
			$prev_page = $path."/?page=".$prev_page;
		}
		
		$offset = $page == 1 ? 0 : $chunk * ($page - 1);
		
		//the articles are less than the chunk size
		if($total_results < $chunk)
		{
			$from = 1;
			$to = $total_results;
		} 
		else 
		{
			//the articles are greater than the chunk size
			if($page == 1)
			{
				//if we are in page one
				$from = 1;
				$to = 20;
			}
			else if($page == $last_page)
			{
				//we are in the last page
				$from = ($chunk * ($page - 1)) + 1;
				$to = $total_results;
			}
			else
			{
				$from = ($chunk * ($page - 1)) + 1;
				$to = ($chunk * $page);
			}
		}	
		
		return [
		$results_key => SQLQuery::query($query." LIMIT ".$chunk." OFFSET ".$offset) , 
		'filters' => [
				'current_page' => $page,
				'total' => $total_results,
				'per_page' => $chunk,
				'from' => $from,
				'to' => $to,
				'last_page' => $last_page,
				'prev_page_url' => $prev_page,
				'path' => Router::GetPath(),
				'next_page_url' => $next_page
		]];
		
	}
}
