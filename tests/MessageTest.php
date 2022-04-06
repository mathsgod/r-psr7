<?php

declare (strict_types = 1);
//error_reporting(E_ALL && ~E_WARNING);
use PHPUnit\Framework\TestCase;
use R\Psr7\Message;

final class MessageTest extends TestCase
{
    public function testCreate()
    {
        $r = new Message();
        $this->assertInstanceOf(Message::class, $r);


        $r = new Message(["a" => 1, "b" => 2]);
        $this->assertInstanceOf(Message::class, $r);



        $r = new Message(["a"=>"1,2,3"]);
        $this->assertInstanceOf(Message::class, $r);


    }

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function test_getProtocolVersion()
    {
        $r = new Message([], null, "1.1");
        $this->assertEquals($r->getProtocolVersion(), "1.1");
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     * @return static
     */
    public function test_withProtocolVersion()
    {
        $r = new Message([], null, "1.1");
        $r = $r->withProtocolVersion("1.0");
        $this->assertEquals($r->getProtocolVersion(), "1.0");

    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ': ' . implode(', ', $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers.
     *     Each key MUST be a header name, and each value MUST be an array of
     *     strings for that header.
     */
    public function test_getHeaders()
    {
        $r = new Message(["a" => "1", "b" => "2"]);
        $this->assertEquals($r->getHeaders(), ["a" => ["1"], "b" => ["2"]]);
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function test_hasHeader()
    {
        $r = new Message();

        $r = $r->withAddedHeader("a", 1);
        $r = $r->withAddedHeader("a", 2);
        $r = $r->withAddedHeader("A", 3);

        $this->assertTrue($r->hasHeader('a'));
        $this->assertFalse($r->hasHeader('b'));

    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function test_getHeader()
    {
        $r = new Message(["a" => "1", "b" => "2"]);
        $this->assertEquals($r->getHeader("a"), ["1"]);
        $this->assertEquals($r->getHeader("b"), ["2"]);
        $this->assertEquals($r->getHeader("A"), ["1"]);
        $this->assertEquals($r->getHeader("B"), ["2"]);


        $r = new Message(["a" => "1", "A" => "2"]);
        $this->assertEquals($r->getHeader("a"), ["2"]);
    }


    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function test_getHeaderLine()
    {
        $r = new Message();

        $r = $r->withAddedHeader("a", 1);
        $r = $r->withAddedHeader("a", 2);
        $r = $r->withAddedHeader("A", 3);

        $this->assertEquals($r->getHeaderLine('a'), "1,2,3");
        $this->assertEquals($r->getHeaderLine('A'), "1,2,3");
        $this->assertEquals($r->getHeaderLine('b'), "");
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */

    public function test_withHeaders()
    {
        $r = new Message();

        $r = $r->withHeader("a", 1);
        $r = $r->withHeader("a", 2);
        $r = $r->withHeader("A", 3);

        $this->assertEquals($r->getHeaders(), ["A" => [3]]);
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names.
     * @throws \InvalidArgumentException for invalid header values.
     */
    public function test_withAddedHeader()
    {
        $r = new Message();

        $r = $r->withAddedHeader("a", "1");
        $r = $r->withAddedHeader("a", "2");
        $r = $r->withAddedHeader("A", "3");

        $this->assertEquals($r->getHeaders(), ["a" => ["1", "2","3"]]);

        $this->assertEquals($r->getHeader('a'), ["1", "2", "3"]);
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return static
     */
    public function test_withoutHeader()
    {
        $r = new Message(["a" => 1, "b" => 2]);

        $r = $r->withoutHeader("a", 1);

        $this->assertEquals($r->getHeaders(), ["b" => [2]]);

    }





}