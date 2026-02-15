<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PHPyh\CodingStandard\PhpCsFixerCodingStandard;

$finder = (new Finder())
    ->in([
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/src-dev',
        __DIR__ . '/tests',
    ])
    ->append([
        __FILE__,
        __DIR__ . '/composer-unused.php',
        __DIR__ . '/rector.php',
    ])
    ->exclude('var')
    ->notPath('reference.php')
;

$config = (new Config())
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache')
    ->setFinder($finder)
;

(new PhpCsFixerCodingStandard())->applyTo($config, [
    '@PHPUnit10x0Migration:risky' => true,
    'method_chaining_indentation' => true,
    'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
    'php_unit_data_provider_method_order' => false,
    'php_unit_data_provider_name' => false,
    'php_unit_data_provider_return_type' => false,
    'single_line_empty_body' => false,
]);

return $config;
