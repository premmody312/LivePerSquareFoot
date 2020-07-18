<?php

require_once 'header.php';
require_once 'navbar.php';

if (userLoggedIn() == false){
	header("Location: index.php");
	die();
}

?>

<!DOCTYPE HTML>
<html>
<head>
</head>
<body>

<?php
	if (isset($_POST['submit'])) {
			if(!(empty($_POST["type"])))
				$type = $_POST["type"];
			if(!(empty($_POST["price"])))
				$price = $_POST["price"];
			if(!(empty($_POST["district"])))
				$district = $_POST["district"];
			if(!(empty($_POST["poi"])))
				$poi = $_POST["poi"];

			$str = "SELECT DISTINCT properties.property_id, supplier_id, address, properties.district, type, price FROM properties, districts, features WHERE properties.district = districts.District AND properties.property_id = features.property_id";

			if (!(empty($_POST["type"])) && $type != "Undecided")
				$str .= " AND properties.type = '$type'";
			if (!(empty($_POST["price"])) && $price != "Undecided")
				$str .= " AND properties.price >= '$price' AND properties.price <= ('$price' + 100)";
			if (!(empty($_POST["district"])) && $district != "Undecided")
				$str .= " AND properties.district = '$district'";
			if (!(empty($_POST["poi"])) && $poi != "Undecided")
				$str .= " AND districts.POI = '$poi'";
			if(!(empty($_POST["features"])))

  			{
  				$feature = $_POST["features"];
    			for($i=0; $i < count($feature); $i++)
    			{
      				$str .= " AND features." . $feature[$i] . " = 1";
    			}
  			}

  			/* echo "<p> '$str' </p>"; */
			$result = $con->query($str);
			?>

		<div class="container">
			<h1> Search Result </h1>

			<?php
				if ($result->num_rows <= 0) {
					echo "<div class='alert alert-warning'>
 						Your search returned no result.
						</div>";
				}
				else {
			?>

			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th> No. </th>
						<th> Owner </th>
						<th> Address </th>
						<th> District </th>
						<th> Type </th>
						<th> Price </th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php
					$i = 1;
					while ($row = $result->fetch_assoc()) {
						$supp_id = $row['supplier_id'];
						$prop_id = $row['property_id'];
						$address = $row['address'];
						$district = $row['district'];
						$type = $row['type'];
						$price = $row['price'];

						$str = "SELECT FName, LName FROM users WHERE user_id = '$supp_id'";
						$result2 = $con->query($str);
						$usr = $result2->fetch_assoc();

						echo "<tr>";
						echo "<td>" . $i . "</td>";
						echo "<td> " .$usr['FName']." ".$usr['LName']." </td>";
						echo "<td> $address </td>";
						echo "<td> $district </td>";
						echo "<td> $type </td>";
						echo "<td> $price </td>";
						?>

                        <td><a type="button" class="btn btn-info" href="view_property.php?prop_id=<?= $prop_id ?>">View</a></td>

						<?php echo "</tr>";
						$i++;
					} // end while
				}
			?>
				</tbody>
			</table>
		</div>
	<?php }
?>


	<div class="container">
		<h3 class="text-center"> Please Fill Out A Search Form </h3>
	
	<form action="search.php" method="post" class = "form-horizontal" role = "form">
		<div class = "form-group">
			<label class = "control-label col-sm-2" for = "type"> Property Type: </label>
			<div class="col-sm-offset-2 col-sm-10">
				<div class="radio">
					<label class="control-label"><input checked="" type="radio" name="type" value="Undecided"> Undecided </label>
				</div>

				<?php
					$str = "SELECT distinct type FROM properties";
					$result = $con->query($str);
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) {

				?>
				<div class="radio">
					<label class="control-label"><input type="radio" name="type" value= "<?php echo $row['type']; ?>" >
						<?php
							echo $row['type'];
						?>
					</label>
				</div>
							<?php
						}
					}
				?>
			</div>
		</div>

		<div class = "form-group">
			<label class = "control-label col-sm-2" for = "district"> District: </label>
			<div class="col-sm-offset-2 col-sm-10">
				<select class="form-control" id="Distract" name="district">
					<option> Undecided </option>
					  <option value="Dubai">Dubai</option>
                <option value="Singapore">Singapore</option>
                <option value="Malaysia">Malaysia</option>
                <option value="San Francisco">San Francisco</option>
                <option value="Las Vegas">Las Vegas</option>
                <option value="New York">New York</option>
                <option value="Toronto">Toronto</option>
                <option value="Vancouver">Vancouver</option>
					<?php
						$str = "SELECT Distinct District FROM districts";
						$result = $con->query($str);
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
					?>
					<option>
						<?php
							echo $row['District'];
						?>
					</option>
					<?php
							}
						}
					?>
				</select>
			</div>
		</div>

		<div class = "form-group">
			<label class = "control-label col-sm-2" for = "poi"> Point Of Interest: </label>
				<div class="col-sm-offset-2 col-sm-10">
				<select class="form-control" id="POI" name="poi">
					<option> Undecided </option>
					<?php
						$str = "SELECT Distinct POI FROM districts";
						$result = $con->query($str);
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
					?>
					<option>
						<?php
							echo $row['POI'];
						?>
					</option>
					<?php
							}
						}
					?>
				</select>
			</div>
		</div>

		<div class = "form-group">
			<label class = "control-label col-sm-2" for = "features"> Features: </label>
			<div class="col-sm-offset-2 col-sm-10">
				<?php
					$str = "SHOW COLUMNS FROM features";
					$result = $con->query($str);
					while($row = mysqli_fetch_array($result)){
						if ($row['Type'] == 'tinyint(1)'){
				?>
				<div class="checkbox">
					<label class="control-label"><input type="checkbox" name="features[]" value=" <?php echo $row['Field']; ?> ">
						<?php
							echo ucfirst($row['Field']);
						?>
					</label>
				</div>
				<?php
						}
					}
				?>
			</div>
		</div>

		<div class = "form-group">
			<label class = "control-label col-sm-2" for = "price"> Price Range: </label>

			<div class="col-sm-offset-2 col-sm-10">
				<div class="radio">
					<label class="control-label"><input checked="" type="radio" name="price" value="Undecided"> Undecided </label>
				</div>
				<?php
					$str = "SELECT MAX(price) AS mx FROM properties";
					$str2 = "SELECT MIN(price) AS mn FROM properties";

					$result = $con->query($str);
					$result2 = $con->query($str2);
					if ($result->num_rows > 0) {
						$row = $result->fetch_assoc();
						$max = $row['mx'];
						$range = $max/5.0;

						$row2 = $result2->fetch_assoc();
						$min = $row2['mn'];

						$range = ($max - $min) / 100;

						for ($i = 0; $i < $range; $i++){

				?>
				<div class="radio">
					<label class="control-label"><input type="radio" name="price" value= "<?php echo  (100 * $i);?>">
						<?php
							echo (100 * $i) . "-" . (100 * ($i+1));
						?>
					</label>
				</div>
				<?php
						}
					}
				?>

				<div class="form-group">
    				<div class="col-sm-offset-2 col-sm-10">
   				    	<button type="submit" class="btn btn-default" name="submit">Submit</button>
  					</div>
  				</div>
			</div>
		</div>
	</form>
	</div>
</body>
</html>
