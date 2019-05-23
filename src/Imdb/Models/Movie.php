<?php

namespace Imdb\Models;

use Imdb\Models\Details;
use Imdb\Models\Rating;
use Imdb\Models\TechnicalSpecs;

class Movie
{
    /**
     * Name of the movie.
     * 
     * @var string
     */

    private $title;

    /**
     * @var int
     */
    private $year;

    /**
     * For example: PG, 12, 12A, etc.
     * 
     * @var string
     */
    private $certificate;

    /**
     * @var string
     */
    private $originalTitle;

    /**
     * @var Imdb\Models\Rating
     */
    private $ratings;

    /**
     * @var array of Imdb\Models\Recommendation.
     */
    private $recommendations;

    /**
     * @var array of strings.
     */
    private $keywords;

    /**
     * @var Imdb\Models\Details
     */
    private $details;

    /**
     * @var Imdb\Models\BoxOffice
     */
    private $boxOffice;

    /**
     * @var array of Imdb\Models\Company
     */
    private $productionCompanies;

    /**
     * @var Imdb\Models\TechnicalSpecs
     */
    private $technicalSpecs;

    /**
     * @var string
     */
    private $trivia;

    /**
     * @var string
     */
    private $goof;

    /**
     * @var array of Imdb\Models\Quote.
     */
    private $quotes;

    /**
     * @var Imdb\Models\Review
     */
    private $review;

    public function __construct($title, int $year, string $certificate, string $originalTitle, Rating $ratings, array $recommendations, array $keywords, Details $details) {
        $this->title = $title;
        $this->year = $year;
        $this->certificate = $certificate;
        $this->originalTitle = $originalTitle;
        $this->ratings = $ratings;
        $this->recommendations = $recommendations;
        $this->keywords = $keywords;
        $this->details = $details;
    }

    /**
     * @return string
     */ 
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getYear() : int
    {
        return $this->year;
    }

    /**
     * @return string
     */
    public function getCertificate() : string
    {
        return $this->certificate;
    }

    /**
     * @return string
     */
    public function getOriginalTitle() : string
    {
        return $this->originalTitle;
    }

    /**
     * @return Imdb\Models\Rating
     */
    public function getRatings() : Rating
    {
        return $this->ratings;
    }

    /**
     * @return array of Imdb\Models\Recommendation.
     */
    public function getRecommendations() : array
    {
        return $this->recommendations;
    }

	/**
	 * @return array of strings.
	 */
	public function getKeywords() : array
	{
		return $this->keywords;
	}

	/**
	 * @return Imdb\Models\Details.
	 */
	public function getDetails() : Details
	{
		return $this->details;
	}
}
