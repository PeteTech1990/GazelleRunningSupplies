<?php
namespace gazelleRunningSupplies;
use mysqli;

session_start();

class dbConnect
        {       

            public object $sqlConnection;

            public int $userID;

            var $failLoginMessage;

            function __construct()
            {
                $sqlServer = "localhost";
                $database = "gazellerunningsupplies";

                $this->sqlConnection = new mysqli($sqlServer, "sa", "sa", $database);
                
            }

            function authenticateUser()
            {
                $username = $_POST["txtUsername"];
                $sqlComm = "SELECT * FROM tblUser WHERE username='$username'";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                if($sqlReturn->num_rows > 0)
                {
                    while($row = $sqlReturn->fetch_assoc())
                    {
                        if($row["password"] == $_POST["txtPassword"])
                        {
                            $this->userID = $row["userID"];
                            return true;
                        }
                        else
                        {
                            $this->failLoginMessage = "Password incorrect, please try again";
                        }             
                    }
                }
                else
                {
                    $this->failLoginMessage = "Username unknown, please try again";
                }   
            }

        }

        $dbConnect = new dbConnect();

        if($dbConnect->authenticateUser()){
            $_SESSION["userID"] = $dbConnect->userID;
            header("Location: adminOrders.php?order=1");
        }    
        else
        {
            $_SESSION["failLoginMessage"] = $dbConnect->failLoginMessage;
            header("Location: adminLogin.php");
        }            
                                    
           
        
?>