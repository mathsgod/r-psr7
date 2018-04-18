<?
declare (strict_types = 1);
error_reporting(E_ALL && ~E_WARNING);
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testCreate(){
        $this->assertEquals("","");
    }


}