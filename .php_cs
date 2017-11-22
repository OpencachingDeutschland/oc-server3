<?php

$blacklistedFiles = [];

$finder = PhpCsFixer\Finder::create()
    ->filter(function(SplFileInfo $file) use ($blacklistedFiles) {
        return !in_array($file->getFilename(), $blacklistedFiles, true);
    })
    ->in(__DIR__ . '/htdocs/src')
    ->in(__DIR__ . '/htdocs/app')
    ->in(__DIR__ . '/htdocs/bin')
;


return PhpCsFixer\Config::create()
   ->setRules([
       '@PSR2' => true,
       'single_blank_line_before_namespace' => true,
       'function_declaration' => true,
       'no_spaces_after_function_name' => true,
       'no_useless_return' => true,
       'no_trailing_whitespace' => true,
       'no_trailing_whitespace_in_comment' => true,
       'no_blank_lines_after_phpdoc' => true,
       'ordered_imports' => true,

       'trailing_comma_in_multiline_array' => true,
       'single_quote' => true,
       'method_separation' => true,
       'no_blank_lines_after_class_opening' => true,

       'visibility_required' => true,
       'blank_line_before_return' => true,
       'cast_spaces' => true,
       'class_definition' => array('singleLine' => true),
       'no_unused_imports' => true,
       'self_accessor' => true,
       'whitespace_after_comma_in_array' => true,
       'is_null' => true,
       'space_after_semicolon' => true,
       'semicolon_after_instruction' => true,
       'no_singleline_whitespace_before_semicolons' => true,
       'no_multiline_whitespace_before_semicolons' => true,
       'concat_space' => [
           'spacing' => 'one'
       ],
       'standardize_not_equals' => true,
       'ternary_operator_spaces' => true,
       'no_leading_namespace_whitespace' => true,
       'dir_constant' => true,
       'no_useless_else' => true,
       'native_function_casing' => true,
       'no_multiline_whitespace_around_double_arrow' => true,

       'phpdoc_return_self_reference' => true,
       'phpdoc_trim' => true,
       'phpdoc_var_without_name' => true,
       'phpdoc_no_empty_return' => true,
       'phpdoc_order' => true,
       'phpdoc_types' => true,
       'phpdoc_scalar' => true,
       'phpdoc_no_package' => true,
       'phpdoc_single_line_var_spacing' => true,
       'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],

   ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
