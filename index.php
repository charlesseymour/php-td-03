<?php 

require_once("database.php"); 

if (isset($_GET["tag"])) {
	try {
		$results = $db->prepare('select entries.id, title, date from entries
							    join entries_tags on entries.id = entries_tags.entry_id
							    join tags on entries_tags.tag_id = tags.id
							    where tag = ?');
		$results->bindParam(1, $_GET["tag"]);
		$results->execute();
	} catch(Exception $e) {
		echo $e->getMessage();
		die();
	}
} else {
	try {
		$results = $db->query('select * from entries order by date desc');
	} catch(Exception $e) {
		echo $e->getMessage();
		die();
	}
}

$entries = $results->fetchAll(PDO::FETCH_ASSOC);

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
                <div class="entry-list">
					<?php 
					if (isset($_GET["tag"])) {
						echo '<h3 style="text-align: center">Entries tagged "' . $_GET["tag"] . '"</h3><br>';
					}
					foreach($entries as $entry) {
						$fullDate = date('F j, Y', strtotime($entry['date']));
						try {
							$results = $db->prepare('select tag, tags.id from entries
												   join entries_tags on entries.id = entries_tags.entry_id
												   join tags on entries_tags.tag_id = tags.id
												   where entries.id = ?');
							$results->bindParam(1, $entry['id']);
							$results->execute();
						} catch(Exception $e) {
							echo $e->getMessage();
							die();
						}
						$tags = $results->fetchAll(PDO::FETCH_ASSOC);
						echo <<<EOT
						<article>
							<h2><a href="detail.php?id={$entry['id']}">{$entry['title']}</a></h2>
							<time datetime="{$entry['date']}">{$fullDate}</time>
EOT;
						if (!empty($tags)) {
							echo '<div class="tag-container"><span class="tag-label">tags:</span>';
							foreach($tags as $tag) {
								echo '<span class="tag"><a href="index.php?tag=' . $tag["tag"] .
								'">' . $tag["tag"] . '</a></span>';
							}
							echo('</div>');
						}
						
						echo '</article>';
					}
					?>
                </div>
            </div>
        </section>
        <?php include "inc/footer.php"; ?>
    </body>
</html>