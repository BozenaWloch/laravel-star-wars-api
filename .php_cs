<?php
declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->exclude(['vendor', 'bootstrap'])
    ->notPath('_ide_helper.php')
    ->in(__DIR__);

/**
 * Removes unnecessary whitespaces around access modifiers in class.
 */
class NoWhitespacesAroundAccessModifiersFixer implements \PhpCsFixer\Fixer\FixerInterface
{

    protected const POSSIBLE_KIND = [
        T_PUBLIC,
        T_PROTECTED,
        T_PRIVATE,
        T_ABSTRACT,
        T_FINAL,
        T_STATIC,
        T_CONST,
        T_FUNCTION,
    ];


    /**
     * @inheritdoc
     */
    public function fix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens): void
    {
        for ($i = 0, $total = \count($tokens) - 1; $i < $total; ++$i) {
            if (!$tokens[$i]->isGivenKind(self::POSSIBLE_KIND)) {
                continue;
            }

            if (!$tokens[$i + 1]->isWhitespace()) {
                continue;
            }

            $tokens[$i + 1] = new \PhpCsFixer\Tokenizer\Token([T_WHITESPACE, ' ']);

            ++$i;
        }
    }


    /**
     * @inheritdoc
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::POSSIBLE_KIND);
    }


    /**
     * @inheritdoc
     */
    public function getPriority(): int
    {
        // Must run after VisibilityRequiredFixer
        return 1;
    }


    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Custom/no_whitespaces_around_access_modifiers';
    }


    /**
     * @inheritdoc
     */
    public function isRisky(): bool
    {
        return false;
    }


    /**
     * @inheritdoc
     */
    public function supports(\SplFileInfo $file): bool
    {
        return true;
    }
}

/**
 * Controls whitespaces around reference.
 */
class WhitespaceAroundReferenceFixer implements \PhpCsFixer\Fixer\FixerInterface
{

    public const SCOPE_AFTER = 'after';
    public const SCOPE_BEFORE = 'before';

    public const ACTION_NONE = 'none';
    public const ACTION_ONE = 'one';
    public const ACTION_AT_LEAST_ONE = 'at_least_one';
    public const ACTION_KEEP = 'keep';

    /**
     * @var string
     */
    protected $after = self::ACTION_NONE;

    /**
     * @var string
     */
    protected $before = self::ACTION_KEEP;


    /**
     * WhitespaceAroundReference constructor.
     *
     * @param array|null $configuration
     *
     * @throws \DomainException
     */
    public function __construct(array $configuration = [])
    {
        if (array_key_exists(self::SCOPE_AFTER, $configuration)) {
            if (!\in_array($configuration[self::SCOPE_AFTER], [
                self::ACTION_NONE,
                self::ACTION_ONE,
                self::ACTION_AT_LEAST_ONE,
                self::ACTION_KEEP,
            ], true)) {
                throw new DomainException(sprintf('Unknown value "%s" for the "after" option', $configuration[self::SCOPE_AFTER]));
            }

            $this->after = $configuration[self::SCOPE_AFTER];

            unset($configuration[self::SCOPE_AFTER]);
        }

        if (array_key_exists(self::SCOPE_BEFORE, $configuration)) {
            if (!\in_array($configuration[self::SCOPE_BEFORE], [
                self::ACTION_NONE,
                self::ACTION_ONE,
                self::ACTION_AT_LEAST_ONE,
                self::ACTION_KEEP,
            ], true)) {
                throw new DomainException(sprintf('Unknown value "%s" for the "before" option', $configuration[self::SCOPE_BEFORE]));
            }

            $this->before = $configuration[self::SCOPE_BEFORE];

            unset($configuration[self::SCOPE_BEFORE]);
        }

        if (!empty($configuration)) {
            throw new DomainException(sprintf('Unknown actions: %s', implode(', ', array_keys($configuration))));
        }
    }


