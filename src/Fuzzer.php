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

    // 获取所有内置函数
    public static function get_all_internal_function(): array
    {
        $all_function_names = get_defined_functions();
        return  $all_function_names['internal'];
    }
    
    // 判断一个函数是否是单参数函数
    public static function is_one_param_function($function_name)
    {
        $funcinfo = Inspector::func($function_name,true);
        return count($funcinfo['required_params']) == 1 || (count($funcinfo['required_params']) == 0 && count($funcinfo['optional_params']) == 1);
    }

    // 获取所有单参数函数
    public static function get_all_one_param_internal_function(): array
    {
        $one_param_function_names = [];
        $function_names = self::get_all_internal_function();
        foreach ($function_names as $function_name) {
            if (self::is_one_param_function($function_name)) {
                $one_param_function_names[] = $function_name;
            }
        }
        return $one_param_function_names;
    }

    // fuzz所有内置函数
    public static function fuzz_all_internal_function(callable $callback)
    {
        $function_names = self::get_all_internal_function();
        foreach ($function_names as $function_name) {
            try {
                $reflect_func = new \ReflectionFunction($function_name);
                $callback($reflect_func);
            } catch (\Throwable $e) {
                printf("%s failed,err:%s", $function_name, $e);
            }
        }
    }
}
