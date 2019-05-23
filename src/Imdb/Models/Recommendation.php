<?php

namespace Imdb\Models;

/**
 * Object for holding movie recommandation on the Title page.
 */
class Recommendation
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $image;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $year;

    /**
     * @var array of strings.
     */
    private $genres;

    /**
     * @var Imdb\Models\Rating
     */
    private $ratings;

    /**
     * @var string
     */
    private $plot;

    /**
     * @var string
     */
    private $directorName;

    /**
     * Name of the stars in the movie.
     * 
     * @var array of strings.
     */
    private $stars;

    /**
     * For example PG, 12, 12A, etc.
     * 
     * @var string
     */
    private $classification;

    public function __construct(int $id, string $image, string $title, int $year, array $genres, Rating $ratings, string $plot, string $directorName, array $stars, string $classification) {
        $this->id = $id;
        $this->image = $image;
        $this->title = $title;
        $this->year = $year;
        $this->genres = $genres;
        $this->ratings = $ratings;
        $this->plot = $plot;
        $this->directorName = $directorName;
        $this->stars = $stars;
        $this->classification = $classification;
    }

	/**
	 * @return int
	 */
	public function getID() : int
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getImage() : string
	{
		return $this->image;
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
	 * @return array of strings.
	 */
	public function getGenres() : array
	{
		return $this->genres;
	}

	/**
	 * @return Imdb\Models\Rating
	 */
	public function getRatings() : Rating
	{
		return $this->ratings;
	}

	/**
	 * @return string
	 */
	public function getPlot() : string
	{
		return $this->plot;
	}

	/**
	 * @return string
	 */
	public function getDirectorName() : string
	{
		return $this->directorName;
	}

	/**
	 * @return array of strings.
	 */
	public function getStars() : array
	{
		return $this->stars;
	}

	/**
	 * @return string For example PG, 12, 12A, etc.
	 */
	public function getClassification() : string
	{
		return $this->classification;
	}
}
