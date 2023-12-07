
<?php 
    require_once("../../admin/inc/config.php");
    echo $_POST['e_id'];
    if(isset($_POST['e_id']) AND isset($_POST['c_id']) AND isset($_POST['v_id']))
    {
        echo "data rreached";
        $vote_date = date("Y-m-d");
        $vote_time = date("h:i:s");

        mysqli_query($db, "INSERT INTO votings(election_id, voters_id, candidate_id, vote_date, vote_time) VALUES('". $_POST['e_id'] ."', '". $_POST['v_id'] ."','". $_POST['c_id'] ."','". $vote_date ."','". $vote_time ."')") or die(mysqli_error($db));

        echo "Success";
    }
    else echo "Failure";

?>
