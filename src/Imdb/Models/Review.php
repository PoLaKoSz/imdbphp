<?php

namespace Imdb\Models;

class Comment
{
    /**
     * @var int Between -1 and 10.
     */
    private $rating;

    /**
     * @var Imdb\Models\ReviewAuthor
     */
    private $author;

    /**
     * @var int Unix timestamp;
     */
    private $date;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string Text of the review.
     */
    private $content;

    /**
     * @var array with two element (x and y : x out of y found this helpful.)
     */
    private $helpfulRate;
}
