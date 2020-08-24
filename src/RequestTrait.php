<?php

namespace R\Psr7;

trait RequestTrait
{
    //-----
    public function HttpAccept()
    {

        $accepts = [];
        foreach ($this->getHeader("Accept") as $a) {
            $ss = explode(";", $a, 2);
            $accepts[] = ["media" => $ss[0], "params" => $ss[1]];
        }
        return $accepts;
    }

    public function ContentType()
    {
        $s = explode(";", $this->getHeader("Content-Type")[0]);
        return strtolower($s[0]);
    }

    public function isAccept(string $content_type)
    {
        foreach ($this->HttpAccept() as $accept) {
            if ($accept["media"] == $content_type) {
                return true;
            }
        }
        return false;
    }
}
