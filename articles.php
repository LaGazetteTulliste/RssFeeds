<?php
// Get the articles from the article Controller
require_once '../app/controllers/ArticlesController.php';
$articlesController = new ArticlesController();
$articles = $articlesController->getAllArticles();

// Set the header to XML and set all the RSS feed properties
header("Content-type: text/xml");
?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
     xmlns:georss="http://www.georss.org/georss"
     xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
     version="2.0">
    <channel>
        <title>LaGazetteTulliste | RSS</title>
        <link>https://journal.elliotmoreau.fr/</link>
        <description>LaGazetteTulliste, le journal de votre ville.</description>
        <language>en-fr</language>
        <atom:link href="https://journal.elliotmoreau.fr/feed/" rel="self" type="application/rss+xml" />
        <image>
            <url>https://journal.elliotmoreau.fr/static/img/logo.png</url>
            <title>LaGazetteTulliste | RSS</title>
            <link>https://journal.elliotmoreau.fr/</link>
        </image>
        <?php
        // Set the timezone to Paris
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('Europe/Paris');
        }

        function format_content($content): false|string
        {
            $content = mb_convert_encoding($content, 'ISO-8859-1');
            // Create a DOMDocument and load the HTML, suppressing warnings
            $dom = new DOMDocument();
            @$dom->loadHTML($content);

            // Use XPath to locate all figure elements
            $xpath = new DOMXPath($dom);
            $figures = $xpath->query('//figure');

            // Loop through each figure element
            foreach ($figures as $figure) {
                // Extract the image URL and caption
                $figure->getAttribute('data-trix-attachment');
                $imageUrl = $figure->getElementsByTagName('img')->item(0)->getAttribute('src');
                $caption = $figure->getElementsByTagName('figcaption')->item(0)->nodeValue;

                // If the attributes are empty, provide default values
                if (empty($imageUrl)) {
                    $imageUrl = "Error";
                }

                if (empty($caption)) {
                    $caption = "Aucune description";
                }

                $caption = mb_convert_encoding($caption, 'ISO-8859-1');

                // Format the figure element
                $imageUrl = htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8');
                // Remove &quot; entities from the image URL
                $imageUrl = str_replace('&quot;', '', $imageUrl);
                $replacement = "<img src='$imageUrl' alt='$caption'>";

                // Create a new DOMDocument for the replacement
                $replacementDom = new DOMDocument();
                @$replacementDom->loadHTML($replacement);

                // Import the replacement node into the original DOM
                $importedNode = $dom->importNode($replacementDom->documentElement, true);

                // Replace the figure element with the formatted content
                $figure->parentNode->replaceChild($importedNode, $figure);
            }

            // Get the modified content as a string
            return $dom->saveHTML();
        }

        while ($article = $articles->fetch()) {
            $title = $article["title"];
            $link = "https://journal.elliotmoreau.fr/article/" . $article["id"];
            $img = "https://journal.elliotmoreau.fr/static/img/articles/" . $article["id"];
            $description = $article["description"];
            $category = $article["category"];
            $author = $article["author"];

            $content = format_content($article["content"]);
            $date = date("D, d M Y H:i:s O", strtotime($article["created_at"]));
            ?>
            <item>
                <title><?= htmlspecialchars($title) ?></title>
                <link><?= htmlspecialchars($link) ?></link>
                <guid><?= htmlspecialchars($link) ?></guid>
                <description><?= htmlspecialchars($description) ?></description>
                <pubDate><?= htmlspecialchars($date) ?></pubDate>
                <category><![CDATA[<?= htmlspecialchars($category) ?>]]></category>
                <media:content url="<?= htmlspecialchars($img) ?>" xmlns:media="http://search.yahoo.com/mrss/" type="image/png" medium="image" duration="10"></media:content>
                <content:encoded><![CDATA[<?= $content ?>]]></content:encoded>
                <dc:creator><![CDATA[<?= htmlspecialchars($author) ?>]]></dc:creator>
            </item>
            <?php
        }
        ?>
    </channel>
</rss>
