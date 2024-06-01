<?php
session_start();

if (isset($_POST['authenticated']) && $_POST['authenticated'] == 'true') {
    $_SESSION['is_authenticated'] = true;
    echo "Authenticated";
} else {
    echo "Authentication failed";
}
