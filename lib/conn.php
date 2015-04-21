<?php 
class conn{
    
    protected $connectionString = NULL;

    function dbConnect(){
        $host = "localhost";
        $user = "root";
        $db = "VS";
        $passwd = "";

        $con = mysqli_connect($host ,$user,$passwd,$db);
            
        /* check connection */
        if ($con->connect_errno) {
            printf("Connect failed: %s\n", $con->connect_error);
            exit();
        }

        //$query = "SELECT VERSION()";
        //$query = "SELECT * from user_name"; 
       // $rs = mysqli_query($con, $query) or die("Cannot execute query:" . mysqli_error($con)); 
        //$row = mysqli_fetch_row($rs);

       // echo $row[0] . "\n";
        //pg_close($con); 
        return $con;

    }
    
    function dbDisconnect($conn){
        mysqli_close($conn);
    }
}

?>

