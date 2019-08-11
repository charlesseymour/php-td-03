<?php 

require_once("database.php"); 

if (isset($_GET["tag"])) {
	try {
		$tag = filter_input(INPUT_GET, 'tag', FILTER_SANITIZE_STRING);
		$tag1 = $tag . " %";
		$tag2 = "% " . $tag;
		$tag3 = "% " . $tag . " %";
		$results = $db->prepare('SELECT * FROM entries WHERE 
								tags LIKE ? OR
								tags LIKE ? OR
								tags LIKE ? OR 
								tags = ?
								ORDER BY date DESC');
		$results->bindParam(1, $tag1);
		$results->bindParam(2, $tag2);
		$results->bindParam(3, $tag3);
		$results->bindParam(4, $tag);
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
						echo('<h3 style="text-align: center; color: #678f89;">Entries tagged: "' . $_GET["tag"] . '"</h3><br>');
					}
					foreach($entries as $entry) {
						$fullDate = date('F j, Y', strtotime($entry['date']));
						echo <<<EOT
						<article>
							<h2><a href="detail.php?id={$entry['id']}">{$entry['title']}</a></h2>
							<time datetime="{$entry['date']}">{$fullDate}</time>
EOT;
						if (!empty($entry["tags"])) {
							$tags = explode(" ", $entry["tags"]);
							echo '<div class="tag-container"><span class="tag-label">tags:</span>';
							foreach($tags as $tag) {
								$tag = trim($tag);
								echo '<span class="tag"><a href="index.php?tag=' . $tag .
								'">' . $tag . '</a></span>';
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