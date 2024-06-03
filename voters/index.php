<?php
require_once("inc/header.php");
require_once("inc/navigation.php");

// Check if the user is authenticated
$isAuthenticated = isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'] === true;

// Get current date
$currentDate = date('Y-m-d');

// Check if there are active elections
$fetchingActiveElections = mysqli_query($db, "SELECT * FROM elections WHERE status = 'Active' AND starting_date <= '$currentDate' AND ending_date >= '$currentDate'") or die(mysqli_error($db));
$totalActiveElections = mysqli_num_rows($fetchingActiveElections);
$hasActiveElections = $totalActiveElections > 0;

?>

<div class="row my-3">
    <div class="col-12">
        <div class="d-flex justify-content-start align-items-center mb-4">
            <h3 class="mr-3">Voters Panel</h3>
            <?php if ($hasActiveElections && !$isAuthenticated) {?>
                <button class="btn btn-md btn-primary" id="aadharAuthBtn" onclick="location.href='index.html'">Authenticate with Aadhar</button>
            <?php } elseif ($isAuthenticated) {?>
                <button class="btn btn-md btn-success" disabled>Authenticated</button>
            <?php } else {?>
                <button class="btn btn-md btn-primary" disabled>No Active Elections</button>
            <?php }?>
        </div>

        <?php
        if ($hasActiveElections) {
            while ($data = mysqli_fetch_assoc($fetchingActiveElections)) {
                $election_id = $data['id'];
                $election_topic = $data['election_topic'];
                $starting_date = $data['starting_date'];
                $ending_date = $data['ending_date'];
        ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="4" class="bg-primary text-white">
                                <h5> ELECTION TOPIC: <?php echo strtoupper($election_topic); ?></h5>
                                <small>Start Date: <?php echo $starting_date; ?> | End Date: <?php echo $ending_date; ?></small>
                            </th>
                        </tr>
                        <tr>
                            <th> Photo </th>
                            <th> Candidate Details </th>
                            <th> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $fetchingCandidates = mysqli_query($db, "SELECT * FROM candidate_details WHERE election_id = '". $election_id ."'") or die(mysqli_error($db));

                        while ($candidateData = mysqli_fetch_assoc($fetchingCandidates)) {
                            $candidate_id = $candidateData['id'];
                            $candidate_photo = $candidateData['candidate_photo'];

                            // Fetching Candidate Votes 
                            $fetchingVotes = mysqli_query($db, "SELECT * FROM votings WHERE candidate_id = '". $candidate_id . "'") or die(mysqli_error($db));
                            $totalVotes = mysqli_num_rows($fetchingVotes);
                    ?>
                            <tr>
                                <td> <img src="<?php echo $candidate_photo; ?>" class="candidate_photo img-thumbnail" width="100"> </td>
                                <td><?php echo "<b>" . $candidateData['candidate_name'] . "</b><br />" . $candidateData['candidate_details']; ?></td>
                                <td>
                                    <?php
                                        $checkIfVoteCasted = mysqli_query($db, "SELECT * FROM votings WHERE voters_id = '". $_SESSION['user_id'] ."' AND election_id = '". $election_id ."'") or die(mysqli_error($db));    
                                        $isVoteCasted = mysqli_num_rows($checkIfVoteCasted);

                                        if ($isVoteCasted > 0) {
                                            $voteCastedData = mysqli_fetch_assoc($checkIfVoteCasted);
                                            $voteCastedToCandidate = $voteCastedData['candidate_id'];

                                            if ($voteCastedToCandidate == $candidate_id) {
                                    ?>
                                                <img src="../assets/images/vote.png" width="50px;">
                                    <?php
                                            }
                                        } else {
                                    ?>
                                            <button class="btn btn-md btn-success" onclick="CastVote(<?php echo $election_id; ?>, <?php echo $candidate_id; ?>, <?php echo $_SESSION['user_id']; ?>)"> Vote </button>
                                    <?php
                                        }
                                    ?>
                                </td>
                            </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                </table>
        <?php
            }
        } else {
            echo "<div class='alert alert-warning'>No active elections at the moment.</div>";
        }
        ?>

        <!-- Section for displaying upcoming elections -->
        <h3 class="my-3">Upcoming Elections</h3>
        <?php
        $fetchingUpcomingElections = mysqli_query($db, "SELECT * FROM elections WHERE starting_date > '$currentDate'") or die(mysqli_error($db));
        $totalUpcomingElections = mysqli_num_rows($fetchingUpcomingElections);

        if ($totalUpcomingElections > 0) {
            while ($data = mysqli_fetch_assoc($fetchingUpcomingElections)) {
                $election_id = $data['id'];
                $election_topic = $data['election_topic'];
                $starting_date = $data['starting_date'];
                $ending_date = $data['ending_date'];
        ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="3" class="bg-info text-white">
                                <h5> ELECTION TOPIC: <?php echo strtoupper($election_topic); ?> (UPCOMING)</h5>
                                <small>Start Date: <?php echo $starting_date; ?> | End Date: <?php echo $ending_date; ?></small>
                            </th>
                        </tr>
                    </thead>
                </table>
        <?php
            }
        } else {
            echo "<div class='alert alert-info'>No upcoming elections at the moment.</div>";
        }
        ?>

        <!-- Section for displaying completed elections results -->
        <h3 class="my-3">Election Results</h3>
        <?php
        $fetchingCompletedElections = mysqli_query($db, "SELECT * FROM elections WHERE status = 'Completed'") or die(mysqli_error($db));
        $totalCompletedElections = mysqli_num_rows($fetchingCompletedElections);

        if ($totalCompletedElections > 0) {
            while ($data = mysqli_fetch_assoc($fetchingCompletedElections)) {
                $election_id = $data['id'];
                $election_topic = $data['election_topic'];
                $starting_date = $data['starting_date'];
                $ending_date = $data['ending_date'];
        ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="3" class="bg-secondary text-white">
                                <h5> ELECTION TOPIC: <?php echo strtoupper($election_topic); ?> (COMPLETED)</h5>
                                <small>Start Date: <?php echo $starting_date; ?> | End Date: <?php echo $ending_date; ?></small>
                            </th>
                        </tr>
                        <tr>
                            <th> Photo </th>
                            <th> Candidate Details </th>
                            <th> Total Votes</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $fetchingCandidates = mysqli_query($db, "SELECT * FROM candidate_details WHERE election_id = '". $election_id ."'") or die(mysqli_error($db));

                        while ($candidateData = mysqli_fetch_assoc($fetchingCandidates)) {
                            $candidate_id = $candidateData['id'];
                            $candidate_photo = $candidateData['candidate_photo'];

                            // Fetching Candidate Votes 
                            $fetchingVotes = mysqli_query($db, "SELECT * FROM votings WHERE candidate_id = '". $candidate_id . "'") or die(mysqli_error($db));
                            $totalVotes = mysqli_num_rows($fetchingVotes);
                    ?>
                            <tr>
                                <td> <img src="<?php echo $candidate_photo; ?>" class="candidate_photo img-thumbnail" width="100"> </td>
                                <td><?php echo "<b>" . $candidateData['candidate_name'] . "</b><br />" . $candidateData['candidate_details']; ?></td>
                                <td><?php echo $totalVotes; ?> votes</td>
                            </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                </table>
        <?php
            }
        } else {
            echo "<div class='alert alert-info'>No completed elections to display results.</div>";
        }
        ?>
    </div>
</div>

<script>
const CastVote = (election_id, candidate_id, voters_id) => {
    if (!<?php echo json_encode($isAuthenticated); ?>) {
        alert("Please authenticate using Aadhar before casting your vote.");
        return;
    }

    $.ajax({
        type: "POST",
        url: "inc/ajaxCalls.php",
        data: "e_id=" + election_id + "&c_id=" + candidate_id + "&v_id=" + voters_id,
        success: function(response) {
            console.log(response);
            if (response == "Success") {
                location.assign("index.php?voteCasted=1");
            } else {
                location.assign("index.php?voteNotCasted=1");
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: " + status + " - " + error);
        }
    });
}
</script>

<?php
require_once("inc/footer.php");
?>
