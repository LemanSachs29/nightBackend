<?php
/**
 * Connection to the database using a PDO object. 
 * @author Juan <juanblancomoyano@gmail.com>
 */
class database
{
    /**
     * Database url
     * @var string
     */
    private static $host = 'localhost';
    /**
     * Database name
     * @var string
     */
    private static $dbname = 'nightoutsevilla';
    /**
     * Database username
     * @var string
     */
    private static $username = 'root';
    /**
     * Database password
     * @var string
     */
    private static $password = 'admin';


    /**
     * Method that connects with the database using values from fields
     * @return PDO instance
     */
    public static function getConnection(): ?PDO
    {
        $pdo = null;
        try {
            //String connection
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8mb4";

            //Creating an isntance of pdo
            $pdo = new PDO($dsn, self::$username, self::$password);

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOexception $e) {
            echo "Conection error: " . $e->getMessage();
        } finally {
            return $pdo;
        }
    }
}

?>