<!--
  Created by Ali Sadek, Ziad Khalifa, Cheuk-lun Cheung
-->

<?php
// The preceding tag tells the web server to parse the following text as PHP
// rather than HTML (the default)

// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set some parameters

// Database access configuration
$config["dbuser"] = "ora_aliwagih";			// change "cwl" to your own CWL
$config["dbpassword"] = "a21605143";	// change to 'a' + your student number
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;	// login credentials are used in connectToDB()

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

// The next tag tells the web server to stop parsing the text as PHP. Use the
// pair of tags wherever the content switches to PHP
?>

<html>

<head>
	<title>Property Management System - Ali, Ziad, and Cheuk-Lun</title>
</head>

<body>
    <h1>Property Management System</h1>

    <h2>Reset All Tables</h2>
    <p>Clear and re-populate all tables with default data</p>

    <form method="POST" action="PropertyMgmtUI.php">
        <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
        <p><input type="submit" value="Reset" name="resetSubmit"></p>
    </form>

    <hr />

    <h2>Insert Property</h2>
    <form method="POST" action="PropertyMgmtUI.php">
        <input type="hidden" id="insertQuery" name="insertQueryRequest">
        Property Size (sqft): <input type="text" name="insertPropertySize"> <br /><br />
        Current Value ($): <input type="text" name="insertCurrValue"> <br /><br />
        Purchase Price ($): <input type="text" name="insertPurchasePrice"> <br /><br />
        Property Address: <input type="text" name="insertPropertyAddress"> <br /><br />
        Assigned Property Manager ID: <input type="text" name="insertPropertyMgrID"> <br /><br />
        Owner ID: <input type="text" name="insertOwner"> <br /><br />

        <input type="submit" value="Insert" name="insertSubmit"></p>
    </form>
    <hr />

    <h2>Delete Property</h2>
    <form method="POST" action= PropertyMgmtUI.php>
        <input type="hidden" id="deleteQuery" name="deleteQueryRequest">
        Property ID: <input type="text" name="deletePropertyID"> <br /><br />

        <input type="submit" value="Delete" name="deleteSubmit">
    </form>

    <hr />

    <h2>Select Maintenance Requests Based On Priority and Status</h2>
    <form method="POST" action="PropertyMgmtUI.php">
        <input type="hidden" id="selectQueryRequest" name="selectQueryRequest">

        <!-- Condition 1 (required) -->
        <p>
            <strong>Condition 1 (Required):</strong>
            <select name="attribute1">
                <option value="requestPriority">Request Priority</option>
                <option value="requestStatus">Request Status</option>
            </select>
            =
            <input type="text" name="value1" placeholder="For Request Priority use 1-3. For Request Status use Open, Closed, or In Progress"
                   style="width:500px;" required>
        </p>

        <!-- Optional Condition 2 -->
        <p>
            <strong>Condition 2 (Optional):</strong>
            <select name="attribute2">
                <option value="requestPriority">Request Priority</option>
                <option value="requestStatus">Request Status</option>
            </select>
            =
            <input type="text" name="value2" placeholder="Leave blank if not applicable"style="width:200px;">
        </p>

        <!-- Logical operator: used only if Condition 2 is provided -->
        <p>
            <select name="logicalOp">
                <option value="AND">AND</option>
                <option value="OR">OR</option>
            </select>
            (Only used if Condition 2 is filled in)
        </p>

        <input type="submit" value="Select" name="selectSubmit">
    </form>
    <hr />


    <h2>Update Individual Owner</h2>
    <p>The values are case-sensitive and if you enter in the wrong case, the update statement will not do anything.</p>
    <form method="POST" action="PropertyMgmtUI.php">
        <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
        Owner ID (to identify which Individual Owner to update): <input type="text" name="updateOwnerID"> <br /><br />
        Social Insurance Number (### ### ###): <input type="text" name="updateSIN"> <br /><br />
        Credit Score: <input type="text" name="updateCreditScore"> <br /><br />


        <input type="submit" value="Update" name="updateSubmit"></p>
    </form>

    <hr />

    <h2>Display Property Manager Information (Display Tuples in PropertyManager)</h2>
    <form method="POST" action= PropertyMgmtUI.php>
        <input type="hidden" id="displayQueryRequest" name="displayQueryRequest">
        Attributes (e.g., propertyMgrID, propertyMgrName, phoneNumber, email):
        <input type="text" name="attributes" placeholder="Leave blank for all">
        <br /><br />
        <input type="submit" value="Display" name="displaySubmit">
    </form>
    <hr />

    <h2>Find Property Information for a Specific Amenity (Join Query)</h2>
    <form method="POST" action= PropertyMgmtUI.php>
        <input type="hidden" id="joinQueryRequest" name="joinQueryRequest">
        Amenity Type: <input type="text" name="amenityTypeJoin" placeholder="Enter Pool, Gym, Parking, Garden, or Elevator" required> <br /><br />
        <input type="submit" value="Join" name="joinSubmit">
    </form>
    <hr />

    <strong>The number of properties managed by each property manager(Aggregation with GROUP BY)</strong>
    <form method="GET" action="PropertyMgmtUI.php">
        <input type="hidden" id="query7Request" name="query7Request">
        <input type="submit" value="Execute Query 7">
    </form>
    <hr />

        <strong>List property owners with >1 property (Aggregation with HAVING)</strong>
    <form method="GET" action="PropertyMgmtUI.php">
        <input type="hidden" id="query8Request" name="query8Request">
        <input type="submit" value="Execute Query 8">
    </form>
    <hr />

    <strong>For each property manager, show the average current value of properties they manage, but only for those managers
        whose average value is at or above the overall average property value. (Nested Aggregation with GROUP BY)</strong>
    <form method="GET" action="PropertyMgmtUI.php">
        <input type="hidden" id="query9Request" name="query9Request">
        <input type="submit" value="Execute Query 9">
    </form>
    <hr />

    <strong>Identify all property managers who manage both residential and commercial properties. (Division Query)</strong>
    <form method="GET" action="PropertyMgmtUI.php">
        <input type="hidden" id="query10Request" name="query10Request">
        <input type="submit" value="Execute Query 10">
    </form>
    <hr />

	<?php
	// The following code will be parsed as PHP

    // Handlers

    // NON-HARDCODED QUERIES: Q1 to Q6

    // Query 0: Reset (Not Required)
    function handleResetRequest()
    {
        global $db_conn;
        // Run the initialization script to reset all tables.
        runSQLFile("initializeDB.sql");
        echo "Database reset complete.";
    }

    // Query 1: Insert
    function handleInsertRequest()
    {
        global $db_conn;

        // Build a tuple from POST values.
        // Note: We no longer require a Property ID from the user because it’s auto-generated.
        $tuple = array(
            ":propSize"   => $_POST['insertPropertySize'],
            ":currValue"  => $_POST['insertCurrValue'],
            ":purPrice"   => $_POST['insertPurchasePrice'],
            ":propAddress"=> $_POST['insertPropertyAddress'],
            ":propMgrID"  => $_POST['insertPropertyMgrID'],
            ":ownerID"    => $_POST['insertOwner']
        );

        // Check that none of the fields are empty.
        if (empty($tuple[":propSize"]) ||
            empty($tuple[":currValue"]) ||
            empty($tuple[":purPrice"]) ||
            empty($tuple[":propAddress"]) ||
            empty($tuple[":propMgrID"]) ||
            empty($tuple[":ownerID"])) {
            echo "Please enter all fields to input a valid property";
            return;
        }

        // Check numeric fields.
        if (!is_numeric($tuple[":propSize"]) ||
            !is_numeric($tuple[":currValue"]) ||
            !is_numeric($tuple[":purPrice"]) ||
            !is_numeric($tuple[":propMgrID"]) ||
            !is_numeric($tuple[":ownerID"])) {
            echo "Error: Property size, property values/prices, property manager ID, and owner ID must be numeric.";
            return;
        }

        // If the assigned property manager does not exist, insert default record.
        if (!doesForeignKeyExist('PropertyManager', 'propertyMgrID', $tuple[":propMgrID"])) {
            $defaultManager = array(":id" => $tuple[":propMgrID"]);
            executeBoundSQL(
                "INSERT INTO PropertyManager (propertyMgrID, propertyMgrName, phoneNumber, email)
             VALUES (:id, 'Default Manager', '000-0000', 'default@domain.com')",
                array($defaultManager)
            );
        }

        // If the specified owner does not exist, insert default owner record.
        if (!doesForeignKeyExist('Owner', 'ownerID', $tuple[":ownerID"])) {
            $defaultOwner = array(":oid" => $tuple[":ownerID"]);
            executeBoundSQL(
                "INSERT INTO Owner (ownerID, ownershipPercentage, ownerName, tenantID, contractID)
             VALUES (:oid, 0, 'Default Owner', NULL, NULL)",
                array($defaultOwner)
            );
        }

        // Display the current Property table before the insert.
        $PropertyTable = executePlainSQL("SELECT * FROM Property ORDER BY propertyID");
        printResult($PropertyTable);

        // Insert the new property.
        executeBoundSQL("
        INSERT INTO Property (
            propertyID,
            propertySize,
            currentValue,
            purchasePrice,
            propertyAddress,
            propertyMgrID,
            ownerID
        )
        VALUES (
            propertyID_seq.NEXTVAL,
            :propSize,
            :currValue,
            :purPrice,
            :propAddress,
            :propMgrID,
            :ownerID
        )",
            array($tuple)
        );

        // Display the Property table after the insert.
        $propertyTable1 = executePlainSQL("SELECT * FROM Property ORDER BY propertyID");
        printResult($propertyTable1);

        oci_commit($db_conn);
    }

    // Query 2: Update
    function handleUpdateRequest()
    {
        global $db_conn;

        echo "<h2>Current Individual Records</h2>";
        $currentResults = executePlainSQL("SELECT * FROM Individual ORDER BY ownerID");
        printResult($currentResults);


        $tuple = array (
            ":ownerID" => $_POST['updateOwnerID'],
            ":socialInsuranceNum" => $_POST['updateSIN'],
            ":creditScore" => $_POST['updateCreditScore'],
        );

        // Check none of the fields are empty.
        if (empty($tuple[":ownerID"]) ||
            empty($tuple[":socialInsuranceNum"]) ||
            empty($tuple[":creditScore"])) {
            echo "Please enter all fields to update the Individual record.";
            return;
        }

        // Check numeric fields for creditScore (and ownerID if necessary).
        if (!is_numeric($tuple[":creditScore"]) || !is_numeric($tuple[":ownerID"])) {
            echo "Error: Owner ID and Credit Score must be numeric.";
            return;
        }

        // SIN format must be exactly "### ### ###" (digits separated by spaces)
        if (!preg_match('/^\d{3} \d{3} \d{3}$/', $tuple[":socialInsuranceNum"])) {
            echo "Error: Social Insurance Number must be in the format ### ### ###.";
            return;
        }


        // Make sure that no other record (with a different ownerID) already has this SIN.
        $sin = $tuple[":socialInsuranceNum"];
        $ownerID = $tuple[":ownerID"];
        $sqlUnique = "SELECT COUNT(*) AS SIN_COUNT FROM Individual WHERE socialInsuranceNum = '{$sin}' AND ownerID <> {$ownerID}";
        $uniqueStatement = executePlainSQL($sqlUnique);
        $uniqueRow = oci_fetch_array($uniqueStatement, OCI_ASSOC);
        if ($uniqueRow && $uniqueRow['SIN_COUNT'] > 0) {
            echo "Error: The social insurance number '{$sin}' is already used by another record.";
            return;
        }
        $alltuples = array ($tuple);

        // Update the Individual table
        executeBoundSQL("
				UPDATE Individual
				SET socialInsuranceNum = :socialInsuranceNum, 
				    creditScore = :creditScore
				WHERE ownerID = :ownerID
			", $alltuples);


        // Display updated Individual table.
        echo "<h2>Updated Individual Records</h2>";
        $updatedResults = executePlainSQL("SELECT * FROM Individual ORDER BY ownerID");
        printResult($updatedResults);

        oci_commit($db_conn);
    }

    // Query 3: Delete
    function handleDeleteRequest()
    {
        global $db_conn;

        // Retrieve Property ID from the form submission
        $propertyID = $_POST['deletePropertyID'];

        echo "<h2>Current Property Records</h2>";
        $currentResults = executePlainSQL("SELECT * FROM Property ORDER BY propertyID");
        printResult($currentResults);

        // Execute check query
        $checkQuery = "SELECT COUNT(*) AS PROPERTYCOUNT FROM Property WHERE propertyID = :propertyID";
        $checkTuple = array(":propertyID" => $propertyID);
        $checkResult = executeBoundSQL($checkQuery, array($checkTuple));

        // Fetch result row.
        $checkRow = oci_fetch_assoc($checkResult);
        $propertyCount = $checkRow['PROPERTYCOUNT'];

        if ($propertyCount == 0) {
            echo "<br>Property with ID " . $propertyID . " does not exist.<br>";
            return;
        }

        // Prepare the SQL statement for deletion
        $deleteQuery = "DELETE FROM Property WHERE propertyID = :propertyID";
        $deleteTuple = array(":propertyID" => $propertyID);
        executeBoundSQL($deleteQuery, array($deleteTuple));

        oci_commit($db_conn);

        echo "<br>Property with ID " . $propertyID . " has been deleted successfully.<br>";

        // Display updated Property table.
        echo "<h2>Updated Property Records</h2>";
        $updatedResults = executePlainSQL("SELECT * FROM Property ORDER BY propertyID");
        printResult($updatedResults);
    }

    // Query 4: Selection
    function handleSelectRequest()
    {
        global $db_conn;

        if (isset($_POST['selectSubmit'])) {
            // Retrieve values from the form.
            $attr1    = $_POST['attribute1'];  // e.g., "requestPriority" or "requestStatus"
            $val1     = $_POST['value1'];      // required for condition 1


            $attr2    = $_POST['attribute2'];  // e.g., "requestPriority" or "requestStatus"
            $val2     = $_POST['value2'];      // may be empty
            $logicalOp = $_POST['logicalOp'];  // "AND" or "OR"

            // Validate required condition is provided.
            if (empty($val1)) {
                echo "Please provide a value for Condition 1.";
                return;
            }

            // Start building SQL query.
            $sql = "SELECT * FROM MaintenanceRequestStatus WHERE {$attr1} = :val1";
            $bindings = array(":val1" => $val1);

            // If second condition provided, add it.
            if (!empty($val2)) {
                // Append the logical operator and condition 2.
                $sql .= " {$logicalOp} {$attr2} = :val2";
                $bindings[":val2"] = $val2;
            }



            $result = executeBoundSQL($sql, array($bindings));

            // Display results.
            if ($result) {
                printResult($result);
            } else {
                echo "No maintenance request statuses found matching the specified conditions.";
            }
        } else {
            echo "Filtering conditions not provided.";
        }
    }

    // Query 5: Projection
    function handleDisplayRequest()
    {
        global $db_conn;

        $tableName = "PropertyManager";

        // Get the attribute list from POST (if provided)
        $attributes = "";
        if (isset($_POST['attributes'])) {
            $attributes = trim($_POST['attributes']);
        }

        // Build the SQL query.
        if (!empty($attributes)) {
            $sql = "SELECT {$attributes} FROM {$tableName}";
        } else {
            $sql = "SELECT * FROM {$tableName}";
        }

        $result = executePlainSQL($sql);

        // Check if result is not null before printing.
        if ($result) {
            printResult($result);
        } else {
            echo "No data found for table: " . $tableName;
        }
    }

    // Query 6: Join
    function handleJoinRequest()
    {
        global $db_conn;

        // Retrieve the amenity type from the form.
        $tuple = array(
            ":AmenityType" => $_POST['amenityTypeJoin']
        );
        $amenity = $_POST['amenityTypeJoin'];
        $alltuples = array($tuple);

        // Build the join query 
        // It joins Property and AmenityType on propertyID, filtering on the given amenity type.
        $sql = "
        SELECT P.propertyAddress, P.purchasePrice, P.currentValue
        FROM Property P, AmenityType AT
        WHERE P.propertyID = AT.propertyID
          AND AT.amenityType = :AmenityType
    ";

        // Execute the query.
        $resultResource = executeBoundSQL($sql, $alltuples);

        // Display the results.
        if ($resultResource) {
            printResult($resultResource);
        } else {
            echo "Error: No results found for amenity type '{$amenity}'.";
        }

        oci_commit($db_conn);
    }

    // HARDCODED QUERIES: Q7 To Q10

    // Query 7: Aggregation with GROUP BY
    function handleQuery7Request() {
        global $db_conn;
        $result = executePlainSQL("SELECT propertyMgrID, COUNT(*) AS total_properties FROM Property GROUP BY propertyMgrID ORDER BY total_properties");
        echo "<h3>Number of properties managed by each property manager</h3>";
        printResult($result);
    }

    // Query 8: Aggregation with HAVING
    function handleQuery8Request() {
        global $db_conn;
        $result = executePlainSQL("SELECT ownerID, (SELECT ownerName FROM Owner WHERE Owner.ownerID = Property.ownerID) AS ownerName,
               COUNT(*) AS num_properties FROM Property GROUP BY ownerID HAVING COUNT(*) > 1");
        echo "<h3>Multi-property Owners</h3>";
        printResult($result);
    }

    // Query 9: Nested Aggregation with GROUP BY
    function handleQuery9Request() {
        global $db_conn;

        // Calculate the overall average current value of properties.
        $overallAvgResult = executePlainSQL("SELECT AVG(currentValue) AS overall_avg FROM Property");
        $overallAvgRow = oci_fetch_array($overallAvgResult, OCI_ASSOC);
        $overallAvg = $overallAvgRow['OVERALL_AVG'];

        echo "<h3>Overall Average Property Value: " . number_format($overallAvg, 2) . "</h3>";

        // Then, get the property managers whose average current value is >= the overall average.
        $sql = "SELECT propertyMgrID, AVG(currentValue) AS avg_value 
            FROM Property 
            GROUP BY propertyMgrID 
            HAVING AVG(currentValue) >= (SELECT AVG(currentValue) FROM Property)";
        $result = executePlainSQL($sql);

        echo "<h3>Property managers with average value of managed properties exceeding the overall average property value</h3>";
        printResult($result);

        oci_commit($db_conn);
    }

    // Query 10: Division Query
    function handleQuery10Request() {
        global $db_conn;
        $sql = "
    SELECT DISTINCT pm.propertyMgrID, pm.propertyMgrName
    FROM PropertyManager pm
    WHERE NOT EXISTS (
        (SELECT 'Residential' AS ptype FROM dual UNION SELECT 'Commercial' FROM dual)
        MINUS
        (
            SELECT 'Residential' FROM Residential r 
            JOIN Property p ON r.propertyID = p.propertyID 
            WHERE p.propertyMgrID = pm.propertyMgrID
            UNION
            SELECT 'Commercial' FROM Commercial c 
            JOIN Property p2 ON c.propertyID = p2.propertyID 
            WHERE p2.propertyMgrID = pm.propertyMgrID
        )
    )";
        $result = executePlainSQL($sql);
        echo "<h3>All Property Managers managing both Residential and Commercial properties</h3>";
        printResult($result);
    }


    // HANDLE ALL POST ROUTES
    function handlePOSTRequest()
    {
        if (connectToDB()) {
            if (array_key_exists('resetTablesRequest', $_POST)) {
                handleResetRequest();
            } else if (array_key_exists('updateQueryRequest', $_POST)) {
                handleUpdateRequest();
            } else if (array_key_exists('insertQueryRequest', $_POST)) {
                handleInsertRequest();
            } else if (array_key_exists('deleteQueryRequest', $_POST)) {
                handleDeleteRequest();
            } else if (array_key_exists('selectQueryRequest', $_POST)) {
                handleSelectRequest();
            } else if (array_key_exists('displayQueryRequest', $_POST)) {
                handleDisplayRequest();
            } else if (array_key_exists('joinQueryRequest', $_POST)) {
                handleJoinRequest();
            }
            disconnectFromDB();
        }
    }

    // HANDLE ALL GET ROUTES
    function handleGETRequest()
    {
        if (connectToDB()) {
            if (array_key_exists('query7Request', $_GET)) {
                handleQuery7Request();
            } elseif (array_key_exists('query8Request', $_GET)) {
                handleQuery8Request();
            } elseif (array_key_exists('query9Request', $_GET)) {
                handleQuery9Request();
            } elseif (array_key_exists('query10Request', $_GET)) {
                handleQuery10Request();
            }
            disconnectFromDB();
        }
    }

    if (isset($_POST['resetSubmit']) || isset($_POST['updateSubmit']) || isset($_POST['deleteSubmit']) ||
        isset($_POST['selectSubmit']) || isset($_POST['displaySubmit']) || isset($_POST['joinSubmit']) ||
        isset($_POST['insertSubmit'])) {
        handlePOSTRequest();
    } else if (isset($_GET['query7Request']) || isset($_GET['query8Request']) || isset($_GET['query9Request']) ||
        isset($_GET['query10Request'])) {
        handleGETRequest();
    }


    // Helpers
	function debugAlertMessage($message)
	{
		global $show_debug_alert_messages;

		if ($show_debug_alert_messages) {
			echo "<script type='text/javascript'>alert('" . $message . "');</script>";
		}
	}

	function executePlainSQL($cmdstr)
	{ //takes a plain (no bound variables) SQL command and executes it
		//echo "<br>running ".$cmdstr."<br>";
		global $db_conn, $success;

		$statement = oci_parse($db_conn, $cmdstr);
		//There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
			echo htmlentities($e['message']);
			$success = False;
		}

		$r = oci_execute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = oci_error($statement); // For oci_execute errors pass the statementhandle
			echo htmlentities($e['message']);
			$success = False;
		}

		return $statement;
	}

	function executeBoundSQL($cmdstr, $list)
	{
		/* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

		global $db_conn, $success;
		$statement = oci_parse($db_conn, $cmdstr);

		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn);
			echo htmlentities($e['message']);
			$success = False;
		}

		foreach ($list as $tuple) {
			foreach ($tuple as $bind => $val) {
				//echo $val;
				//echo "<br>".$bind."<br>";
				oci_bind_by_name($statement, $bind, $val);
				unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
			}

			$r = oci_execute($statement, OCI_DEFAULT);
			if (!$r) {
				echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
				$e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
				echo htmlentities($e['message']);
				echo "<br>";
				$success = False;
			}
		}

        return $statement;
	}

    function printResult($result)
    {
        echo "<strong>Displaying Results</strong>";
        echo "<table border='1'>";

        // Fetch column names
        echo "<tr>";
        $numCols = oci_num_fields($result);
        for ($i = 1; $i <= $numCols; $i++) {
            $colName = oci_field_name($result, $i);
            echo "<th>$colName</th>";
        }
        echo "</tr>";

        // Fetch and print rows
        while ($row = oci_fetch_array($result, OCI_ASSOC)) {
            echo "<tr>";
            foreach ($row as $col) {
                echo "<td>{$col}</td>";
            }
            echo "</tr>";
        }

        echo "</table> <br>";
    }

	function connectToDB()
	{
		global $db_conn;
		global $config;

		// Your username is ora_(CWL_ID) and the password is a(student number). For example,
		// ora_platypus is the username and a12345678 is the password.
		// $db_conn = oci_connect("ora_cwl", "a12345678", "dbhost.students.cs.ubc.ca:1522/stu");
		$db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

		if ($db_conn) {
			debugAlertMessage("Database is Connected");
			return true;
		} else {
			debugAlertMessage("Cannot connect to Database");
			$e = OCI_Error(); // For oci_connect errors pass no handle
			echo htmlentities($e['message']);
			return false;
		}
	}

	function disconnectFromDB()
	{
		global $db_conn;

		debugAlertMessage("Disconnect from Database");
		oci_close($db_conn);
	}

    // Check foreign key existence.
    function doesForeignKeyExist($tableName, $columnName, $value) {
        global $db_conn;

        // Note: In production, use bound parameters to avoid SQL injection.
        $sql = "SELECT COUNT(*) AS COUNT_RESULT FROM {$tableName} WHERE {$columnName} = {$value}";
        $statement = executePlainSQL($sql);
        $row = oci_fetch_array($statement);

        return ($row && $row[0] > 0);
    }

    function runSQLFile($filename)
    {
        global $db_conn;

        $filePath = "/home/a/aliwagih/cs304/" . $filename;
        // Read the entire SQL file into a string.
        $sqlContent = file_get_contents($filePath);
        if ($sqlContent === false) {
            echo "Error: Could not read file {$filename}.";
            return;
        }

        // Split the file content into individual SQL statements.
        // Note: This simple split on ";" works if your file doesn't have semicolons within PL/SQL blocks.
        $statements = explode(";", $sqlContent);

        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            // Skip empty statements.
            if (!empty($stmt)) {
                // If the statement ends with a "/" (common in PL/SQL blocks), remove it.
                if (substr($stmt, -1) == "/") {
                    $stmt = substr($stmt, 0, -1);
                }
                executePlainSQL($stmt);
            }
        }

        oci_commit($db_conn);
    }

	// End PHP parsing and send the rest of the HTML content
    ?>
</body>

</html>