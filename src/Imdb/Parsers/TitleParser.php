<?php

namespace Imdb\Parsers;

use Imdb\Models\Details;
use Imdb\Models\Movie;
use Imdb\Models\OfficialSites;
use Imdb\Models\Rating;
use Imdb\Models\Recommendation;

class TitleParser
{
    public function parse(string $html) : Movie
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $xPath = new \DOMXPath($doc);
        $scriptTags = $xPath->query("//script[@type='application/ld+json']");

        if ($scriptTags->length != 1)
        {
            throw new \Exception('Could not parse title: Missing jSON data!');
        }

        $jsonData = json_decode($scriptTags->item(0)->textContent);

        $title = $this->getTitle($doc);
        $year = $this->getYear($doc);
        $certificate = $this->getCertificate($jsonData);
        $originalTitle = $this->getOriginalTitle($xPath, $title);
        $ratings = $this->getRatings($jsonData);
        $recommendations = $this->getRecommendations($xPath);
        $keywords = $this->getKeywords($xPath);
        $details = $this->getDetails($xPath);
        //$genres = $this->getGenres($json);
        return new Movie($title, $year, $certificate, $originalTitle, $ratings, $recommendations, $keywords, $details);
    }

    protected function getTitle(\DOMDocument $doc) : string
    {
        return $doc->getElementById('star-rating-widget')->getAttribute('data-title');
    }

    protected function getYear(\DOMDocument $doc) : int
    {
        return $doc->getElementById('titleYear')->childNodes[1]->textContent;
    }

    protected function getCertificate(object $json) : string
    {
        return $json->contentRating;
    }

    protected function getOriginalTitle(\DOMXPath $xPath, string $title) : string
    {
        $originalTitle = @$xPath->query('//div[@class=originalTitle]')->item(0)->textContent;

        if (!isset($originalTitle));
        {
            return $title;
        }

        return $originalTitle;
    }

    protected function getRatings(object $json) : Rating
    {
        $count = $json->aggregateRating->ratingCount;
        $current = $json->aggregateRating->ratingValue;
        return new Rating($count, $current);
    }

    protected function getRecommendations(\DOMXPath $xPath) : array
    {
        $items = array();

        $movies = $xPath->query('//div[@id="title_recs"]//div[@class="rec_overviews"]//div[@class="rec_overview"]');

        for ($i=0; $i < $movies->count(); $i++)
        {
            $items[] = $this->getRecommendation($movies->item($i), $xPath);
        }
        
        return $items;
    }

    protected function getRecommendation(\DOMNode $movieNode, \DOMXPath $xPath) : Recommendation
    {
        $id = substr($movieNode->getAttribute('data-tconst'), 2);

        $imageNode = $xPath->query('.//div[@class="rec_poster"]//a//img', $movieNode)->item(0);        
        $image = $imageNode->getAttribute('src');
        $title = $imageNode->getAttribute('title');

        $upperJaw = $xPath->query('.//div[@class="rec_details"]//div[@class="rec-info"]//div[@class="rec-jaw-upper"]', $movieNode)->item(0);
        $lowerJaw = $xPath->query('.//div[@class="rec_details"]//div[@class="rec-info"]//div[@class="rec-jaw-lower"]', $movieNode)->item(0);

        $year = substr($xPath->query('.//div[@class="rec-title"]//span[@class="nobr"]', $upperJaw)->item(0)->textContent, 1, 4);

        $certGenreNode = $xPath->query('.//div[@class="rec-cert-genre rec-elipsis"]', $upperJaw)->item(0);
        $genreText = $certGenreNode->textContent;
        $genreText = trim(preg_replace('/\s+/', ' ', $genreText));
        $genres = preg_split('/ \| /', $genreText);

        $ratingsNode = $xPath->query('.//div[@class="rec-rating"]//div[@class="rating rating-list"]', $upperJaw)->item(0);
        @preg_match('/Users rated this (?<current>\b\d[\d.]*\b)\/10 \((?<count>\b\d[\d,.]*\b) votes\) - click stars to rate/', $ratingsNode->getAttribute('title'), $matches);
        $ratings = new Rating(str_replace(',', '', $matches['count']), (float)$matches['current']);

        $plotNode = $xPath->query('.//div[@class="rec-rating"]//div[@class="rec-outline"]//p', $upperJaw)->item(0);
        $plot = trim($plotNode->textContent);

        $directorNode = $xPath->query('.//div[@class="rec-director rec-ellipsis"]', $lowerJaw)->item(0)->lastChild;
        $directorName = trim($directorNode->textContent);

        $starsNode = $xPath->query('.//div[@class="rec-actor rec-ellipsis"]//span', $lowerJaw)->item(0)->lastChild;
        $starsText = trim(preg_replace('/\s+/', ' ', $starsNode->textContent));
        $stars = preg_split('/, /', $starsText);

        $certificateText = $xPath->query('.//span', $certGenreNode)->item(0)->getAttribute('class');
        $classification = $this->clean($certificateText);

        return new Recommendation($id, $image, $title, $year, $genres, $ratings, $plot, $directorName, $stars, $classification);
    }

    protected function clean(string $certificateText) : string
    {
        // us_{certificate} titlePageSprite absmiddle
        // gb_{certificate} titlePageSprite absmiddle
        $certificate = '';
        for ($i=3; $i < strlen($certificateText); $i++)
        {
            $char = $certificateText[$i];
            if ($char == ' ')
            {
                return $certificate;
            }

            if ($char == '_')
            {
                $char = ' ';
            }

            $certificate .= @strtoupper($char);
        }
    }

    protected function getKeywords(\DOMXPath $xPath) : array
    {
        $keywords = array();

        $container = $xPath->query('//div[@id="titleStoryLine"]//div[@class="see-more inline canwrap"]//a//span[@class="itemprop"]');       
        for ($i=0; $i < $container->count(); $i++)
        {
            $keywords[] = $container->item($i)->textContent;
        }

        return $keywords;
    }

    protected function getDetails(\DOMXPath $xPath) : Details
    {
        $rows = $xPath->query('//div[@id="titleDetails"]//div[@class="txt-block"]');

        $officialSites; $countries; $languages; $releaseDate; $alsoKnownAs; $filmingLocations;

        for ($i=0; $i < $rows->count(); $i++)
        {
            $row = $rows->item($i);
            $rowID = $xPath->query('.//h4[@class="inline"]', $row)->item(0)->textContent;

            switch ($rowID)
            {
                case 'Official Sites:': $officialSites = $this->parseOfficialSite($row, $xPath); break;
                default: throw new \Exception(substr($rowID, 0, strlen($rowID) - 1) . " row missing the parsing method in the Details section!"); break;
            }

        }

        return new Details($officialSites, $countries, $languages, $releaseDate, $alsoKnownAs, $filmingLocations);
    }

    protected function parseOfficialSite(\DOMNode $node, \DOMXPath $xPath) : array
    {
        $links = $xPath->query('.//a', $node)->item(0);
        $sites = array();

        foreach ($links as $link)
        {
            
        }

        return $sites;
    }
}
