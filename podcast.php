<?php

require_once '../app/controllers/PodcastController.php';
$podcastController = new PodcastController();
$episodes = $podcastController->get_episodes();

// Set the header to XML and set RSS feed properties
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<rss version="2.0"
     xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
     xmlns:podcast="https://podcastindex.org/namespace/1.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
     xmlns:georss="http://www.georss.org/georss"
     xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#">
    <channel>
        <title>LaGazetteTulliste | Podcast</title>
        <link>https://journal.elliotmoreau.fr/</link>
        <language>fr-fr</language>

        <atom:link href="https://journal.elliotmoreau.fr/feed/podcast/" rel="self" type="application/rss+xml" />

        <?php
        // Set the timezone to Paris
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('Europe/Paris');
        }
        ?>

        <itunes:owner>
            <itunes:email>contact@elliotmoreau.fr</itunes:email>
            <itunes:name>Elliot Moreau</itunes:name>
        </itunes:owner>

        <itunes:category text="News">
            <itunes:category text="Daily News" />
        </itunes:category>

        <itunes:category text="Arts">
            <itunes:category text="Books" />
            <itunes:category text="Food" />
            <itunes:category text="Performing Arts" />
        </itunes:category>

        <itunes:category text="Sports">
            <itunes:category text="Football" />
            <itunes:category text="Basketball" />
            <itunes:category text="Rugby" />
        </itunes:category>

        <itunes:image href="https://journal.elliotmoreau.fr/static/img/podcast.jpeg" />

        <description>Ce podcast est le podcast officiel de LaGazetteTulliste, suivez l'actu de Tulle et de la Corrèze à travers des podcasts courts et réguliers sur toutes les plateformes.</description>

        <itunes:summary>Ce podcast est le podcast officiel de LaGazetteTulliste, suivez l'actu de Tulle et de la Corrèze à travers des podcasts courts et réguliers sur toutes les plateformes.</itunes:summary>

        <itunes:author>Romain Gorse</itunes:author>

        <itunes:explicit>no</itunes:explicit>

        <copyright>LaGazetteTulliste</copyright>

        <itunes:subtitle>Ce podcast est le podcast officiel de LaGazetteTulliste, suivez l'actu de Tulle et de la Corrèze à travers des podcasts courts et réguliers sur toutes les plateformes.</itunes:subtitle>

        <?php
        while ($episode = $episodes->fetch()) {
            $title = $episode["title"];
            $id = $episode["id"];
            $link = "https://journal.elliotmoreau.fr/static/podcast/audios/{$id}.mp3";
            $date = date("D, d M Y H:i:s O", strtotime($episode["created_at"]));
            $duration = $episode["duration"];
            $author = $episode["author"];
            $number = $podcastController->getNumberOfPodcast($id);
            ?>

            <item>
                <itunes:title><?= htmlspecialchars($title) ?></itunes:title>
                <title><?= htmlspecialchars($title) ?></title>
                <enclosure url="https://journal.elliotmoreau.fr/static/podcast/audios/<?= $id ?>.mp3" type="audio/mpeg" length="34216300" />
                <link><?= htmlspecialchars($link) ?></link>
                <guid><?= htmlspecialchars($link) ?></guid>
                <pubDate><?= htmlspecialchars($date) ?></pubDate>
                <itunes:author><?= htmlspecialchars($author) ?></itunes:author>
                <itunes:subtitle><?= htmlspecialchars($title) ?></itunes:subtitle>
                <itunes:image href="https://journal.elliotmoreau.fr/static/img/podcast.jpeg" />
                <itunes:duration><?= htmlspecialchars($duration) ?></itunes:duration>
                <itunes:summary>Podcast n°<?= $number ?> : <?= htmlspecialchars($title) ?></itunes:summary>
                <description>Podcast n°<?= $number ?> : <?= htmlspecialchars($title) ?></description>
            </item>
        <?php } ?>

    </channel>
</rss>