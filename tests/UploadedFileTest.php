<?

declare(strict_types=1);
//error_reporting(E_ALL && ~E_WARNING && ~E_NOTICE);
use PHPUnit\Framework\TestCase;

use R\Psr7\Stream;
use R\Psr7\UploadedFile;

final class UploadedFileTest extends TestCase
{
    public function test_create()
    {
        $s = new Stream("test");
        $uf = new UploadedFile($s, 0, 0, "file", "jpg");
        $this->assertInstanceOf(UploadedFile::class, $uf);
    }
}
