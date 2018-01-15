<?php
	// Connect to DB class
	class connectToDb    //Works
	{
		public $db ='';
		public function setDb()
		{
			return $this ->db = mysqli_connect('localhost', 'root', '9815064fly', 'todo2');
		}	
	}
	$connObj = new connectToDb();
	$connObj->setDb(); // connect to database
	
	class actions  // Works
	{
		
		public function submission()
		{
			$connObj = new connectToDb();
			if(isset($_POST['submit'])) // check if array is empty
			{
				// status INSERTED into  statusTable
				$status = $_POST['status']; //  form data is submitted through post method, this method stores values in an array and is indexed through give key (name given in <input type/> tags)
				mysqli_query($connObj->setDb(),"INSERT INTO statusTable (statusId) VALUES ('$status')");  // assign value to statusTable in column statusId  through a query
				
				// personId INSERTED into personTable
				$personId = $_POST['personId']; // assign value to statusTable in column statusId 
				mysqli_query($connObj->setDb(),"INSERT INTO personTable (personId) VALUES ('$personId')"); 
				
				///  task and date inserted into  Task table
				$task = $_POST['task']; 
				$date = $_POST['date']; 
				//Execute query for task table
				mysqli_query($connObj->setDb(),"INSERT INTO tasks (task,state,dueDate,personId) VALUES ('$task','$status','$date','$personId')"); 
				
				
		    }
		}
		
		/////
		public function deleteRow()
		{
			$connObj = new connectToDb();
			if(isset($_GET['del_task'])) // check if index 'del_task' exists, if so continue through delete logic
			{
				$id = $_GET['del_task']; // get a value from the stored array using index key ('del_task') 
				mysqli_query($connObj->setDb(), "DELETE FROM tasks WHERE id = $id");
			}
		}
	}
	$subObj = new actions();
	$subObj->submission();
	$subObj->deleteRow();
	
	
	class myQuery
	{
		public $tasks ='';
		public function displayFilter()
		{
				$connObj = new connectToDb();
				//Display normally
				if(isset($_GET['status_type']) == NULL) // check if status_type has returned anything (ie if null because no user clicked on status)
				{
					return $this->tasks = mysqli_query($connObj->setDb(), "SELECT * FROM tasks"); 
				}
				//Display Filter
				if(isset($_GET['status_type']))
				{
					$status = $_GET['status_type'];
					return $this ->tasks = mysqli_query($connObj->setDb(), "SELECT * FROM tasks WHERE state = '$status'");
				}
		}
	}
?>


<!DOCTYPE html>
<html lang = "en">
<head>  
<title> To do list </title>
<link rel ="stylesheet" href = "todo.css" />
</head>
<body>

		<div class = "heading">
		<h2> Todo list application <br> </h2>
		<h4> Enter your Task/Status(s,c,l,p)/Due Date/PersonID </h4>
		</div>
		
		<form method ="POST" action = "index.php">
			<input type ="text" name = "task" class ="task_input"> 
			<input type ="text" name = "status" class ="task_status">
			<input type ="text" name = "date" class ="task_date">
			<input type ="text" name = "personId" class ="task_date">
			
			<input type ="submit" class ="task_btn" name ="submit"> <span class ="format"></span></input>
		</form>
		
		<table>
			<thead>
				<tr>
					<th> N </th>
					<th> Task </th>
					<th> Status </th>
					<th> DD </th>
					<th> personID </th>
					<th> Remove </th>
				</tr>
			</thead>
			
			<tbody>
			
			<!--Display Table contents.  While mysqli_fetch_array is returning strings to corresponding rows from table $tasks, execute display logic else return false when there are no more rows
			PHP interpreter stops stops once it reachs ?/ unless in the middle of a condititional statement
			-->
			<?php 
				$i = 1; 
				$queryObj = new myQuery();
				$tasks = $queryObj->displayFilter(); // calls a function that returns a query based on if status was clicked or not
				while ($row = mysqli_fetch_array($tasks)) {?>  <!-- $row is set to an array based on the query made in $tasks -->
				<tr>
					<td> <?php echo $i; // display id column?> </td> 
					<td class ="task"> <?php echo $row['task']; // display task?> </td>
					<td>  <a href= "index.php?status_type=<?php echo $row['state'];?>" target = "_blank"> <?php echo $row['state']; //display state?> </td>
					<td> <?php echo $row['dueDate']; // display dueDate column?> </td>
					<td> <?php echo $row['personId'];?> </td>
					<td class = "delete"> <a href ="index.php?del_task= <?php echo $row['id'] ?>"> x</a></td>  <!-- Assign variable del_task = current id value of given row (After ? in url become $_GET Var  -->
				</tr>
			<?php  $i++;} // end of while loop?>
			</tbody>
		</table>
	
	<br>
	<br>
	<br>
	<br>
	<br>
	
	<table>
			<thead>
				<th>Pending:</th>
				<th>Started:</th>
				<th>Completed:</th>
				<th>Late:</th>
				<th>Total:</th>
			
			</thead>
			
			<tbody>
				<tr>
					<td> 
					<?php 
					$query = "SELECT COUNT(*) c FROM tasks WHERE state = 'p' OR state ='P'";
					$result = mysqli_query($connObj->setDb(),$query); // Perform a query on passed DB 
					$row = mysqli_fetch_assoc($result);
					echo $row['c']; 
					?> 
					</td> 
					<td> 
					<?php 
					$query = "SELECT COUNT(*) c FROM tasks WHERE state = 's' OR state ='S'";
					$result = mysqli_query($connObj->setDb(),$query);
					$row = mysqli_fetch_assoc($result);
					echo $row['c']; 
					?> 
					</td>
					<td> 
					<?php 
					$query = "SELECT COUNT(*) c FROM tasks WHERE state = 'c' OR state ='C'";
					$result = mysqli_query($connObj->setDb(),$query);
					$row = mysqli_fetch_assoc($result);
					echo $row['c']; 
					?> 
					</td>
					<td> 
					<?php 
					$query = "SELECT COUNT(*) c FROM tasks WHERE state = 'l' OR state ='L'";
					$result = mysqli_query($connObj->setDb(),$query);
					$row = mysqli_fetch_assoc($result);
					echo $row['c']; 
					?> 
					</td> 
					<td> 
					<?php 
					$query = "SELECT COUNT(*) c FROM tasks WHERE state = 'l' OR state ='L' OR 'c' OR state ='C' OR 's' OR state ='S' OR 'p' OR state ='P'";
					$result = mysqli_query($connObj->setDb(),$query);
					$row = mysqli_fetch_assoc($result);
					echo $row['c']; 
					?> 
					</td> 
				</tr>
			</tbody>
		</table>
	

</body>
</html>