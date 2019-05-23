<?php

namespace Imdb\Models;

use Imdb\Models\OfficialSites;

class Details
{
    /**
     * @var null|Imdb\Models\OfficialSites.
     */
    private $officialSites;

    /**
     * @var array of strings.
     */
    private $countries;

    /**
     * @var array of Imdb\Models\Language.
     */
    private $languages;

    /**
     * @var array of strings. 0 => Unix timestamp, 1 => string.
     */
    private $releaseDate;

    /**
     * @var string
     */
    private $alsoKnownAs;

    /**
     * @var array of strings.
     */
    private $filmingLocations;

    public function __construct(?OfficialSites $officialSites, array $countries, array $languages, array $releaseDate, string $alsoKnownAs, array $filmingLocations) {
        $this->officialSites = $officialSites;
        $this->countries = $countries;
        $this->languages = $languages;
        $this->releaseDate = $releaseDate;
        $this->alsoKnownAs = $alsoKnownAs;
        $this->filmingLocations = $filmingLocations;
    }

	/**
	 * @return null|Imdb\Models\OfficialSites.
	 */
	public function getOfficial() : ?OfficialSites
	{
		return $this->officialSites;
	}

	/**
	 * @return array of strings.
	 */
	public function getCountries() : array
	{
		return $this->countries;
	}

	/**
	 * @return array of Imdb\Models\Language.
	 */
	public function getLanguages() : array
	{
		return $this->languages;
	}

	/**
	 * @return array of strings. 0 => Unix timestamp (int), 1 => country (string).
	 */
	public function getReleaseDate() : array
	{
		return $this->releaseDate;
	}

	/**
	 * @return string
	 */
	public function getAlsoKnownAs() : string
	{
		return $this->alsoKnownAs;
	}

	/**
	 * @return array of strings.
	 */
	public function getfilmingLocations() : array
	{
		return $this->filmingLocations;
	}
}
