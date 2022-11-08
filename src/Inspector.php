<?php

namespace Rickshang\Fuzzer;

class Inspector
{
    public static function func(string $function_name)
    {
        $reflect_func = new \ReflectionFunction($function_name);
        
        
        echo 'getName():' . $reflect_func->getName() . "\n";
        echo "getParameters():\n";
        $params = $reflect_func->getParameters();
        foreach ($params as $param) {
            $reflect_type =  $param->getType();
            print_r([
                "name:" => $param->getName(),
                'has_type' => $param->hasType(),
                "type" => $reflect_type ? $reflect_type->__toString() : null,
                "type allow null?" => $reflect_type ? $reflect_type->allowsNull() : null,
                "is_callable" => $param->isCallable(),
                "is_optional" => $param->isOptional(),
                "position" => $param->getPosition(),
                "declaring_function" => $param->getDeclaringFunction(),
                "__toString" => $param->__toString(),
            ]);
        }
    }

    public static function extension(string $extension_name,$return = false)
    {
        $re = new \ReflectionExtension($extension_name);
        defined('UNDEFINED') || define('UNDEFINED', '%undefined%');
        $_data = [];

        $_data['getName:'] = $re->getName() ?: UNDEFINED;
        $_data['getVersion:'] = $re->getVersion() ?: UNDEFINED;
        $_data['info:'] = $re->info() ?: UNDEFINED;
        $_data['getClassName:'] = PHP_EOL . implode(", ", $re->getClassNames()) ?: UNDEFINED;
        foreach ($re->getConstants() as $key => $value) {
            $_data['getConstants:'] .= "\n{$key}:={$value}";
        }
        $_data['getDependencies:'] = $re->getDependencies() ?: UNDEFINED;
        $_data['getFunctions:'] = PHP_EOL . implode(", ", array_keys($re->getFunctions())) ?: UNDEFINED;
        $_data['getINIEntries:'] = $re->getINIEntries() ?: UNDEFINED;
        $_data['isPersistent:'] = $re->isPersistent() ?: UNDEFINED;
        $_data['isTemporary:'] = $re->isTemporary() ?: UNDEFINED;

        return print_r($_data,$return);
    }

    public static function get_all_internal_function(): array
    {
        $all_function_names = get_defined_functions();
        return  $all_function_names['internal'];
    }

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
