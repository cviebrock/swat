<?php

declare(strict_types=1);

/**
 * This script takes the JUnit-formatted output from PHPUnit tests,
 * and converts it to the GitHub annotations format, so that test
 * results show up nicely in GH Actions' output for PRs.
 */
$file = $argv[1] ?? 'build/junit.xml';
if (!file_exists($file)) {
    exit(0);
}

$xml = simplexml_load_file($file);
if ($xml === false) {
    exit(0);
}

foreach ($xml->xpath('//testcase') as $case) {
    foreach ($case->xpath('failure|error') as $problem) {
        $src_file = str_replace(getcwd() . '/', '', (string) $case['file']);
        $line = (string) ($case['line'] ?: 1);
        $msg = str_replace(
            ['%', "\r", "\n"],
            ['%25', '%0D', '%0A'],
            trim((string) $problem)
        );
        printf(
            "::error file=%s,line=%s,title=%s::%s\n",
            $src_file,
            $line,
            $case['name'],
            $msg
        );
    }
}
