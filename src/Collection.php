<?
namespace R\Psr7;

class Collection
{
    protected $data = [];

    public function all()
    {
        return $this->data;
    }

    public function clear()
    {
        $this->data = [];
    }

    public function add($key, $value)
    {
        $this->data[$key][] = $value;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function get($key)
    {
        return $this->data[$key];
    }

    public function remove($key)
    {
        unset($this->data[$key]);
    }


}