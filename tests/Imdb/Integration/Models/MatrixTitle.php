<?php

namespace Imdb\Tests\Integration\Models;

use Imdb\Models\Details;
use Imdb\Models\Movie;
use Imdb\Models\OfficialSites;
use Imdb\Models\Rating;

class MatrixTitle extends Movie
{
    public function __construct()
    {
        $title = 'The Matrix';
        $year = 1999;
        $certificate = 'R';
        $originalTitle = 'The Matrix';
        $ratings = new Rating(1500375, 8.7);
        $recommendations = array();
        $keywords = [
            'simulated reality',
            'artificial reality',
            'war with machines',
            'post apocalypse',
            'questioning reality',
        ];

        $webSite = null;
        $faceBook = '';
        $officialSite = new OfficialSites($webSite, $faceBook);
        $countries = [
            'USA'
        ];
        $languages = [
            'English'
        ];
        $releaseDate = [
            10,
            'USA'
        ];
        $alsoKnownAs = 'The Matrix';
        $filmingLocations = [
            'AON Tower',
            'Kent Street',
            'Sydney',
            'New South Wales',
            'Australia',
        ];
        $details = new Details($officialSite, $countries, $languages, $releaseDate, $alsoKnownAs, $filmingLocations);
        parent::__construct($title, $year, $certificate, $originalTitle, $ratings, $recommendations, $keywords, $details);
    }
}
