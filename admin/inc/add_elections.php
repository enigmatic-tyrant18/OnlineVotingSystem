<?php
require_once("inc/header.php");
require_once("inc/navigation.php");

// Check if an election is being edited
$editMode = false;
if (isset($_GET['edit_id'])) {
    $editMode = true;
    $edit_id = $_GET['edit_id'];
    $editQuery = mysqli_query($db, "SELECT * FROM elections WHERE id = '$edit_id'") or die(mysqli_error($db));
    $editData = mysqli_fetch_assoc($editQuery);
}

// Handle form submission for adding/editing an election
if (isset($_POST['addElectionBtn']) || isset($_POST['editElectionBtn'])) {
    $election_topic = mysqli_real_escape_string($db, $_POST['election_topic']);
    $number_of_candidates = mysqli_real_escape_string($db, $_POST['number_of_candidates']);
    $starting_date = mysqli_real_escape_string($db, $_POST['starting_date']);
    $ending_date = mysqli_real_escape_string($db, $_POST['ending_date']);
    $inserted_by = $_SESSION['username'];
    $inserted_on = date("Y-m-d");

    $current_date = date("Y-m-d");

    if ($number_of_candidates < 2) {
        echo '<div class="alert alert-danger my-3" role="alert">Number of candidates must be at least 2.</div>';
    } elseif ($starting_date >= $current_date && $ending_date >= $current_date) {
        $date1 = date_create($inserted_on);
        $date2 = date_create($starting_date);
        $diff = date_diff($date1, $date2);

        if ((int)$diff->format("%R%a") > 0) {
            $status = "InActive";
        } else {
            $status = "Active";
        }

        if (isset($_POST['addElectionBtn'])) {
            // Insert new election
            mysqli_query($db, "INSERT INTO elections(election_topic, no_of_candidates, starting_date, ending_date, status, inserted_by, inserted_on) VALUES('$election_topic', '$number_of_candidates', '$starting_date', '$ending_date', '$status', '$inserted_by', '$inserted_on')") or die(mysqli_error($db));
            echo "<script> location.assign('index.php?addElectionPage=1&added=1'); </script>";
        } else if (isset($_POST['editElectionBtn'])) {
            // Update existing election
            $edit_id = $_POST['edit_id'];
            mysqli_query($db, "UPDATE elections SET election_topic='$election_topic', no_of_candidates='$number_of_candidates', starting_date='$starting_date', ending_date='$ending_date', status='$status' WHERE id='$edit_id'") or die(mysqli_error($db));
            echo "<script> location.assign('index.php?addElectionPage=1&updated=1'); </script>";
        }
    } else {
        echo '<div class="alert alert-danger my-3" role="alert">Starting and Ending dates must be today or a future date.</div>';
    }
}

// Handle deletion of an election
if (isset($_GET['delete_id'])) {
    $d_id = $_GET['delete_id'];

    // Delete related rows in the 'votings' table first
    mysqli_query($db, "DELETE FROM votings WHERE election_id = '$d_id'") or die(mysqli_error($db));

    // Now delete the election
    mysqli_query($db, "DELETE FROM elections WHERE id = '$d_id'") or die(mysqli_error($db));
    echo '<div class="alert alert-danger my-3" role="alert">Election has been deleted successfully!</div>';
}

// Handle success messages
if (isset($_GET['added'])) {
    echo '<div class="alert alert-success my-3" role="alert">Election has been added successfully.</div>';
} else if (isset($_GET['updated'])) {
    echo '<div class="alert alert-success my-3" role="alert">Election has been updated successfully.</div>';
}

?>

<div class="row my-3">
    <div class="col-4">
        <h3><?php echo $editMode ? 'Edit Election' : 'Add New Election'; ?></h3>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="election_topic" placeholder="Election Topic" class="form-control" value="<?php echo $editMode ? $editData['election_topic'] : ''; ?>" required />
            </div>
            <div class="form-group">
                <input type="number" name="number_of_candidates" placeholder="No. of Candidates" class="form-control" value="<?php echo $editMode ? $editData['no_of_candidates'] : ''; ?>" required />
            </div>
            <div class="form-group">
                <input type="text" onfocus="this.type='date'" name="starting_date" placeholder="Starting Date" class="form-control" value="<?php echo $editMode ? $editData['starting_date'] : ''; ?>" required />
            </div>
            <div class="form-group">
                <input type="text" onfocus="this.type='date'" name="ending_date" placeholder="Ending Date" class="form-control" value="<?php echo $editMode ? $editData['ending_date'] : ''; ?>" required />
            </div>
            <?php if ($editMode) { ?>
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>" />
                <input type="submit" value="Update Election" name="editElectionBtn" class="btn btn-warning" />
                <a href="index.php?addElectionPage=1" class="btn btn-secondary">Cancel</a>
            <?php } else { ?>
                <input type="submit" value="Add Election" name="addElectionBtn" class="btn btn-success" />
            <?php } ?>
        </form>
    </div>

    <div class="col-8">
        <h3>Upcoming Elections</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Election Name</th>
                    <th scope="col"># Candidates</th>
                    <th scope="col">Starting Date</th>
                    <th scope="col">Ending Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $fetchingData = mysqli_query($db, "SELECT * FROM elections") or die(mysqli_error($db)); 
                    $isAnyElectionAdded = mysqli_num_rows($fetchingData);

                    if ($isAnyElectionAdded > 0) {
                        $sno = 1;
                        while ($row = mysqli_fetch_assoc($fetchingData)) {
                            $election_id = $row['id'];
                ?>
                            <tr>
                                <td><?php echo $sno++; ?></td>
                                <td><?php echo $row['election_topic']; ?></td>
                                <td><?php echo $row['no_of_candidates']; ?></td>
                                <td><?php echo $row['starting_date']; ?></td>
                                <td><?php echo $row['ending_date']; ?></td>
                                <td><?php echo $row['status']; ?></td>
                                <td>
                                    <a href="index.php?addElectionPage=1&edit_id=<?php echo $election_id; ?>" class="btn btn-sm btn-warning"> Edit </a>
                                    <button class="btn btn-sm btn-danger" onclick="DeleteData(<?php echo $election_id; ?>)"> Delete </button>
                                </td>
                            </tr>
                <?php
                        }
                    } else {
                ?>
                        <tr>
                            <td colspan="7">No elections added yet.</td>
                        </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const DeleteData = (e_id) => {
        let c = confirm("Are you sure you want to delete it?");
        if (c == true) {
            location.assign("index.php?addElectionPage=1&delete_id=" + e_id);
        }
    }
</script>

<?php
require_once("inc/footer.php");
?>
