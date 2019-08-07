<?php 

require_once("database.php"); 

if(!empty($_GET['id'])) {
	$entry_id = intval($_GET['id']);
}

try {
	$results = $db->prepare('select * from entries where id = ?');
	$results->bindParam(1, $entry_id);
	$results->execute();
} catch(Exception $e) {
	echo $e->getMessage();
	die();
}

$entry = $results->fetch(PDO::FETCH_ASSOC);

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
                <div class="entry-list single">
                    <article>
                        <h1><?php echo $entry["title"]; ?></h1>
                        <time datetime="2016-01-31"><?php echo(date('F j, Y', strtotime($entry['date']))); ?></time>
                        <div class="entry">
                            <h3>Time Spent: </h3>
                            <p><?php echo($entry["id"]); ?></p>
                        </div>
                        <div class="entry">
                            <h3>What I Learned:</h3>
                            <p><?php echo($entry['learned']); ?></p>
                        </div>
                        <div class="entry">
                            <h3>Resources to Remember:</h3>
							<p><?php if (isset($entry['resources'])) { echo($entry['resources']); } ?></p>	
                        </div>
                    </article>
                </div>
            </div>
            <div class="edit">
                <p><a href="edit.html">Edit Entry</a></p>
            </div>
        </section>
        <?php include "inc/footer.php"; ?>
    </body>
</html>