    /**
     * @inheritdoc
     */
    public function fix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens): void
    {
        for ($i = 0, $j = 0, $total = \count($tokens) - 1; $i < $total; $j = ++$i) {
            if ($tokens[$i]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_RETURN_REF)) {
                $this->performAction($this->after, self::SCOPE_AFTER, $tokens, $i);
                $this->performAction($this->after, self::SCOPE_BEFORE, $tokens, $i);

                $total += $i - $j;
            }
        }
    }


    /**
     * @inheritdoc
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\PhpCsFixer\Tokenizer\CT::T_RETURN_REF);
    }


    /**
     * @inheritdoc
     */
    public function getPriority(): int
    {
        return -1;
    }


    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Custom/whitespace_around_reference';
    }


    /**
     * @inheritdoc
     */
    public function isRisky(): bool
    {
        return false;
    }


    /**
     * @inheritdoc
     */
    public function supports(\SplFileInfo $file): bool
    {
        return true;
    }


    /**
     * @param string                       $action
     * @param string                       $scope
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int                          $position
     */
    private function performAction(string $action, string $scope, \PhpCsFixer\Tokenizer\Tokens $tokens, int &$position)
    {
        $modifier = (self::SCOPE_AFTER === $scope) ? +1 : -1;

        switch ($action) {
            case self::ACTION_NONE:
                if (IsSet($tokens[$position + $modifier]) && $tokens[$position + $modifier]->isWhitespace()) {
                    $tokens->clearAt($position + $modifier);

                    --$position;
                }
                break;

            case self::ACTION_ONE:
                if (IsSet($tokens[$position + $modifier])) {
                    if ($tokens[$position + $modifier]->isWhitespace()) {
                        $tokens[$position + $modifier] = new \PhpCsFixer\Tokenizer\Token([T_WHITESPACE, ' ']);
                    } else {
                        $tokens->insertAt($position + $modifier + 1, new \PhpCsFixer\Tokenizer\Token([T_WHITESPACE, ' ']));

                        ++$position;
                    }
                }
                break;

            case self::ACTION_AT_LEAST_ONE:
                if (IsSet($tokens[$position + $modifier]) && !$tokens[$position + $modifier]->isWhitespace()) {
                    $tokens->insertAt($position + $modifier + 1, new \PhpCsFixer\Tokenizer\Token([T_WHITESPACE, ' ']));

                    ++$position;
                }
                break;

            case self::ACTION_KEEP:
                //
                break;
        }
    }
}

/**
 * Controls class FQN type hints in PhpDocs.
 */
class FQNControlFixer implements \PhpCsFixer\Fixer\FixerInterface
{

    public const PLACE_PARAM = 'param';
    public const PLACE_THROWS = 'throws';
    public const PLACE_RETURN = 'return';
    public const PLACE_PROPERTY = 'property';
    public const PLACE_PROPERTY_READ = 'property-read';
    public const PLACE_METHOD = 'method';
    public const PLACE_VAR = 'var';
    public const PLACE_COVERS = 'covers';

    /**
     * All possible places.
     */
    public const ALL_PLACES = [
        self::PLACE_PARAM         => self::PLACE_PARAM,
        self::PLACE_THROWS        => self::PLACE_THROWS,
        self::PLACE_RETURN        => self::PLACE_RETURN,
        self::PLACE_PROPERTY      => self::PLACE_PROPERTY,
        self::PLACE_PROPERTY_READ => self::PLACE_PROPERTY_READ,
        self::PLACE_METHOD        => self::PLACE_METHOD,
        self::PLACE_VAR           => self::PLACE_VAR,
        self::PLACE_COVERS        => self::PLACE_COVERS,
    ];

    /**
     * @var string[]
     */
    private $imports = [];

    /**
     * @var string
     */
    private $namespace = '';

    /**
     * @var string[]
     */
    private $places;


