<?php



class DBConn
{
    private static $connection;
    public static function getConnection()
    {
		if(isset(self::$connection)) {
			self::$connection = null;
		}
		
        if (!isset(self::$connection)) {
            self::$connection = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
			if(!self::$connection)
			{
				printError2("Could not establish database connection ",mysqli_connect_error(self::$connection));
				die("");
			}
			return self::$connection;
	    }
    }
    public static function closeConnection()
    {
        mysqli_close(self::$connection);
        unset($connection);
    }
}