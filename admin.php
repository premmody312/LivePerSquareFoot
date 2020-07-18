<?php

/*
* Admin page.
* Shows a list of all available properties in LivePerSquareFoot with CRUD functionality.
* Shows a list of all users who owns a property
*/



require_once 'header.php';
require_once 'navbar.php';

// Check if there's a logged in user
if( userLoggedIn() ) {

  // Check if the logged in user is an admin
  $query = "SELECT is_admin FROM users WHERE user_id=?";

  // prepare query for execution
  if($stmt = $con->prepare($query)){

      $stmt->bind_Param("s", $_SESSION['user_id']);
      $stmt->execute();
      $result = $stmt->get_result();

      $num = $result->num_rows;;

      if($num>0){

          //If the user_id matches a user in our db, get the is_admin value
          $row = $result->fetch_assoc();

          // If the current logged in user is NOT an admin, redirect to profile.php
          if($row['is_admin'] == 0){
              header("Location: profile.php");
              die();
          }
      }

  }
  else {
    echo "Failed to prepare the SQL";
    // If we can't check if the
    header("Location: profile.php");
    die();
  }
}
else { // No logged in user
  header("Location: index.php");
  die();
}

// At this point we should have made sure the current logged in user is an admin
?>

<!DOCTYPE HTML>
<html>
<body>

    <h1 class="text-center">Hello Admin</h1>

    <?php
    // Select all properties and Supplier FName and LName
    $query = "SELECT property_id, address, district, type, price, FName, LName FROM properties, users where supplier_id = user_id";

    // prepare query for execution
    if($stmt = $con->prepare($query)){

        $stmt->execute();
        $result = $stmt->get_result();
        $num = $result->num_rows;;

        if($num == 0)
          echo "<h3>There are no properties currently registered in LivePerSquareFoot</h3>";
        else {
        ?>

        <div class="container">
          <h2>Current Properties in LivePerSquareFoot</h2>
          <table class="table table-bordered table-hover" >
            <thead>
            <tr>
              <th>No.</th>
              <th>Address</th>
              <th>District</th>
              <th>Type</th>
              <th>Price per month</th>
              <th>Supplier Name</th>
              <th>Action</th>
            </tr>
            </thead>

            <tbody>

            <?php
              $i = 1;
              while($row = $result->fetch_assoc()):
            ?>
            <tr>
              <td><?= $i; $i++ ?></td>
              <td><?=$row['address'] ?></td>
              <td><?=$row['district']?></td>
              <td><?=$row['type']?></td>
              <td><?=$row['price']?></td>
              <td><?=$row['FName'] . ' ' . $row['LName']?></td>
              <td>
              <a type="button" class="btn btn-info" href="view_property.php?prop_id=<?=$row['property_id']?>">View</a>
              <a type="button" class="btn btn-primary" href="property_history.php?prop_id=<?=$row['property_id']?>">Summarize History</a>
              <a type="button" class="btn btn-danger" href="delete_property.php?property_id=<?=$row['property_id']?>">Delete</a>
              </td>
            </tr>

              <?php
              endwhile; // $row = $result->fetch_assoc()
            } // end else for '$num == 0'
          } // end if $stmt prepare
          ?>
            </tbody>

          </table>
        </div>

    <hr class = "colorgraph"> <br>

    <?php
    // Get a list of all users in LivePerSquareFoot
    $query = "SELECT user_id, FName, LName, gender, email, phone_no, grad_year, faculty_id, degree_type FROM users";

    $stmt = $con->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    $num = $result->num_rows;



    if($num == 0) // No users currently registered in LivePerSquareFoot
      echo "<h3>Other than you, there are currently no users registered in LivePerSquareFoot</h3>";
    else{
    ?>

    <!-- List of current users in LivePerSquareFoot -->
    <div class="container">
      <h2>Current Users in LivePerSquareFoot</h2>

      <table class="table table-bordered table-hover" >
      <thead>
        <tr>
          <th>No.</th>
          <th>Supplier Name</th>
          <th>Gender</th>
          <th>Email</th>
          <th>Phone No.</th>
          <th>Graduation Year</th>
          <th>Faculty</th>
          <th>Degree Type</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
    <?php
        $i = 1;
        while($users = $result->fetch_assoc()):

          // Get Faculty name from Facutlies table
          $query = "SELECT faculty FROM faculties WHERE faculty_id=?";

          $stmt = $con->prepare($query);
          $stmt->bind_Param("i", $users['faculty_id']);
          $stmt->execute();
          $result_fac_name = $stmt->get_result();

          $facultyName = $result_fac_name->fetch_assoc();

          // If the current user is the logged in user, skip that user since Admins can't
          // delete themselves.
          if($users['user_id'] == $_SESSION['user_id'])
            continue;
    ?>

          <tr>
            <td><?= $i; $i++ ?></td>
            <td><?= $users['FName'] . ' ' . $users['LName'] ?></td>
            <td><?= $users['gender']?></td>
            <td><?= $users['email']?></td>
            <td><?= $users['phone_no']?></td>
            <td><?= $users['grad_year']?></td>
            <td><?= $facultyName['faculty']?></td>
            <td><?= $users['degree_type'] ?></td>
            <td class="col-md-3">
              <a type="button" class="btn btn-primary" href="booking_requests.php?user_id=<?=$users['user_id']?>">Summarize History</a>
              <a type="button" class="btn btn-danger" href=<?php echo "delete_user.php?user_id=" . $users['user_id']?> > Delete User</a>
            </td>
          </tr>

        <?php
        endwhile;
    } // enc else of if($num == 0)
    ?>

      </tbody>
    </table>

  </div>
</body>
</html>
