<?php
declare(strict_types=1);

$config = [
    // Base dir for generated tests
    "test_dir" => __DIR__ . "/../tests",
    // Base namespacese for tests
    "base_test_qcn" => "EMA\\Tests",
    // Base dir for generated sources
    "src_dir" => __DIR__ . "/../src",
    // 3 layer each with own namespace and subdirectory
    "layers" => [
        "app" => [
            "base_qcn" => "EMA\\App",
            "dir" => "App",
        ],
        "domain" => [
            "base_qcn" => "EMA\\Domain",
            "dir" => "Domain",
        ],
        "infrastructure" => [
            "base_qcn" => "EMA\\Infrastructure",
            "dir" => "Infrastructure",
        ],
    ],
    // config for individual things
    "primitives" => [
        // each thing has unique key
        "command" => [
            // each layer must have a config, otherwise it won't let generation happen
            "src" => [
                "stubs" => [
                    "/*<PSR4_NAMESPACE_LAST>*/" => __DIR__ . "/Stubs/Command/Command.stub.php",
                    "/*<PSR4_NAMESPACE_LAST>*/Handler" => __DIR__ . "/Stubs/Command/CommandHandler.stub.php",
                    "/*<PSR4_NAMESPACE_LAST>*/Authorizer" => __DIR__ . "/Stubs/Command/CommandAuthorization.stub.php",
                    // final file name => stub file
                ], // full paths to stubs
            ],
            "test" => [
                "stubs" => [
                    "/*<PSR4_NAMESPACE_LAST>*/Test" => __DIR__ . "/Stubs/Simple/SimpleTest.stub.php",
                    "/*<PSR4_NAMESPACE_LAST>*/HandlerTest" => __DIR__ . "/Stubs/Simple/SimpleTest.stub.php",
                    "/*<PSR4_NAMESPACE_LAST>*/AuthorizerTest" => __DIR__ . "/Stubs/Simple/SimpleTest.stub.php",
                    // final file name => stub file
                ], // full paths to stubs
            ],
        
        ],
        // ... any other primitive
    ],
    
];

return $config;