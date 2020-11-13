<?php

use Imdb\Image;

class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testInitializingObjectThroughConstructorIsDisabled()
    {
        $this->expectException(\Error::class);

        new Image();
    }

    public function testFromIdPopulateId()
    {
        $image = Image::fromID('MV5BMjdkZmZmMDItODgxNC00MmE4LTlmMDItYzkzNTNhZjcwMGYzXkEyXkFqcGdeQXVyMDc2NTEzMw');

        $this->assertEquals('MV5BMjdkZmZmMDItODgxNC00MmE4LTlmMDItYzkzNTNhZjcwMGYzXkEyXkFqcGdeQXVyMDc2NTEzMw', $image->ID());
    }

    public function testFromIdKeepOriginalUrlNull()
    {
        $image = Image::fromID('MV5BMjdkZmZmMDItODgxNC00MmE4LTlmMDItYzkzNTNhZjcwMGYzXkEyXkFqcGdeQXVyMDc2NTEzMw');

        $this->assertNull($image->originalURL());
    }

    public function testFromUrlThrowsParseErrorWhenUrlIsInvalid()
    {
        $this->expectException(\ParseError::class);
        $this->expectExceptionMessage('Image has an invalid format [https://github.com/tboothman/imdbphp]');

        Image::fromURL('https://github.com/tboothman/imdbphp');
    }

    public function testFromUrlParseImageWith()
    {
        $image = Image::fromUrl('https://m.media-amazon.com/images/M/MV5BYzQzZmQ0MzAtYmI4NS00YWQ2LTgwOTEtYjA2YjI2ZmEwMTNjXkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_.jpg');

        $this->assertEquals('MV5BYzQzZmQ0MzAtYmI4NS00YWQ2LTgwOTEtYjA2YjI2ZmEwMTNjXkEyXkFqcGdeQXVyMTkxNjUyNQ', $image->ID());
    }

    public function testFromUrlParseImageIdCorrectlyWhenUrlContainsOneAtChar()
    {
        $image = Image::fromURL('https://m.media-amazon.com/images/M/MV5BNGEyOGJiNWEtMTgwMi00ODU4LTlkMjItZWI4NjFmMzgxZGY2XkEyXkFqcGdeQXVyNjcyNjcyMzQ@._V1_UX182_CR0,0,182,268_AL_.jpg');

        $this->assertEquals('MV5BNGEyOGJiNWEtMTgwMi00ODU4LTlkMjItZWI4NjFmMzgxZGY2XkEyXkFqcGdeQXVyNjcyNjcyMzQ', $image->ID());
    }

    public function testFromUrlParseImageIdCorrectlyWhenUrlContainsTwoAtChar()
    {
        $image = Image::fromURL('https://m.media-amazon.com/images/M/MV5BMjdkZmZmMDItODgxNC00MmE4LTlmMDItYzkzNTNhZjcwMGYzXkEyXkFqcGdeQXVyMDc2NTEzMw@@._V1_.jpg');

        $this->assertEquals('MV5BMjdkZmZmMDItODgxNC00MmE4LTlmMDItYzkzNTNhZjcwMGYzXkEyXkFqcGdeQXVyMDc2NTEzMw', $image->ID());
    }

    public function testFromUrlPopulateOriginalUrl()
    {
        $image = Image::fromURL('https://m.media-amazon.com/images/M/MV5BMjdkZmZmMDItODgxNC00MmE4LTlmMDItYzkzNTNhZjcwMGYzXkEyXkFqcGdeQXVyMDc2NTEzMw@@._V1_.jpg');

        $this->assertEquals('https://m.media-amazon.com/images/M/MV5BMjdkZmZmMDItODgxNC00MmE4LTlmMDItYzkzNTNhZjcwMGYzXkEyXkFqcGdeQXVyMDc2NTEzMw@@._V1_.jpg', $image->originalURL());
    }

    public function testWithWidth()
    {
        $id = 'MV5BNGEyOGJiNWEtMTgwMi00ODU4LTlkMjItZWI4NjFmMzgxZGY2XkEyXkFqcGdeQXVyNjcyNjcyMzQ';
        $image = Image::fromID($id);

        $this->assertEquals('https://m.media-amazon.com/images/M/MV5BNGEyOGJiNWEtMTgwMi00ODU4LTlkMjItZWI4NjFmMzgxZGY2XkEyXkFqcGdeQXVyNjcyNjcyMzQ@._V1_FMjpg_UX100.jpg', $image->withWidth(100));
    }

    public function testWithHeight()
    {
        $id = 'MV5BNGEyOGJiNWEtMTgwMi00ODU4LTlkMjItZWI4NjFmMzgxZGY2XkEyXkFqcGdeQXVyNjcyNjcyMzQ';
        $image = Image::fromID($id);

        $this->assertEquals('https://m.media-amazon.com/images/M/MV5BNGEyOGJiNWEtMTgwMi00ODU4LTlkMjItZWI4NjFmMzgxZGY2XkEyXkFqcGdeQXVyNjcyNjcyMzQ@._V1_FMjpg_UY999.jpg', $image->withHeight(999));
    }

    public function testAsExact()
    {
        $image = Image::fromID('MV5BZWViOWJmNTctNjBjMC00ODA1LWIxZjItZTQxNGZiMDIxZTIwXkEyXkFqcGdeQXVyMTkxNjUyNQ');

        $this->assertEquals('https://m.media-amazon.com/images/M/MV5BZWViOWJmNTctNjBjMC00ODA1LWIxZjItZTQxNGZiMDIxZTIwXkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_UX100_CR0,0,100,150_AL_.jpg', $image->asExact(100, 150));
    }

    public function testAsFullSized()
    {
        $image = Image::fromID('MV5BZWViOWJmNTctNjBjMC00ODA1LWIxZjItZTQxNGZiMDIxZTIwXkEyXkFqcGdeQXVyMTkxNjUyNQ');

        $this->assertEquals('https://m.media-amazon.com/images/M/MV5BZWViOWJmNTctNjBjMC00ODA1LWIxZjItZTQxNGZiMDIxZTIwXkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_.jpg', $image->asFullSized());
    }

    public function testAsThumbnail()
    {
        $image = Image::fromID('MV5BNGEyOGJiNWEtMTgwMi00ODU4LTlkMjItZWI4NjFmMzgxZGY2XkEyXkFqcGdeQXVyNjcyNjcyMzQ');

        $this->assertEquals('https://m.media-amazon.com/images/M/MV5BNGEyOGJiNWEtMTgwMi00ODU4LTlkMjItZWI4NjFmMzgxZGY2XkEyXkFqcGdeQXVyNjcyNjcyMzQ@._V1_UX182_CR0,0,182,268_AL_.jpg', $image->asThumbnail());
    }
}
