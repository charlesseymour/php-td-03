<?php 

require_once("database.php"); 

try {
	$results = $db->query('select * from entries order by date desc');
} catch(Exception $e) {
	echo $e->getMessage();
	die();
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
					
					foreach($entries as $entry) {
						$fullDate = date('F j, Y', strtotime($entry['date']));
						echo <<<EOT
						<article>
							<h2><a href="detail.php?id={$entry['id']}">{$entry['title']}</a></h2>
							<time datetime="{$entry['date']}">{$fullDate}</time>
						</article>
EOT;
					}
					?>
                </div>
            </div>
        </section>
        <?php include "inc/footer.php"; ?>
    </body>
</html>