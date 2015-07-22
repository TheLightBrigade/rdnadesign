<?php
//This is a file that we can put function definitions in and call them in our scripts.

  

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function parse_array_variables($row)
{
  foreach($row as $key => $value) {
    $$key = $value;
    echo $key . " - " . $value . "<br>";
  }
}

function Get_FET_Rate() {
  include('gbfodata.php');
  
  $query = "SELECT Global_LNG_FET_Rate FROM Defaults WHERE Default_ID = '0'"; 
  $stmt = $conn->prepare($query); 
  $stmt->execute;
  $row = $stmt->fetch();
  
  if(count($result)) {
    $FET_Rate = $row['Global_LNG_FET_Rate'];
  }
  else {
    $FET_Rate = 0.40581;
  }
  
  return $FET_Rate;
}

function format_telephone($phone_number)
{
    $cleaned = preg_replace('/[^[:digit:]]/', '', $phone_number);
    preg_match('/(\d{3})(\d{3})(\d{4})/', $cleaned, $matches);
    return "({$matches[1]}) {$matches[2]}-{$matches[3]}";
    
    //Call the function like this: 
    // echo format_telephone($customer_phone); //
}

function mysql_prep( $value ) {
    $magic_quotes_active = get_magic_quotes_gpc();
    $new_enough_php = function_exists( "mysql_real_escape_string" ); // i.e. PHP >= v4.3.0
    if( $new_enough_php ) { // PHP v4.3.0 or higher
        // undo any magic quote effects so mysql_real_escape_string can do the work
        if( $magic_quotes_active ) { $value = stripslashes( $value ); }
        $value = mysql_real_escape_string( $value );
    } else { // before PHP v4.3.0
        // if magic quotes aren't already on then add slashes manually
        if( !$magic_quotes_active ) { $value = addslashes( $value ); }
        // if magic quotes are active, then the slashes already exist
    }
    return $value;
}

function confirm_query($result_set) {
    if (!$result_set) {
        die("Database query failed: " . mysql_error());
    }
}

function check_required_fields($required_array) {
    $field_errors = array();
    foreach($required_array as $fieldname) {
        if (!isset($_POST[$fieldname]) || (empty($_POST[$fieldname]) && !is_numeric($_POST[$fieldname]))) {
            $field_errors[] = $fieldname;
        }
    }
    return $field_errors;
}

function check_max_field_lengths($field_length_array) {
    $field_errors = array();
    foreach($field_length_array as $fieldname => $maxlength ) {
        if (strlen(trim(mysql_prep($_POST[$fieldname]))) > $maxlength) { $field_errors[] = $fieldname; }
    }
    return $field_errors;
}

function display_errors($error_array) {
    echo "<p class=\"errors\">";
    echo "Please review the following fields:<br />";
    foreach($error_array as $error) {
        echo " - " . $error . "<br />";
    }
    echo "</p>";
}


function Record_Query($Recorded_Query) {
    //This function will accept a query as an argument and toss it in the database table Query_History.

    //Current Status:  I don't fucking know.  Nate will have to help here.

    $User_ID = $_SESSION['Employee_ID'];
    $QH_Timestamp = date('Y-m-d H:i:s');


    $QH_Query = "INSERT INTO Query_History (QH_User_ID, QH_Timestamp, QH_Query) VALUES ('$User_ID', '$QH_Timestamp', '$Recorded_Query')";
    //echo $QH_Query;
    $QH_Result = mysql_query($QH_Query) or die('There was an error: ' . mysql_error());
}

function GBFO_Mailer($Recipients,$Subject,$Body) {
    //This is an attempt to standardize the outgoing auto-mailer stuff.

    //Define the headers, so that html can be processed by the email client
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1;' . "\r\n";
    $headers .= 'From: GBF Online <desr@greenbuffalofuel.com>' . "\r\n";


    //Get the subject and add the date.
    $Subject_Date = date('M jS');
    $Mail_Subject = "GBFO - " . $Subject . " - " . $Subject_Date;

    //Set the greeting to anthropomorphize the auto-mailer
    $Hour = date('G');
    if ( $Hour >= 5 && $Hour <= 11 ) {$Greeting = "Good morning,";}
    else if ( $Hour >= 12 && $Hour <= 18 ) {$Greeting = "Good afternoon,";}
    else if ( $Hour >= 19 || $Hour <= 4 ) {$Greeting = "Good evening,";}

    $Mail_Body = "<html><body>" . $Greeting . " GBF team.  This is an automated message from GBF Online.<br><br>"
        .$Body.
        "<br>
        <br>
        <b>Project A.L.I.C.E.</b>
        <br>Artificial LNG Intelligence, Championing Excellence
        <br>Green Buffalo Fuel, llc
        <br>desr@greenbuffalofuel.com
        <br>716-332-4748
        </body></html>
        ";


    //Actually send the email
    mail($Recipients, $Mail_Subject, $Mail_Body, $headers);
}

