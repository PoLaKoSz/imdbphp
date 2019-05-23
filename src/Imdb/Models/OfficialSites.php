<?php

namespace Imdb\Models;

class OfficialSites
{
    /**
     * @var null|string URL.
     */
    private $webSite;

    /**
     * @var null|string URL.
     */
    private $faceBook;

    public function __construct(?string $webSite, ?string $faceBook) {
        $this->webSite = $webSite;
        $this->faceBook = $faceBook;
    }

	/**
	 * @return null|string URL.
	 */
	public function WebSite() : ?string
	{
		return $this->webSite;
	}

	/**
	 * @return null|string URL.
	 */
	public function FaceBook() : ?string
	{
		return $this->faceBook;
	}
}
