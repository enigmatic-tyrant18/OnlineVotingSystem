<?php
if (isset($_GET['added'])) {
?>
    <div class="alert alert-success my-3" role="alert">
        Candidate has been added successfully.
    </div>
<?php
} else if (isset($_GET['updated'])) {
?>
    <div class="alert alert-success my-3" role="alert">
        Candidate has been updated successfully.
    </div>
<?php
} else if (isset($_GET['largeFile'])) {
?>
    <div class="alert alert-danger my-3" role="alert">
        Candidate image is too large, please upload a smaller file (you can upload any image up to 2MB).
    </div>
<?php
} else if (isset($_GET['invalidFile'])) {
?>
    <div class="alert alert-danger my-3" role="alert">
        Invalid image type (Only .jpg, .png files are allowed).
    </div>
<?php
} else if (isset($_GET['failed'])) {
?>
    <div class="alert alert-danger my-3" role="alert">
        Image uploading failed, please try again.
    </div>
<?php
}

$candidate_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$candidate = null;

if ($candidate_id > 0) {
    $candidate_query = mysqli_query($db, "SELECT * FROM candidate_details WHERE id = " . $candidate_id) or die(mysqli_error($db));
    $candidate = mysqli_fetch_assoc($candidate_query);
}
?>

<div class="row my-3">
    <div class="col-4">
        <h3><?php echo $candidate ? "Edit Candidate" : "Add New Candidates"; ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="candidate_id" value="<?php echo $candidate ? $candidate['id'] : ''; ?>" />
            <div class="form-group">
                <select class="form-control" name="election_id" required <?php echo $candidate ? 'disabled' : ''; ?>>
                    <option value="">Select Election</option>
                    <?php
                    $fetchingElections = mysqli_query($db, "SELECT * FROM elections") or die(mysqli_error($db));
                    $isAnyElectionAdded = mysqli_num_rows($fetchingElections);
                    if ($isAnyElectionAdded > 0) {
                        while ($row = mysqli_fetch_assoc($fetchingElections)) {
                            $election_id = $row['id'];
                            $election_name = $row['election_topic'];
                            $allowed_candidates = $row['no_of_candidates'];

                            // Now checking how many candidates are added in this election 
                            $fetchingCandidate = mysqli_query($db, "SELECT * FROM candidate_details WHERE election_id = '" . $election_id . "'") or die(mysqli_error($db));
                            $added_candidates = mysqli_num_rows($fetchingCandidate);

                            if ($added_candidates < $allowed_candidates || ($candidate && $candidate['election_id'] == $election_id)) {
                    ?>
                                <option value="<?php echo $election_id; ?>" <?php echo $candidate && $candidate['election_id'] == $election_id ? 'selected' : ''; ?>><?php echo $election_name; ?></option>
                    <?php
                            }
                        }
                    } else {
                    ?>
                        <option value="">Please add election first</option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="candidate_name" placeholder="Candidate Name" class="form-control" value="<?php echo $candidate ? $candidate['candidate_name'] : ''; ?>" required />
            </div>
            <div class="form-group">
                <input type="file" name="candidate_photo" class="form-control" />
            </div>
            <div class="form-group">
                <input type="text" name="candidate_details" placeholder="Candidate Details" class="form-control" value="<?php echo $candidate ? $candidate['candidate_details'] : ''; ?>" required />
            </div>
            <input type="submit" value="<?php echo $candidate ? "Update Candidate" : "Add Candidate"; ?>" name="saveCandidateBtn" class="btn btn-success" />
        </form>
    </div>

    <div class="col-8">
        <h3>Candidate Details</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Photo</th>
                    <th scope="col">Name</th>
                    <th scope="col">Details</th>
                    <th scope="col">Election</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $fetchingData = mysqli_query($db, "SELECT * FROM candidate_details") or die(mysqli_error($db));
                $isAnyCandidateAdded = mysqli_num_rows($fetchingData);

                if ($isAnyCandidateAdded > 0) {
                    $sno = 1;
                    while ($row = mysqli_fetch_assoc($fetchingData)) {
                        $election_id = $row['election_id'];
                        $fetchingElectionName = mysqli_query($db, "SELECT * FROM elections WHERE id = '" . $election_id . "'") or die(mysqli_error($db));
                        $execFetchingElectionNameQuery = mysqli_fetch_assoc($fetchingElectionName);
                        $election_name = isset($execFetchingElectionNameQuery['election_topic']) ? $execFetchingElectionNameQuery['election_topic'] : null;

                        $candidate_photo = $row['candidate_photo'];

                ?>
                        <tr>
                            <td><?php echo $sno++; ?></td>
                            <td>
                                <?php if ($candidate_photo) { ?>
                                    <img src="<?php echo $candidate_photo; ?>" class="candidate_photo" />
                                <?php } else { ?>
                                    No Photo
                                <?php } ?>
                            </td>
                            <td><?php echo $row['candidate_name']; ?></td>
                            <td><?php echo $row['candidate_details']; ?></td>
                            <td><?php echo $election_name; ?></td>
                            <td>
                                <a href="index.php?addCandidatePage=1&edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="index.php?addCandidatePage=1&delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="7">No candidate has been added yet.</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
if (isset($_POST['saveCandidateBtn'])) {
    $candidate_id = intval($_POST['candidate_id']);
    $election_id = mysqli_real_escape_string($db, $_POST['election_id']);
    $candidate_name = mysqli_real_escape_string($db, $_POST['candidate_name']);
    $candidate_details = mysqli_real_escape_string($db, $_POST['candidate_details']);
    $inserted_by = $_SESSION['username'];
    $inserted_on = date("Y-m-d");

    // Photograph Logic Starts
    if (!empty($_FILES['candidate_photo']['name'])) {
        $targetted_folder = "../assets/images/candidate_photos/";
        $candidate_photo = $targetted_folder . rand(111111111, 99999999999) . "_" . rand(111111111, 99999999999) . $_FILES['candidate_photo']['name'];
        $candidate_photo_tmp_name = $_FILES['candidate_photo']['tmp_name'];
        $candidate_photo_type = strtolower(pathinfo($candidate_photo, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "png", "jpeg");
        $image_size = $_FILES['candidate_photo']['size'];

        if ($image_size < 2000000) { // 2 MB
            if (in_array($candidate_photo_type, $allowed_types)) {
                if (move_uploaded_file($candidate_photo_tmp_name, $candidate_photo)) {
                    if ($candidate_id > 0) {
                        // updating db with photo
                        mysqli_query($db, "UPDATE candidate_details SET candidate_name = '$candidate_name', candidate_details = '$candidate_details', candidate_photo = '$candidate_photo', inserted_by = '$inserted_by', inserted_on = '$inserted_on' WHERE id = $candidate_id") or die(mysqli_error($db));
                    } else {
                        // inserting into db with photo
                        mysqli_query($db, "INSERT INTO candidate_details(election_id, candidate_name, candidate_details, candidate_photo, inserted_by, inserted_on) VALUES('$election_id', '$candidate_name', '$candidate_details', '$candidate_photo', '$inserted_by', '$inserted_on')") or die(mysqli_error($db));
                    }

                    echo "<script> location.assign('index.php?addCandidatePage=1&" . ($candidate_id > 0 ? "updated=1" : "added=1") . "'); </script>";
                } else {
                    echo "<script> location.assign('index.php?addCandidatePage=1&failed=1'); </script>";
                }
            } else {
                echo "<script> location.assign('index.php?addCandidatePage=1&invalidFile=1'); </script>";
            }
        } else {
            echo "<script> location.assign('index.php?addCandidatePage=1&largeFile=1'); </script>";
        }
    } else {
        if ($candidate_id > 0) {
            // updating db without photo
            mysqli_query($db, "UPDATE candidate_details SET candidate_name = '$candidate_name', candidate_details = '$candidate_details', inserted_by = '$inserted_by', inserted_on = '$inserted_on' WHERE id = $candidate_id") or die(mysqli_error($db));
        } else {
            // inserting into db without photo
            mysqli_query($db, "INSERT INTO candidate_details(election_id, candidate_name, candidate_details, inserted_by, inserted_on) VALUES('$election_id', '$candidate_name', '$candidate_details', '$inserted_by', '$inserted_on')") or die(mysqli_error($db));
        }

        echo "<script> location.assign('index.php?addCandidatePage=1&" . ($candidate_id > 0 ? "updated=1" : "added=1") . "'); </script>";
    }
}

// Handle candidate deletion
if (isset($_GET['delete'])) {
    $candidate_id = intval($_GET['delete']);
    mysqli_query($db, "DELETE FROM candidate_details WHERE id = $candidate_id") or die(mysqli_error($db));
    echo "<script> location.assign('index.php?addCandidatePage=1'); </script>";
}
?>
