<?php

use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use Silverorange\PhpCodingTools\Standards\PhpCsFixer\Php81;

// Choose the appropriate base configuration for your project
$config = new Php81();

// Alternatively, if you have a set of custom rules you'd like to add in
// addition to the base configuration, you can define them here and then
// pass them into the constructor. e.g.
//
//   $my_rules = ['yoda_style' => true];
//   $config = new \Silverorange\PhpCodingTools\Standards\PhpCsFixer\Php81($my_rules);

// Set up the directories you want to process
$finder = (new Finder())
    ->in(__DIR__)
    ->exclude([
        'node_modules',
    ]);

return $config
    // uncomment the following if you want to use parallelism to speed up processing
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setFinder($finder);
