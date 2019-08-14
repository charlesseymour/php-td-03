<?php 

session_start();

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
			<?php if(isset($_SESSION['status'])) {
				echo '<h1 style="color: green; text-align: center;">' . $_SESSION['status'] . '</h1>';
				unset($_SESSION['status']);
			} 
			
			if ($entry) {
				$date = date('F j, Y', strtotime($entry["date"]));
				if (isset($entry["resources"])) { 
					$resources = $entry["resources"]; }
				try {
					$tag_search = $db->query('SELECT tag FROM tags JOIN entries_tags ON tags.id = entries_tags.tag_id
																   JOIN entries ON entries_tags.entry_id = entries.id
																   WHERE entries.id = ' . $entry["id"]);
					$tag_search->execute();
					$tags = $tag_search->fetchAll(PDO::FETCH_ASSOC);
				} catch (Exception $e) {
					echo $e->getMessage();
					die();
				}
				echo <<<EOT
                <div class="entry-list single">
                    <article>
                        <h1>$entry[title]</h1>
                        <time datetime="2016-01-31">{$date}</time>
EOT;
						
						if (!empty($tags)) {
							echo '<div class="tag-container"><span class="tag-label">tags:</span>';
							foreach($tags as $tag) {
								echo '<span class="tag"><a href="index.php?tag=' . $tag["tag"] .
								'">' . $tag["tag"] . '</a></span>';
							}
							echo('</div>');
						}
						
				echo <<<EOT
                        <div class="entry">
                            <h3>Time Spent: </h3>
                            <p>$entry[time_spent]</p>
                        </div>
                        <div class="entry">
                            <h3>What I Learned:</h3>
                            <p>$entry[learned]</p>
                        </div>
                        <div class="entry">
                            <h3>Resources to Remember:</h3>
							<p>$entry[resources]</p>	
                        </div>
                    </article>
                </div>
			</div>
			<div class="edit">
				<p><a href="edit.php?id=$entry[id]">Edit Entry</a></p>
				<p><a href="delete.php?id=$entry[id]" onclick="return confirm('Are you sure?')">Delete Entry</a></p>
			</div>
EOT;
			} else {
				echo('<h2 style="text-align: center">Could not find that entry</h1><br>');
			}
				?>
            
        </section>
        <?php include "inc/footer.php"; ?>
    </body>
</html>