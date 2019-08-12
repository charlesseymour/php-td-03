<?php 

session_start();

require_once("database.php"); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
	$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
	$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
	$time_spent = filter_input(INPUT_POST, 'timeSpent', FILTER_SANITIZE_NUMBER_INT);
	$learned = filter_input(INPUT_POST, 'whatILearned', FILTER_SANITIZE_STRING);
	$resources = filter_input(INPUT_POST, 'ResourcesToRemember', FILTER_SANITIZE_STRING);
	$tags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
	
	if (empty($title) || empty($date) || empty($time_spent) || empty($learned)) {
		$alert = '<p style="color:red">Please fill in all required fields.</p>';
	} else {
		try {
			$add = $db->prepare('INSERT INTO entries (title, date, time_spent, learned, resources, tags)
				                VALUES (?, ?, ?, ?, ?, ?)');
			$add->bindParam(1, $title);
			$add->bindParam(2, $date);
			$add->bindParam(3, $time_spent);
			$add->bindParam(4, $learned);
			$add->bindParam(5, $resources);
			$add->bindParam(6, $tags);
			$add->execute();
			$_SESSION['status'] = "Entry added!";
			$results = $db->query('SELECT last_insert_rowid()');
			$new_entry_id = $results->fetch(PDO::FETCH_NUM);
			var_dump($new_entry_id[0]);
			header("Location: /detail.php?id=" . $new_entry_id[0]);
			exit();
		} catch (Exception $e) {
			echo('<p style="color:red">'.$e->getMessage().'</p>');
		}
	}
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>MyJournal</title>
        <link href="https://fonts.googleapis.com/css?family=Cousine:400" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Work+Sans:600" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/site.css">
    </head>
    <body>
        <?php include "inc/header.php"; ?>
        <section>
            <div class="container">
                <div class="new-entry">
					<?php if (isset($alert)) { echo $alert; } ?>
                    <h2>New Entry</h2>
                    <form action="new.php" method="post">
                        <label for="title"> Title</label>
                        <input id="title" type="text" name="title" <?php 
							if (isset($_POST["title"])) { echo('value="' . $_POST['title'] . '"'); } ?>>
							<br>
                        <label for="date">Date</label>
                        <input id="date" type="date" name="date" <?php 
							if (isset($_POST["date"])) { echo('value="' . $_POST['date'] . '"'); } ?>>
							<br>
                        <label for="time-spent"> Time Spent</label>
                        <input id="time-spent" type="text" name="timeSpent" <?php 
							if (isset($_POST["timeSpent"])) { echo('value="' . $_POST['timeSpent'] . '"'); } ?>>
							<br>
                        <label for="what-i-learned">What I Learned</label>
                        <textarea id="what-i-learned" rows="5" name="whatILearned"><?php 
							if (isset($_POST["whatILearned"])) { echo($_POST['whatILearned']); } ?></textarea>
                        <label for="resources-to-remember">Resources to Remember (optional)</label>
                        <textarea id="resources-to-remember" rows="5" name="ResourcesToRemember"><?php 
							if (isset($_POST["ResourcesToRemember"])) { echo($_POST['ResourcesToRemember']); } ?></textarea>
                        <label for="tags">Tags (optional, separate with space)</label>
						<input id="tags" type="text" name="tags" <?php
							if (isset($_POST["tags"])) { echo('value="' . $_POST["tags"] . '"'); } ?>>
							<br>
						<input type="submit" value="Publish Entry" class="button">
                        <a href="#" class="button button-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </section>
        <?php include "inc/footer.php"; ?>
    </body>
</html>