    /**
     * FQNControlFixer constructor.
     *
     * @param string[] $places
     *
     * @throws \DomainException
     */
    public function __construct(array $places = self::ALL_PLACES)
    {
        foreach ($places as $place) {
            if (!array_key_exists($place, self::ALL_PLACES)) {
                throw new DomainException(sprintf('Unknown place: %s', $place));
            }
        }

        $this->places = $places;
    }


    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function fix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens): void
    {
        $this->imports = [];
        $this->namespace = '';

        for ($i = 0, $total = \count($tokens) - 1; $i < $total; ++$i) {
            if ($tokens[$i]->isGivenKind(T_NAMESPACE)) {
                $this->namespace = '';

                for ($j = $i + 2; $j < $total && ';' !== $tokens[$j]->getContent() && '{' !== $tokens[$j]->getContent(); ++$j) {
                    if ($tokens[$j]->isGivenKind([
                        T_NS_SEPARATOR,
                        T_STRING,
                    ])) {
                        if ('' === $this->namespace && !$tokens[$j]->isGivenKind(T_NS_SEPARATOR)) {
                            $this->namespace = '\\';
                        }

                        $this->namespace .= $tokens[$j]->getContent();
                    }
                }
            } elseif ($tokens[$i]->isGivenKind(T_USE)) {
                $import = '';
                $group = null;

                for ($j = $i + 2; $j < $total && ';' !== $tokens[$j]->getContent(); ++$j) {
                    if ($tokens[$j]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_OPEN)) {
                        $group = '';
                        $end = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_GROUP_IMPORT_BRACE, $j);

                        for ($k = $j + 1; $k < $end; ++$k) {
                            $group .= $tokens[$k]->getContent();
                        }

                        foreach (preg_split('/\s*,\s*/', trim($group)) as $item) {
                            $this->addImport(sprintf('%s%s', $import, trim($item)));
                        }

                        $i = $end + 1;
                    } elseif ($tokens[$j]->isGivenKind([
                        T_NS_SEPARATOR,
                        T_STRING,
                        T_WHITESPACE,
                        T_AS,
                    ])) {
                        if ('' === $import && !$tokens[$j]->isGivenKind(T_NS_SEPARATOR)) {
                            $import = '\\';
                        }

                        $import .= $tokens[$j]->getContent();
                    }
                }

                if (null === $group) {
                    $this->addImport(trim($import));
                }
            } elseif ($tokens[$i]->isGivenKind(T_DOC_COMMENT)) {
                $pattern = sprintf('/\*\s*@(%s)\s+(.+)/i', implode('|', array_map(function (string $place) {
                    return preg_quote($place, '/');
                }, $this->places)));

                $tokens[$i] = new \PhpCsFixer\Tokenizer\Token([
                    T_DOC_COMMENT,
                    preg_replace_callback($pattern, function (array $match) {
                        switch (strtolower($match[1])) {
                            case self::PLACE_PARAM:
                            case self::PLACE_PROPERTY:
                            case self::PLACE_PROPERTY_READ:
                                $fixed = $this->handleParam($match[2]);
                                break;

                            case self::PLACE_THROWS:
                            case self::PLACE_RETURN:
                            case self::PLACE_VAR:
                                $fixed = $this->handleReturn($match[2]);
                                break;

                            case self::PLACE_METHOD:
                                $fixed = $this->handleMethod($match[2]);
                                break;

                            case self::PLACE_COVERS:
                                $fixed = $this->handleCovers($match[2]);
                                break;

                            default:
                                $fixed = $match[2];
                                break;
                        }

                        return sprintf('%s%s', substr($match[0], 0, -strlen($match[2])), $fixed);
                    }, $tokens[$i]->getContent()),
                ]);
            }
        }
    }


    /**
     * @inheritdoc
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([
            T_USE,
            T_DOC_COMMENT,
        ]);
    }


    /**
     * @inheritdoc
     */
    public function getPriority(): int
    {
        // Should be run before PhpdocAlignFixer (-21), before NoUnusedImportsFixer (-10) and before PhpdocTypesOrderFixer (0)
        return 1;
    }


    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Custom/fqn_control';
    }


    /**
     * @inheritdoc
     */
    public function isRisky(): bool
    {
        return false;
    }


    /**
     * @inheritdoc
     */
    public function supports(\SplFileInfo $file): bool
    {
        return true;
    }


    /**
     * @param string $content
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function handleParam(string $content): string
    {
        if ('$' === $content{0} && preg_match('/(\$\S+\s+)(.+)/', $content, $match)) {
            [
                1 => $variable,
                2 => $typeHints,
            ] = $match;

            $format = '%2$s%1$s';
        } elseif (preg_match('/(.+?)(\s+\$.+)/', $content, $match)) {
            [
                2 => $variable,
                1 => $typeHints,
            ] = $match;

            $format = '%1$s%2$s';
        } else {
            return $content;
        }

        return sprintf($format, $this->applyFQN($typeHints), $variable);
    }


    /**
     * @param string $content
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function handleReturn(string $content): string
    {
        $firstWhitespace = strpos($content, ' ');
        $comment = '';

        if (false !== $firstWhitespace) {
            $firstPipeline = strpos($content, '|');
            $firstCutOff = max((false === $firstPipeline) ? -1 : $firstPipeline, $firstWhitespace);
            $comment = substr($content, $firstCutOff);
            $content = substr($content, 0, $firstCutOff);
        }

        return sprintf('%s%s', $this->applyFQN($content), $comment);
    }


    /**
     * @param string $content
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function handleMethod(string $content): string
    {
        return preg_replace_callback('/\s*(?:static\s+)?(.*?)\s+[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\(.*\)\s*$/i', function (array $match) {
            return str_replace($match[1], $this->applyFQN($match[1]), $match[0]);
        }, $content);
    }


    /**
     * @param string $content
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function handleCovers(string $content): string
    {
        if (preg_match('/^([\w\s\\\\]+?)(\<extended\>)?(::.+)?$/i', $content, $match)) {
            unset($match[0]);

            $match[1] = $this->applyFQN($match[1]);

            return implode($match);
        }

        return $content;
    }


    /**
     * @param string $fqn
     *
     * @throws \RuntimeException
     */
    private function addImport(string $fqn): void
    {
        $import = $this->getUseNameAndNamespace($fqn);

        if (array_key_exists($import['name'], $this->imports)) {
            throw new RuntimeException(sprintf('Import "%s" is already defined', $import['name']));
        }

        $this->imports[$import['name']] = $import['fqn'];
        $this->imports[$import['fqn']] = $import['fqn'];
    }


    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function getImportFQN(string $name): string
    {
        if (!array_key_exists($name, $this->imports)) {
            $class = sprintf('%s\\%s', $this->namespace, $name);

            if (!class_exists($class)) {
                return $name;
                //throw new RuntimeException(sprintf('Import "%s" is not defined and is not in current namespace "%s" either', $name, $this->namespace));
            }

            return $class;
        }

        return $this->imports[$name];
    }


    /**
     * @param string $use
     *
     * @return array
     */
    private function getUseNameAndNamespace(string $use): array
    {
        if (preg_match('/\s+as\s+(.+)$/i', $use, $match)) {
            $name = $match[1];
            $use = substr($use, 0, -strlen($match[0]));
        } else {
            $lastIndexOf = strrpos($use, '\\');
            $name = false === $lastIndexOf ? $use : trim(substr($use, $lastIndexOf + 1));
        }

        return [
            'name' => $name,
            'fqn'  => preg_replace('/\s+/', '', $use),
        ];
    }


    /**
     * @param string $typeHint
     *
     * @return bool
     */
    private function isBasicType(string $typeHint): bool
    {
        return in_array(strtolower($typeHint), [
            'bool',
            'boolean',
            'int',
            'integer',
            'float',
            'double',
            'string',
            'array',
            'object',
            'mixed',
            'resource',
            'callable',
            'null',
            'iterable',
            'void',
        ], true);
    }


    /**
     * @param string $content
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function applyFQN(string $content): string
    {
        $typeHintsArray = preg_split('/(\s*\|\s*)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($typeHintsArray as $index => $typeHint) {
            if (0 === ($index % 2) && '\\' !== $typeHint{0} && preg_match('/(.+?)([\[\]\s]+)?$/im', $typeHint, $match) && !$this->isBasicType($match[1])) {
                $typeHintsArray[$index] = sprintf('%s%s', $this->getImportFQN($match[1]), $match[2] ?? '');
            }
        }

        return implode($typeHintsArray);
    }
}

// https://mlocati.github.io/php-cs-fixer-configurator/
return PhpCsFixer\Config::create()
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setUsingCache(true)
    ->registerCustomFixers([
        new NoWhitespacesAroundAccessModifiersFixer(),
        new FQNControlFixer(FQNControlFixer::ALL_PLACES),
        new WhitespaceAroundReferenceFixer([
            'after'  => WhitespaceAroundReferenceFixer::ACTION_ONE,
            'before' => WhitespaceAroundReferenceFixer::ACTION_ONE,
        ]),
    ])
    ->setRules([
        'Custom/no_whitespaces_around_access_modifiers' => true,
        'Custom/whitespace_around_reference'            => true,
        'Custom/fqn_control'                            => true,

        '@PSR2' => true,

        'align_multiline_comment' => [
            'comment_type' => 'phpdocs_only',
        ],

        'array_syntax' => [
            'syntax' => 'short',
        ],

        'binary_operator_spaces' => [
            'default'   => 'single_space',
            'operators' => [
                '=>' => 'align_single_space_minimal',
            ],
        ],

        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'die',
                'exit',
                'return',
                'throw',
                'try',
                'while',
                'do',
                'goto',
                'if',
                'switch',
                'yield',
            ],
        ],

        'blank_line_after_opening_tag' => false,
        'linebreak_after_opening_tag'  => true,
        'cast_spaces'                  => true,

        'class_attributes_separation' => [
            'elements' => [
                'const',
                'method',
                'property',
            ],
        ],

        'class_definition' => [
            'multiLineExtendsEachSingleLine' => false,
            'singleItemSingleLine'           => false,
            'singleLine'                     => false,
        ],

        'class_keyword_remove'       => false,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'compact_nullable_typehint'  => true,

        'concat_space' => [
            'spacing' => 'one',
        ],

        'declare_equal_normalize' => [
            'space' => 'none',
        ],

        'declare_strict_types' => true,   // Very risky for PHP < 7
        'dir_constant'         => true,
        'elseif'               => true,
        'encoding'             => true,

        'escape_implicit_backslashes' => [
            'double_quoted'  => true,
            'heredoc_syntax' => true,
            'single_quoted'  => false,
        ],

        'explicit_indirect_variable' => true,
        'full_opening_tag'           => true,

        'function_declaration' => [
            'closure_function_spacing' => 'one',
        ],

        'function_to_constant'    => true,
        'function_typehint_space' => true,
        'include'                 => true,

        'increment_style' => [
            'style' => 'pre',
        ],

        'indentation_type' => true,

        'is_null' => [
            'use_yoda_style' => true,
        ],

        'line_ending'           => true,
        'lowercase_cast'        => true,
        'lowercase_constants'   => true,
        'lowercase_keywords'    => true,
        'magic_constant_casing' => true,

        'method_argument_space' => [
            'ensure_fully_multiline'           => false,
            'keep_multiple_spaces_after_comma' => false,
        ],

        'method_chaining_indentation'            => true,
        'modernize_types_casting'                => true,
        'multiline_whitespace_before_semicolons' => true,
        'native_function_casing'                 => true,
        'native_function_invocation'             => true,
        'new_with_braces'                        => true,
        //'no_alias_functions'                     => true,
        'no_blank_lines_after_class_opening'     => false,
        'no_blank_lines_after_phpdoc'            => true,
        'no_closing_tag'                         => true,
        'no_empty_statement'                     => true,

        'no_extra_blank_lines' => [
            'tokens' => [
                'continue',
                'curly_brace_block',
                'return',
            ],
        ],

        'no_leading_import_slash'         => true,
        'no_leading_namespace_whitespace' => true,

        'no_mixed_echo_print' => [
            'use' => 'echo',
        ],

        'no_multiline_whitespace_around_double_arrow' => true,
        'no_null_property_initialization'             => true,
        'no_short_bool_cast'                          => true,
        'no_singleline_whitespace_before_semicolons'  => true,
        'no_spaces_after_function_name'               => true,
        'no_spaces_around_offset'                     => true,
        'no_spaces_inside_parenthesis'                => true,
        'no_superfluous_elseif'                       => true,
        'no_trailing_whitespace'                      => true,
        'no_trailing_whitespace_in_comment'           => true,
        'no_unneeded_control_parentheses'             => true,
        //'no_unneeded_curly_braces'                    => true,
        'no_unneeded_final_method'                    => true,
        'no_unused_imports'                           => true,
        'no_useless_else'                             => true,
        'no_useless_return'                           => true,
        'no_whitespace_before_comma_in_array'         => true,
        'no_whitespace_in_blank_line'                 => true,
        'not_operator_with_space'                     => false,
        'not_operator_with_successor_space'           => false,
        'object_operator_without_whitespace'          => true,

        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                'method_public',
                'method_protected',
                'method_private',
            ],
        ],

        'ordered_imports' => [
            'sortAlgorithm' => 'alpha',
            'importsOrder'  => [
                'const',
                'class',
                'function',
            ],
        ],

        'php_unit_fqcn_annotation' => true,

        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => false,
        ],

        'phpdoc_align'                   => true,
        'phpdoc_indent'                  => true,
        //'phpdoc_inline_tag'              => true,
        'phpdoc_no_useless_inheritdoc'   => true,
        'phpdoc_order'                   => true,
        'phpdoc_scalar'                  => true,
        'phpdoc_separation'              => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary'                 => true,
        'phpdoc_to_comment'              => false,  // Set to false because it also affects Swagger PhpDocs
        'phpdoc_trim'                    => true,
        'phpdoc_types'                   => true,

        'phpdoc_types_order' => [
            'sort_algorithm'  => 'alpha',   // Szkoda, że nie ma tu 'alpha_primitive_first'
            'null_adjustment' => 'always_last',
        ],

        'phpdoc_var_without_name' => false, // Set to false because it also (sometimes) removes variable name not only in PhpDocs related to class fields

        'return_type_declaration' => [
            'space_before' => 'none',
        ],

        'self_accessor'                      => true,
        'semicolon_after_instruction'        => true,
        'short_scalar_cast'                  => true,
        'simplified_null_return'             => true,
        'single_blank_line_at_eof'           => true,
        'single_blank_line_before_namespace' => true,
        'single_class_element_per_statement' => true,
        'single_import_per_statement'        => true,
        'single_line_after_imports'          => true,
        'single_line_comment_style'          => true,
        'single_quote'                       => true,

        'space_after_semicolon' => [
            'remove_in_empty_for_expressions' => true,
        ],

        'standardize_not_equals'            => true,
        'switch_case_semicolon_to_colon'    => true,
        'switch_case_space'                 => true,
        'ternary_operator_spaces'           => true,
        'ternary_to_null_coalescing'        => true,
        'trailing_comma_in_multiline_array' => true,
        'trim_array_spaces'                 => true,
        'unary_operator_spaces'             => true,

        'visibility_required' => [
            'elements' => [
                'property',
                'method',
                'const',
            ],
        ],

        'void_return' => true,

        'whitespace_after_comma_in_array' => true,

        'yoda_style' => [
            'equal'            => true,
            'identical'        => true,
            'less_and_greater' => null,
        ],
    ]);
