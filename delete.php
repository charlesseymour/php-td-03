<?php 

require_once("database.php"); 

if(!empty($_GET['id'])) {
	$entry_id = intval($_GET['id']);
}

/*$check_entry = $db->prepare('SELECT * FROM entries WHERE id = ?');
$check_entry->bindParam(1, $entry_id);
$check_entry->execute();*/

try {
	$results = $db->prepare('DELETE FROM entries WHERE id = ?');
	$results->bindParam(1, $entry_id);
	$results->execute();
	// get associated entries_tags rows and delete
	// (Tried setting the columns in entries_tags table 
	// to ON DELETE CASCADE but that didn't work-- any suggestions?)
	$entries_tags_delete = $db->prepare('DELETE FROM entries_tags WHERE entry_id = ?');
	$entries_tags_delete->bindParam(1, $entry_id);
	$entries_tags_delete->execute();
} catch(Exception $e) {
	echo $e->getMessage();
	die();
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
				<h1 style="color: green; text-align: center;">Entry deleted!</h1>
			</div>
		</section>
		<?php include "inc/footer.php"; ?>
	</body>
</html>