function Recalculate_Lease($Lease_Number) {

    //Go find the numbers we're recalculating this for (Total security deposit, maintenance max, etc)

    $Lease_Query = "
        SELECT * FROM Leases
        WHERE Lease_Number = '$Lease_Number'";

    $Lease_Result = mysql_query($Lease_Query) or die('There was an error: ' . mysql_error());
    while($row = mysql_fetch_array($Lease_Result, MYSQL_ASSOC)) {

        //Find out what the per_mile_maintenance cost and periodic_maintenance_fee are
        $Periodic_Maintenance_Fee = $row['Periodic_Maintenance_Fee'];
        $Total_Maintenance_Amount = $row['Total_Maintenance_Amount'];

        $Security_Installment = $row['Security_Installment'];
        $Total_Security_Deposit = $row['Total_Security_Deposit'];
    }


    //How much has been paid already?  (I.E. how much remains to be paid?)
    $Lease_Lines_Query = "
        SELECT
        SUM(Lease_Line_Security_Deposit_Due) AS Security_Deposit_Invoiced,
        SUM(Lease_Line_Maintenance_Due) AS Maintenance_Invoiced
        FROM Lease_Lines
        WHERE Lease_Number = '$Lease_Number' AND Invoice_Number <> 0";

    $Lease_Lines_Result = mysql_query($Lease_Lines_Query) or die('There was an error: ' . mysql_error());
    while($row = mysql_fetch_array($Lease_Lines_Result, MYSQL_ASSOC)) {

        //Find out what the per_mile_maintenance cost and periodic_maintenance_fee are
        $Security_Deposit_Invoiced = $row['Security_Deposit_Invoiced'];
        $Maintenance_Invoiced = $row['Maintenance_Invoiced'];
    }


    $Security_Deposit_Outstanding = $Total_Security_Deposit - $Security_Deposit_Invoiced;
    $Maintenance_Outstanding = $Total_Maintenance_Amount - $Maintenance_Invoiced;


    //Clear out any maintenance or Security deposit amount that hasn't yet been invoiced

    $Clear_Uninvoiced_Maintenance_Query = "
      UPDATE Lease_Lines
      SET Lease_Line_Maintenance_Due = '0'
      WHERE Lease_Number = '$Lease_Number' AND Invoice_Number = '0'";
    $Clear_Uninvoiced_Maintenance_Result = mysql_query($Clear_Uninvoiced_Maintenance_Query) or die("There was an error: " . mysql_error());

    $Clear_Uninvoiced_Security_Deposit_Query = "
      UPDATE Lease_Lines
      SET Lease_Line_Security_Deposit_Due = '0'
      WHERE Lease_Number = '$Lease_Number' AND Invoice_Number = '0'";
    $Clear_Uninvoiced_Security_Deposit_Result = mysql_query($Clear_Uninvoiced_Security_Deposit_Query) or die("There was an error: " . mysql_error());

    //Great, now anything that hasn't been invoiced has been cleaned out.

    //Now let's modify those uninvoiced lines.

    $Uninvoiced_Lease_Lines_Query = "
      SELECT * FROM Lease_Lines
      WHERE Lease_Number = '$Lease_Number' AND Invoice_Number = '0'
      ORDER BY Lease_Line_Start_Date ASC";

    $Uninvoiced_Lease_Lines_Result = mysql_query($Uninvoiced_Lease_Lines_Query) or die("There was an error: " . mysql_error());
        while($row = mysql_fetch_array($Uninvoiced_Lease_Lines_Result, MYSQL_ASSOC))
        {
            //Get this Lease_ID
            $Lease_Line_ID = $row['Lease_Line_ID'];

            //If the maintenance yet to be paid is more than the periodic maintenance fee, just insert the periodic fee
            if($Maintenance_Outstanding >= $Periodic_Maintenance_Fee) {
                $query7 = "UPDATE Lease_Lines SET Lease_Line_Maintenance_Due = '$Periodic_Maintenance_Fee' WHERE Lease_Line_ID = '$Lease_Line_ID' LIMIT 1";
                $result = mysql_query($query7) or die("There was an error: " . mysql_error());

                $Maintenance_Outstanding = $Maintenance_Outstanding - $Periodic_Maintenance_Fee;
            }
            //If the maintenance yet to be paid is less than that, then input the remainder.
            else {

                $query8 = "UPDATE Lease_Lines SET Lease_Line_Maintenance_Due = '$Maintenance_Outstanding' WHERE Lease_Line_ID = '$Lease_Line_ID' LIMIT 1";
                $result = mysql_query($query8) or die("There was an error: " . mysql_error());

                $Maintenance_Outstanding = 0;
            }


            //If the security yet to be paid is more than the periodic maintenance fee, just insert the periodic fee
            if($Security_Deposit_Outstanding >= $Security_Installment) {
                $query9 = "
                    UPDATE Lease_Lines
                    SET Lease_Line_Security_Deposit_Due = '$Security_Installment'
                    WHERE Lease_Line_ID = '$Lease_Line_ID' LIMIT 1";
                $result = mysql_query($query9) or die("There was an error: " . mysql_error());

                $Security_Deposit_Outstanding = $Security_Deposit_Outstanding - $Security_Installment;
            }
            //If the security yet to be paid is less than that, then input the remainder.
            else {

                $query10 = "
                    UPDATE Lease_Lines
                    SET Lease_Line_Security_Deposit_Due = '$Security_Deposit_Outstanding'
                    WHERE Lease_Line_ID = '$Lease_Line_ID' LIMIT 1";
                $result = mysql_query($query10) or die("There was an error: " . mysql_error());

                $Security_Deposit_Outstanding = 0;
            }
        }
    }









?>