# Fuzzer
## description
a php fuzzer class for security research
## Install
```
composer require rickshang/fuzzer
```
## Demo 

```php
require __DIR__ . '/vendor/autoload.php';
use RickShang/Fuzzer/Fuzzer;

$fuzzer = new Fuzzer();
$fuzzer->load_payloads_from_file("./test.txt");
$fuzzer->run(function($payload){
    print_r([
        'payload'=>$payload,
        'basename'=>basename($payload)
    ]);
});
```