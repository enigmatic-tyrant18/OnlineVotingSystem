<?php 
    require_once("admin/inc/config.php");
    $fetchingElections = mysqli_query($db, "SELECT * FROM elections") or die(mysqli_error($db));
    while($data = mysqli_fetch_assoc($fetchingElections))
    {
        $starting_date = $data['starting_date'];
        $ending_date = $data['ending_date'];
        $curr_date = date("Y-m-d");
        $election_id = $data['id'];
        $status = $data['status'];
        if($status == "Active")
        {
            $date1=date_create($curr_date);
            $date2=date_create($ending_date);
            $diff=date_diff($date1,$date2);
            if((int)$diff->format("%R%a") < 0)
            {
                mysqli_query($db, "UPDATE elections SET status = 'Expired' WHERE id = '". $election_id ."'") OR die(mysqli_error($db));
            }
        }else if($status == "InActive")
        {
            $date1=date_create($curr_date);
            $date2=date_create($starting_date);
            $diff=date_diff($date1,$date2);
            if((int)$diff->format("%R%a") <= 0)
            {
                mysqli_query($db, "UPDATE elections SET status = 'Active' WHERE id = '". $election_id ."'") OR die(mysqli_error($db));
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Online Voting System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container h-100">
        <div class="d-flex justify-content-center h-100">
            <div class="user_card">
                <div class="d-flex justify-content-center">
                    <div class="brand_logo_container">
                        <img src="assets/images/logo.gif" class="brand_logo" alt="Logo">
                    </div>
                </div>

                <?php 
                    if(isset($_GET['sign-up']))
                    {
                ?>
                        <div class="d-flex justify-content-center form_container">
                            <form method="POST">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" name="su_email" class="form-control input_user" placeholder="Email" required />
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" name="su_contact_no" class="form-control input_pass" placeholder="Contact #" required />
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="su_password" class="form-control input_pass" placeholder="Password" required />
                                </div>     
                                <div class="input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="su_retype_password" class="form-control input_pass" placeholder="Retype Password" required />
                                </div>         
                                <div class="d-flex justify-content-center mt-3 login_container">
                                    <button type="submit" name="sign_up_btn" class="btn login_btn">Sign Up</button>
                                </div>
                            </form>
                        </div>
                
                        <div class="mt-4">
                            <div class="d-flex justify-content-center links text-white">
                                Already Created Account? <a href="index.php" class="ml-2 text-white">Sign In</a>
                            </div>
                        </div>
                <?php
                    } else if(isset($_GET['forgot-password'])) {
                ?>
                        <div class="d-flex justify-content-center form_container">
                            <form method="POST">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" name="fp_email" class="form-control input_user" placeholder="Registered Email" required />
                                </div>
                                <div class="d-flex justify-content-center mt-3 login_container">
                                    <button type="submit" name="forgot_password_btn" class="btn login_btn">Request OTP</button>
                                </div>
                            </form>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex justify-content-center links text-white">
                                <a href="index.php" class="text-white">Back to Login</a>
                            </div>
                        </div>
                <?php
                    } else if(isset($_GET['reset-password'])) {
                ?>
                       <!-- Inside the HTML form -->
<div class="d-flex justify-content-center form_container">
    <form method="POST">
        <div class="input-group mb-3">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            </div>
            <input type="email" name="fp_email" class="form-control input_user" placeholder="Registered Email" required />
        </div>
        <div class="input-group mb-3">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
            </div>
            <input type="text" name="otp" class="form-control input_pass" placeholder="Enter OTP" required />
        </div>
        <div class="input-group mb-2">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
            </div>
            <input type="password" name="new_password" class="form-control input_pass" placeholder="New Password" required />
        </div>     
        <div class="d-flex justify-content-center mt-3 login_container">
            <button type="submit" name="reset_password_btn" class="btn login_btn">Reset Password</button>
        </div>
    </form>
</div>

                <?php
                    } else {
                ?>
                        <div class="d-flex justify-content-center form_container">
                            <form method="POST">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" name="contact_no" class="form-control input_user" placeholder="Contact No" required />
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="password" class="form-control input_pass" placeholder="Password" required />
                                </div>
                                <div class="d-flex justify-content-center mt-3 login_container">
                                    <button type="submit" name="loginBtn" class="btn login_btn">Login</button>
                                </div>
                            </form>   
                        </div>
                
                        <div class="mt-4">
                            <div class="d-flex justify-content-center links text-white">
                                Don't have an account? <a href="?sign-up=1" class="ml-2 text-white">Sign Up</a>
                            </div>
                            <div class="d-flex justify-content-center links">
                                <a href="?forgot-password=1" class="text-white">Forgot your password?</a>
                            </div>
                        </div>
                <?php
                    }      
                ?>
                <?php 
                    if(isset($_GET['registered']))
                    {
                ?>
                        <span class="bg-white text-success text-center my-3"> Your account has been created successfully! </span>
                <?php
                    } else if(isset($_GET['invalid'])) {
                ?>
                        <span class="bg-white text-danger text-center my-3"> Passwords mismatched, please try again! </span>
                <?php
                    } else if(isset($_GET['not_registered'])) {
                ?>
                        <span class="bg-white text-warning text-center my-3"> Sorry, you are not registered! </span>
                <?php
                    } else if(isset($_GET['invalid_access'])) {
                ?>
                        <span class="bg-white text-danger text-center my-3"> Invalid contact number or password! </span>
                <?php
                    }
                ?>   
                <?php 
                    if(isset($_GET['invalid_contact']))
                    {
                ?>
                        <span class="bg-white text-danger text-center my-3"> Invalid contact number! Please enter a 10-digit contact number. </span>
                <?php
                    } else if(isset($_GET['user_exists'])) {
                ?>
                        <span class="bg-white text-danger text-center my-3"> User already exists! Please try with a different email or contact number. </span>
                <?php
                    } else if(isset($_GET['invalid_email'])) {
                ?>
                        <span class="bg-white text-danger text-center my-3"> Invalid email! Please use an email ending with @gmail.com. </span>
                <?php
                    } else if(isset($_GET['otp_sent'])) {
                ?>
                        <span class="bg-white text-success text-center my-3"> OTP sent to your registered email. </span>
                <?php
                    } else if(isset($_GET['invalid_otp'])) {
                ?>
                        <span class="bg-white text-danger text-center my-3"> Invalid OTP, please try again! </span>
                <?php
                    } else if(isset($_GET['password_reset'])) {
                ?>  
                        <span class="bg-white text-success text-center my-3"> Password has been reset successfully! </span>
                <?php
                    }
                ?>             
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
<?php 
    require_once("admin/inc/config.php");
    session_start();
    if(isset($_POST['sign_up_btn']))
    {
        $su_email = mysqli_real_escape_string($db, $_POST['su_email']);
        $su_contact_no = mysqli_real_escape_string($db, $_POST['su_contact_no']);
        $su_password = mysqli_real_escape_string($db, sha1($_POST['su_password']));
        $su_retype_password = mysqli_real_escape_string($db, sha1($_POST['su_retype_password']));
        $user_role = "Voter";

        // Check if email ends with @gmail.com
        if (!preg_match("/@gmail\.com$/", $su_email)) {
            ?>
            <script> location.assign("index.php?sign-up=1&invalid_email=1"); </script>
            <?php
            exit;
        }

        // Check if contact number is 10 digits
        if(strlen($su_contact_no) != 10 || !preg_match('/^[0-9]+$/', $su_contact_no))
        {
            ?>
            <script> location.assign("index.php?sign-up=1&invalid_contact=1"); </script>
            <?php
            exit;
        }

        // Check if user already exists
        $check_user = mysqli_query($db, "SELECT * FROM users WHERE email = '" . $su_email . "' OR contact_no = '" . $su_contact_no . "'") or die(mysqli_error($db));
        if(mysqli_num_rows($check_user) > 0)
        {
            ?>
            <script> location.assign("index.php?sign-up=1&user_exists=1"); </script>
            <?php
            exit;
        }

        if($su_password == $su_retype_password)
        {
            mysqli_query($db, "INSERT INTO users(email, contact_no, password, user_role) VALUES('". $su_email ."', '". $su_contact_no ."', '". $su_password ."', '". $user_role ."')") or die(mysqli_error($db));
        ?>
            <script> location.assign("index.php?sign-up=1&registered=1"); </script>
        <?php

        }else {
    ?>
            <script> location.assign("index.php?sign-up=1&invalid=1"); </script>
    <?php
        }
             
    }else if(isset($_POST['loginBtn']))
    {
        $contact_no = mysqli_real_escape_string($db, $_POST['contact_no']);
        $password = mysqli_real_escape_string($db, sha1($_POST['password']));
        $fetchingData = mysqli_query($db, "SELECT * FROM users WHERE contact_no = '" . $contact_no . "'") or die(mysqli_error($db));
        if(mysqli_num_rows($fetchingData) > 0)
        {
            $data = mysqli_fetch_assoc($fetchingData);
            if($contact_no == $data['contact_no'] AND $password == $data['password'])
            {
                session_start();
                $_SESSION['user_role'] = $data['user_role'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['user_id'] = $data['id'];
                if($data['user_role'] == "Admin")
                {
                    $_SESSION['key'] = "AdminKey";
            ?>
                    <script> 
                    location.assign("admin/index.php?homepage=1"); </script>
            <?php
                }else {
                    $_SESSION['key'] = "VotersKey";
            ?>
                    <script> location.assign("voters/index.php"); </script>
            <?php
                }

            }else {
        ?>
                <script> location.assign("index.php?invalid_access=1"); </script>
        <?php
            }
        } else if (isset($_POST['forgot_password_btn'])) {
            // Forgot password logic
            $fp_email = mysqli_real_escape_string($db, $_POST['fp_email']);
            $check_user = mysqli_query($db, "SELECT * FROM users WHERE email = '" . $fp_email . "'") or die(mysqli_error($db));
        
            if (mysqli_num_rows($check_user) > 0) {
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['fp_email'] = $fp_email;
        
                // Send OTP to user's email
                mail($fp_email, "Your OTP for Password Reset", "Your OTP is: $otp");
        
                ?>
                <script> location.assign("index.php?reset-password=1"); </script>
                <?php
            } else {
                ?>
                <script> location.assign("index.php?forgot-password=1&not_registered=1"); </script>
                <?php
            }
        } else if (isset($_POST['reset_password_btn'])) {
            // Reset password logic
            $otp = mysqli_real_escape_string($db, $_POST['otp']);
            $new_password = mysqli_real_escape_string($db, sha1($_POST['new_password']));
            $fp_email = $_SESSION['fp_email'];
        
            if ($otp == $_SESSION['otp']) {
                $fp_email = $_SESSION['fp_email'];
                mysqli_query($db, "UPDATE users SET password = '$new_password' WHERE email = '$fp_email'") or die(mysqli_error($db));
        
                // Clear OTP and email from session
                unset($_SESSION['otp']);
                unset($_SESSION['fp_email']);
        
                ?>
                <script> location.assign("index.php?password_reset=1"); </script>
                <?php
            } else {
                ?>
                <script> location.assign("index.php?reset-password=1&invalid_otp=1"); </script>
                <?php
            }
        }else {
    ?>
            <script> location.assign("index.php?sign-up=1&not_registered=1"); </script>
    <?php
        }
    }
?>
