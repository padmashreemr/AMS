<?php 
  require("jsrs.php");

  jsrsDispatch("receive"); //"receive" here is name of the php function 
                           // below that receives the query string from the page
                           

  /**Receives the data (sent to the hidden iframe), and formats
   * it for processing
   *
   * @param  string  $str  A query (url) string with key => value pairs 
   *                       Examples: "key=value&key2=value2"
   *                                 "guy=ralph&girl=susan"
   *
   * @return  int  1 on success (To let the parent page know it's done)  
   *
   * This function receives a query string, parses it into key => value pairs
   * and puts it into the $in array.  The $in array is then sent to the function
   * 'process', the function that finally uses the data (eg. goes to the database
   * and run a query).  The function returns a php array of key=>value pairs ($out),
   * which will be printed out to the hidden iframe in javascript format, making
   * the data retrievable by the parent page.
   *
   * Example of $in array    $res["guy"]  = "john"
   *                         $res["girl"] = "jane"
   *                      
   */

	function receive($str){	

		$in = _urlDecode($str);
		$out = process($in); 
		
		print_javascript($out);
		
		return 1; //success
	}


  function process($in) {

    /** MODIFY THIS FUNCTION TO PROCESS INCOMING DATA **/
    $out = array();
    
    $firstName = $in['firstname'];
    $password = $in['password'];
 
 // Create connection
	$con=mysqli_connect("Apples-iMac.local","padma","welcome123","test");
	
	// Check connection
	if (mysqli_connect_errno($con))
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		echo "<br>";
	}
	else
	{
		echo "Connected to test database successfully\n";
		echo "<br>";
	}
	
	echo "<br>";
	echo "Validating Username and Password";
	echo "<br>";
		
	$sql= "SELECT * FROM UsersTable WHERE UPPER(Name) LIKE UPPER('$firstName')";
	$result = mysqli_query($con,$sql);
	/* check whethere there were matching records in the table
	by counting the number of results returned */
	if(mysqli_num_rows($result) >= 1)
	{
		$out['firstname'] = $firstName;
		$count = 1;
		while($row = mysqli_fetch_array($result))
		{
			if($row['Password'] == $in['password'])
			{
				$out['password'.$count] = 'VALID';
				break;
			}
			$count = $count + 1;
		}
		
		if( $count > mysqli_num_rows($result) )
		{
			$out['password'] = 'NOT VALID';
		}
	}
	else
	{
		$out['firstname'] = 'DOES NOT EXIST';
	}
	
	//Close connection
	mysqli_close($con);
   
    return $out; 
  }
 
  /**Take a php array (with key=>value pairs) and print
   * out the keys in a javascript array called "keys"
   * and the values in a javascript array called "values"
   * to the iframe page.  These arrays will then be
   * used by the parent page.
   *
   * @param  array   $out    PHP array to print to out in javascript format
   */
  function print_javascript($out) {
    echo "<script>";
    echo "document.keys = new Array(".count($out).");";
    echo "document.values = new Array(".count($out).");";
    $i=0;
    foreach($out as $key => $value) {
      echo "document.keys[".$i."] = '".$key."';"; 
      echo "document.values[".$i++."] = '".$value."';";
    }
    echo "</script>"; 
  }
  
  
?>
