<?php

namespace Rickshang\Fuzzer;

class Inspector
{

    public static function char(string $char):string{
        return sprintf("hex: %s char: %s", bin2hex($char), $char);
    }

    public static function func(string $function_name, $return = false): array
    {
        $reflect_func = new \ReflectionFunction($function_name);
        $required_params = [];
        $optional_params = [];
        $ret = [
            'function_name' => $reflect_func->getName(),
        ];

        $params = $reflect_func->getParameters();
        foreach ($params as $param) {
            $reflect_type =  $param->getType();
            $is_optional = $param->isOptional();
            if ($is_optional) {
                $optional_params[] = $param->getName();
            } else {
                $required_params[] = $param->getName();
            }
            $ret['params'][$param->getName()] = [
                'has_type' => $param->hasType(),
                "type" => [
                    'toString' => $reflect_type ? $reflect_type->__toString() : null,
                    'allow_null' => $reflect_type ? $reflect_type->allowsNull() : null,
                ],
                "is_callable" => $param->isCallable(),
                "is_optional" => $is_optional,
                "position" => $param->getPosition(),
                "__toString" => $param->__toString(),
            ];
        }
        $ret['optional_params'] = $optional_params;
        $ret['required_params'] = $required_params;
        if ($return) {
            return $ret;
        }
        print_r($ret, $return);
    }



    public static function extension(string $extension_name, $return = false): array
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
        if ($return) {
            return $_data;
        }
        print_r($_data, $return);
    }
}
