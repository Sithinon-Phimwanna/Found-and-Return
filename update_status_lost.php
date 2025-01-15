<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $status_id = $_POST['status_id'];

    $query = "UPDATE lost_items SET status_id = ? WHERE item_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $status_id, $item_id);
    $stmt->execute();
    $stmt->close();

    $mysqli->close();
    header('Location: lost_items_list.php');
    exit;
}
?>
<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $item_id = (int) $_POST['item_id']; // Assuming item_id is an integer
    $status_id = (int) $_POST['status_id']; // Assuming status_id is an integer

    // Prepare the query
    $query = "UPDATE lost_items SET status_id = ? WHERE item_id = ?";
    if ($stmt = $mysqli->prepare($query)) {
        // Bind parameters
        $stmt->bind_param('ii', $status_id, $item_id);

        // Execute the query
        if ($stmt->execute()) {
            // Close statement and connection
            $stmt->close();
            $mysqli->close();

            // Redirect with success message
            header('Location: lost_items_list.php?update_success=true');
            exit;
        } else {
            // If the update fails, redirect with an error message
            $stmt->close();
            $mysqli->close();
            header('Location: lost_items_list.php?update_error=true');
            exit;
        }
    } else {
        // If preparing the statement fails, redirect with an error message
        $mysqli->close();
        header('Location: lost_items_list.php?update_error=true');
        exit;
    }
}
?>
