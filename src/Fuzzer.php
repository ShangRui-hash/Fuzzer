<?php

namespace Rickshang\Fuzzer;

class Fuzzer
{
    public $payloads = [];


    public function load_payloads_from_file(string $filename)
    {
        $this->payloads = explode("\n", file_get_contents($filename));
    }

    public static function get_all_unicode_chars(): array
    {
        ini_set('memory_limit', '1024M');
        $chars = [];
        for ($i = 0; $i <= 0x10ffff; $i++) {
            $chars[] = mb_chr($i, 'UTF-8');
        }
        return $chars;
    }


    public static function get_all_ascii_chars(): array
    {
        $chars = [];
        for ($i = 0; $i <= 255; $i++) {
            $chars[] = chr($i);
        }
        return $chars;
    }

  

    public function run($callback)
    {
        foreach ($this->payloads as $payload) {
            $callback($payload);
        }
    }




}
