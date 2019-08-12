<?php 

session_start();

require_once("database.php"); 

if(!empty($_GET['id'])) {
	$entry_id = intval($_GET['id']);
}

try {
	$results = $db->prepare('select * from entries
							join entries_tags on entries.id = entries_tags.entry_id
							join tags on entries_tags.tag_id = tags.id
							where entries.id = ?');
	$results->bindParam(1, $entry_id);
	$results->execute();
} catch(Exception $e) {
	echo $e->getMessage();
	die();
}
$entry_rows = $results->fetchAll(PDO::FETCH_ASSOC);
//echo($entry_rows[0]["entry_id"]);
//var_dump($entry_rows);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
	$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
	$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
	$time_spent = filter_input(INPUT_POST, 'timeSpent', FILTER_SANITIZE_NUMBER_INT);
	$learned = filter_input(INPUT_POST, 'whatILearned', FILTER_SANITIZE_STRING);
	$resources = filter_input(INPUT_POST, 'ResourcesToRemember', FILTER_SANITIZE_STRING);
	$tags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
	
	if (empty($id) || empty($title) || empty($date) || empty($time_spent) || empty($learned)) {
		$alert = '<p style="color:red">Please fill in all required fields.</p>';
	} else {
		try {
			$update = $db->prepare('UPDATE entries SET title = ?, date = ?, time_spent = ?,
				   learned = ?, resources = ? WHERE id = ?');
			$update->bindParam(1, $title);
			$update->bindParam(2, $date);
			$update->bindParam(3, $time_spent);
			$update->bindParam(4, $learned);
			$update->bindParam(5, $resources);
			$update->bindParam(6, $id);
			$update->execute();
			$tags = trim($tags);
			$tags = preg_replace('/\s+/', ' ', $tags);
			//echo('$tags = ' . $tags);
			$tags_array = explode(" ", $tags);
			//echo('$tags_array = ');
			//var_dump($tags_array);
			foreach ($tags_array as $tag_string) {
				$tag_string = trim($tag_string);
				//echo('$tag_string = ' . $tag_string);
				$tag_add = $db->prepare('INSERT OR IGNORE INTO tags (tag) VALUES (?)');
				$tag_add->bindParam(1, $tag_string);
				$tag_add->execute();
				$tag_query = $db->prepare('SELECT * FROM tags WHERE tag = ?');
				$tag_query->bindParam(1, $tag_string);
				$tag_query->execute();
				$matching_tag = $tag_query->fetch(PDO::FETCH_ASSOC);
				//var_dump($matching_tag);
				$tag_id = $matching_tag["id"];
				$tag_entry_add = $db->prepare('INSERT OR IGNORE INTO entries_tags (entry_id, tag_id) VALUES (?, ?)');
				$tag_entry_add->bindParam(1, $id);
				$tag_entry_add->bindParam(2, $tag_id);
				$tag_entry_add->execute(); 
			}
			$inQuery = implode(',', array_fill(0, count($tags_array), '?'));
			var_dump($inQuery);
			$tag_entry_delete = $db->prepare('DELETE FROM entries_tags WHERE tag_id NOT IN (
													SELECT id FROM tags WHERE tag IN (' . $inQuery . '))');
			foreach ($tags_array as $k=> $tag_array) {
				$tag_entry_delete->bindParam($tag_array);
			}
			$tag_entry_delete->execute();											
			$_SESSION['status'] = "Entry updated!";
			header("Location: /detail.php?id=" . $entry_rows[0]["entry_id"]);
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
                <div class="edit-entry">
					<?php if (isset($alert)) { echo $alert; } 
					if ($entry_rows) {
						$id = $entry_rows[0]["entry_id"];
						if (isset($_POST["title"])) { 
							$temp_title = $_POST["title"];
						} else { 
							$temp_title = $entry_rows[0]["title"]; 
						}
						if (isset($_POST["date"])) { 
							$temp_date = $_POST["date"];
						} else { 
							$temp_date = $entry_rows[0]["date"]; 
						}
						if (isset($_POST["timeSpent"])) { 
							$temp_time = $_POST["timeSpent"];
						} else { 
							$temp_time = $entry_rows[0]["time_spent"]; 
						}  
						if (isset($_POST["whatILearned"])) { 
							$temp_learned = $_POST["whatILearned"]; 
						} else { 
							$temp_learned = $entry_rows[0]["learned"]; 
						}
						if (isset($_POST["ResourcesToRemember"])) { 
							$temp_resources = $_POST["ResourcesToRemember"];
						} else {
							$temp_resources = $entry_rows[0]["resources"]; 
						}
						if (isset($_POST["tags"])) { 
							$temp_tags = $_POST["tags"];
						} else {
							$temp_tags = "";
							foreach ($entry_rows as $row) {
								$temp_tags .= $row["tag"];
								$temp_tags .= " "; 
							}
						}
					echo <<< EOT
                    <h2>Edit Entry</h2>
					<form action="edit.php?id=$id" method="post">
						<input type="hidden" id="id" name="id" value="$id">
                        <label for="title"> Title</label>
                        <input id="title" type="text" name="title" value="{$temp_title}"><br>
                        <label for="date">Date</label>
                        <input id="date" type="date" name="date" value="{$temp_date}"><br>
                        <label for="time-spent"> Time Spent</label>
                        <input id="time-spent" type="text" name="timeSpent" value="{$temp_time}"><br>
                        <label for="what-i-learned">What I Learned</label>
                        <textarea id="what-i-learned" rows="5" name="whatILearned">{$temp_learned}</textarea>
                        <label for="resources-to-remember">Resources to Remember (optional)</label>
                        <textarea id="resources-to-remember" rows="5" name="ResourcesToRemember">{$temp_resources}</textarea>
                        <label for="tags">Tags (optional; separate tags with single space)</label>
						<input id="tags" type="text" name="tags" value="{$temp_tags}"><br>
						<input type="submit" value="Publish Entry" class="button">
                        <a href="index.php" class="button button-secondary">Cancel</a>
                    </form>
EOT;
					} else {
						echo('<h2 style="text-align: center;">Could not find that entry</h2>');
					}
					?>
                </div>
            </div>
        </section>
        <?php include "inc/footer.php"; ?>
    </body>
</html>