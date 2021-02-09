<?php




/* File-scope initializer */
namespace Wikimedia\WebIDL;


class Grammar extends \WikiPEG\PEGParserBase {
  // initializer
  
  	/** @var string */
  	private $filename = '';
  	/** @var int */
  	private $lineNum = 1;
  	/** @var bool */
  	private $keepComments = true;
  	/** @var string[]|null */
  	private $lastComments = null;
  	/** @var ?string */
  	private $lastWS = null;
  
  	/**
  	 * @param string $contents
  	 * @param array $options
  	 * @return array
  	 */
  	public static function load( string $contents, array $options = [] ) {
  		$g = new Grammar();
  		$g->filename = $options['sourceName'] ?? "<unknown>";
  		$g->keepComments = $options['keepComments'] ?? true;
  		$ast = $g->parse( $contents );
  		if ( $options['concrete'] ?? false ) {
  			$ast[] = [
  				'type' => 'eof',
  				'value' => '',
  				'trivia' => $g->lastWS ?? ''
  			];
  		}
  		return $ast;
  	}
  
  	private function withComments( $ast ) {
  		if ( !$this->keepComments ) {
  			unset( $ast['leadingComments'] );
  			unset( $ast['trailingComments'] );
  		}
  		return $ast;
  	}
  
  	private static $basicType = [
  		'type' => null,
  		'extAttrs' => [],
  		'generic' => '',
  		'nullable' => false,
  		'union' => false,
  		'idlType' => null,
  	];
  
  	private static $basicExtAttr = [
  		'type' => 'extended-attribute',
  		'name' => null,
  		'arguments' => [],
  		'rhs' => null,
  	];
  
  	private static function setType(array &$t, string $val): array {
  		if ( ($t['type'] ?? null) !== null) {
  			return $t;
  		}
  		$t['type'] = $val;
  		if (is_array($t['idlType'] ?? null)) {
  			if ($t['generic'] === '' && $t['union'] === false) {
  				self::setType($t['idlType'], $val);
  			} else if ($t['union'] === false) {
  				foreach ($t['idlType'] as &$tt) {
  					self::setType($tt, $val);
  				}
  			}
  		}
  		return $t;
  	}
  

  // cache init
  

  // expectations
  protected $expectations = [
    0 => ["type" => "end", "description" => "end of input"],
    1 => ["type" => "class", "value" => "[\\t ]", "description" => "[\\t ]"],
    2 => ["type" => "literal", "value" => "//", "description" => "\"//\""],
    3 => ["type" => "class", "value" => "[^\\n\\r]", "description" => "[^\\n\\r]"],
    4 => ["type" => "literal", "value" => "/*", "description" => "\"/*\""],
    5 => ["type" => "class", "value" => "[^\\n\\r*]", "description" => "[^\\n\\r*]"],
    6 => ["type" => "literal", "value" => "*", "description" => "\"*\""],
    7 => ["type" => "literal", "value" => "*/", "description" => "\"*/\""],
    8 => ["type" => "literal", "value" => "[", "description" => "\"[\""],
    9 => ["type" => "literal", "value" => "]", "description" => "\"]\""],
    10 => ["type" => "literal", "value" => "\x0a", "description" => "\"\\n\""],
    11 => ["type" => "literal", "value" => "\x0d\x0a", "description" => "\"\\r\\n\""],
    12 => ["type" => "literal", "value" => "\x0d", "description" => "\"\\r\""],
    13 => ["type" => "literal", "value" => ",", "description" => "\",\""],
    14 => ["type" => "literal", "value" => "callback", "description" => "\"callback\""],
    15 => ["type" => "literal", "value" => "interface", "description" => "\"interface\""],
    16 => ["type" => "literal", "value" => "namespace", "description" => "\"namespace\""],
    17 => ["type" => "literal", "value" => "{", "description" => "\"{\""],
    18 => ["type" => "literal", "value" => "}", "description" => "\"}\""],
    19 => ["type" => "literal", "value" => ";", "description" => "\";\""],
    20 => ["type" => "literal", "value" => "partial", "description" => "\"partial\""],
    21 => ["type" => "literal", "value" => "dictionary", "description" => "\"dictionary\""],
    22 => ["type" => "literal", "value" => "enum", "description" => "\"enum\""],
    23 => ["type" => "literal", "value" => "typedef", "description" => "\"typedef\""],
    24 => ["type" => "literal", "value" => "includes", "description" => "\"includes\""],
    25 => ["type" => "literal", "value" => "(", "description" => "\"(\""],
    26 => ["type" => "literal", "value" => ")", "description" => "\")\""],
    27 => ["type" => "literal", "value" => "=", "description" => "\"=\""],
    28 => ["type" => "class", "value" => "[-_]", "description" => "[-_]"],
    29 => ["type" => "class", "value" => "[A-Za-z]", "description" => "[A-Za-z]"],
    30 => ["type" => "class", "value" => "[-_0-9A-Za-z]", "description" => "[-_0-9A-Za-z]"],
    31 => ["type" => "literal", "value" => ":", "description" => "\":\""],
    32 => ["type" => "literal", "value" => "mixin", "description" => "\"mixin\""],
    33 => ["type" => "literal", "value" => "async", "description" => "\"async\""],
    34 => ["type" => "literal", "value" => "attribute", "description" => "\"attribute\""],
    35 => ["type" => "literal", "value" => "const", "description" => "\"const\""],
    36 => ["type" => "literal", "value" => "constructor", "description" => "\"constructor\""],
    37 => ["type" => "literal", "value" => "deleter", "description" => "\"deleter\""],
    38 => ["type" => "literal", "value" => "getter", "description" => "\"getter\""],
    39 => ["type" => "literal", "value" => "inherit", "description" => "\"inherit\""],
    40 => ["type" => "literal", "value" => "iterable", "description" => "\"iterable\""],
    41 => ["type" => "literal", "value" => "maplike", "description" => "\"maplike\""],
    42 => ["type" => "literal", "value" => "readonly", "description" => "\"readonly\""],
    43 => ["type" => "literal", "value" => "required", "description" => "\"required\""],
    44 => ["type" => "literal", "value" => "setlike", "description" => "\"setlike\""],
    45 => ["type" => "literal", "value" => "setter", "description" => "\"setter\""],
    46 => ["type" => "literal", "value" => "static", "description" => "\"static\""],
    47 => ["type" => "literal", "value" => "stringifier", "description" => "\"stringifier\""],
    48 => ["type" => "literal", "value" => "unrestricted", "description" => "\"unrestricted\""],
    49 => ["type" => "literal", "value" => "ArrayBuffer", "description" => "\"ArrayBuffer\""],
    50 => ["type" => "literal", "value" => "DataView", "description" => "\"DataView\""],
    51 => ["type" => "literal", "value" => "Int8Array", "description" => "\"Int8Array\""],
    52 => ["type" => "literal", "value" => "Int16Array", "description" => "\"Int16Array\""],
    53 => ["type" => "literal", "value" => "Int32Array", "description" => "\"Int32Array\""],
    54 => ["type" => "literal", "value" => "Uint8Array", "description" => "\"Uint8Array\""],
    55 => ["type" => "literal", "value" => "Uint16Array", "description" => "\"Uint16Array\""],
    56 => ["type" => "literal", "value" => "Uint32Array", "description" => "\"Uint32Array\""],
    57 => ["type" => "literal", "value" => "Uint8ClampedArray", "description" => "\"Uint8ClampedArray\""],
    58 => ["type" => "literal", "value" => "Float32Array", "description" => "\"Float32Array\""],
    59 => ["type" => "literal", "value" => "Float64Array", "description" => "\"Float64Array\""],
    60 => ["type" => "literal", "value" => "ByteString", "description" => "\"ByteString\""],
    61 => ["type" => "literal", "value" => "DOMString", "description" => "\"DOMString\""],
    62 => ["type" => "literal", "value" => "FrozenArray", "description" => "\"FrozenArray\""],
    63 => ["type" => "literal", "value" => "Infinity", "description" => "\"Infinity\""],
    64 => ["type" => "literal", "value" => "NaN", "description" => "\"NaN\""],
    65 => ["type" => "literal", "value" => "ObservableArray", "description" => "\"ObservableArray\""],
    66 => ["type" => "literal", "value" => "Promise", "description" => "\"Promise\""],
    67 => ["type" => "literal", "value" => "USVString", "description" => "\"USVString\""],
    68 => ["type" => "literal", "value" => "any", "description" => "\"any\""],
    69 => ["type" => "literal", "value" => "bigint", "description" => "\"bigint\""],
    70 => ["type" => "literal", "value" => "boolean", "description" => "\"boolean\""],
    71 => ["type" => "literal", "value" => "byte", "description" => "\"byte\""],
    72 => ["type" => "literal", "value" => "double", "description" => "\"double\""],
    73 => ["type" => "literal", "value" => "false", "description" => "\"false\""],
    74 => ["type" => "literal", "value" => "float", "description" => "\"float\""],
    75 => ["type" => "literal", "value" => "long", "description" => "\"long\""],
    76 => ["type" => "literal", "value" => "null", "description" => "\"null\""],
    77 => ["type" => "literal", "value" => "object", "description" => "\"object\""],
    78 => ["type" => "literal", "value" => "octet", "description" => "\"octet\""],
    79 => ["type" => "literal", "value" => "or", "description" => "\"or\""],
    80 => ["type" => "literal", "value" => "optional", "description" => "\"optional\""],
    81 => ["type" => "literal", "value" => "record", "description" => "\"record\""],
    82 => ["type" => "literal", "value" => "sequence", "description" => "\"sequence\""],
    83 => ["type" => "literal", "value" => "short", "description" => "\"short\""],
    84 => ["type" => "literal", "value" => "symbol", "description" => "\"symbol\""],
    85 => ["type" => "literal", "value" => "true", "description" => "\"true\""],
    86 => ["type" => "literal", "value" => "unsigned", "description" => "\"unsigned\""],
    87 => ["type" => "literal", "value" => "undefined", "description" => "\"undefined\""],
    88 => ["type" => "literal", "value" => "\"", "description" => "\"\\\"\""],
    89 => ["type" => "class", "value" => "[^\\\"]", "description" => "[^\\\"]"],
    90 => ["type" => "class", "value" => "[0-9]", "description" => "[0-9]"],
    91 => ["type" => "literal", "value" => ".", "description" => "\".\""],
    92 => ["type" => "class", "value" => "[Ee]", "description" => "[Ee]"],
    93 => ["type" => "class", "value" => "[+-]", "description" => "[+-]"],
    94 => ["type" => "class", "value" => "[^\\t\\n\\r 0-9A-Za-z]", "description" => "[^\\t\\n\\r 0-9A-Za-z]"],
    95 => ["type" => "literal", "value" => "-", "description" => "\"-\""],
    96 => ["type" => "literal", "value" => "-Infinity", "description" => "\"-Infinity\""],
    97 => ["type" => "literal", "value" => "...", "description" => "\"...\""],
    98 => ["type" => "literal", "value" => "<", "description" => "\"<\""],
    99 => ["type" => "literal", "value" => ">", "description" => "\">\""],
    100 => ["type" => "literal", "value" => "?", "description" => "\"?\""],
    101 => ["type" => "class", "value" => "[ \\t]", "description" => "[ \\t]"],
    102 => ["type" => "class", "value" => "[1-9]", "description" => "[1-9]"],
    103 => ["type" => "literal", "value" => "0x", "description" => "\"0x\""],
    104 => ["type" => "literal", "value" => "0X", "description" => "\"0X\""],
    105 => ["type" => "class", "value" => "[0-9A-Fa-f]", "description" => "[0-9A-Fa-f]"],
    106 => ["type" => "literal", "value" => "0", "description" => "\"0\""],
    107 => ["type" => "class", "value" => "[0-7]", "description" => "[0-7]"],
  ];

  // actions
  private function a0($d) {
   return $d; 
  }
  private function a1($r) {
  
    $c = array_values(array_filter($r, function($v) { return $v[0] === '/'; }));
    $this->lastComments = $c;
    $this->lastWS = implode( '', $r );
    return $c;
  
  }
  private function a2($c, $e, $d) {
  
        $d['extAttrs'] = $e;
        $d['leadingComments'] = $c;
        return $this->withComments($d);
      
  }
  private function a3() {
  
      $r = $this->lastComments; $this->lastComments = null; return $r;
  
  }
  private function a4($e, $rest) {
   array_unshift($rest, $e); return $rest; 
  }
  private function a5() {
   return []; 
  }
  private function a6($nl) {
   $this->lineNum++; return $nl; 
  }
  private function a7($c) {
   return $c; 
  }
  private function a8($im) {
   return $im; 
  }
  private function a9($name, $m) {
  
      return [
          'type' => 'namespace',
          'name' => $name,
          'inheritance' => null,
          'partial' => false,
          'members' => $m,
      ];
  
  }
  private function a10($p) {
  
      $p['partial'] = true; return $p;
  
  }
  private function a11($name, $inh, $m) {
  
          return [
              'type' => 'dictionary',
              'name' => $name,
              'partial' => false,
              'members' => $m,
              'inheritance' => $inh,
          ];
      
  }
  private function a12($name, $vals) {
  
      return [
          'type' => 'enum',
          'name' => $name,
          'values' => $vals,
      ];
  
  }
  private function a13($t, $name) {
  
      return [
          'type' => 'typedef',
           'name' => $name,
           'idlType' => self::setType($t, 'typedef-type'),
      ];
  
  }
  private function a14($target, $incl) {
  
      return [
          'type' => 'includes',
          'target' => $target,
          'includes' => $incl,
      ];
  
  }
  private function a15($name) {
  
      return [ 'name' => $name ] + self::$basicExtAttr;
  
  }
  private function a16($name, $args) {
  
      return [ 'name' => $name, 'arguments' => $args ] + self::$basicExtAttr;
  
  }
  private function a17($name, $rhs) {
  
      return [
          'name' => $name,
          'rhs' => $rhs,
      ] + self::$basicExtAttr;
  
  }
  private function a18($name, $rhs, $args) {
  
      return [
          'name' => $name,
          'arguments' => $args,
          'rhs' => [ 'type' => 'identifier', 'value' => $rhs ],
      ] + self::$basicExtAttr;
  
  }
  private function a19($name, $m) {
  
      return [
          'type' => 'callback interface',
          'name' => $name,
          'inheritance' => null,
          'members' => $m,
          'partial' => false,
      ];
  
  }
  private function a20($s) {
  
          return $s[0] == "_" ? substr($s, 1) : $s ;
      
  }
  private function a21($c, $e, $m) {
  
        $m['extAttrs'] = $e;
        $m['leadingComments'] = $c;
        return $this->withComments( $m );
      
  }
  private function a22($p) {
   return $p; 
  }
  private function a23($name) {
  return $name;
  }
  private function a24($s, $vals) {
  
      array_unshift( $vals, [
          'type' => 'enum-value',
          'value' => $s,
      ] );
      return $vals;
  
  }
  private function a25($e, $t) {
  
          $t['extAttrs'] = $e; return $t;
      
  }
  private function a26($a, $rest) {
   array_unshift($rest, $a); return $rest; 
  }
  private function a27($rhs) {
  
          return [ 'type' => 'identifier', 'value' => $rhs ];
      
  }
  private function a28($s, $s2) {
  return $s2;
  }
  private function a29($s, $rest) {
  
          array_unshift($rest, $s);
          $val = array_map(function($item) {
              return [ 'value' => $item ];
          }, $rest);
          return [ 'type' => 'identifier-list', 'value' => $val ];
      
  }
  private function a30($s) {
  
          return [ 'type' => 'string', 'value' => $s ];
      
  }
  private function a31($s, $rest) {
  
          array_unshift($rest, $s);
          $val = array_map(function($item) {
              return [ 'value' => $item ];
          }, $rest);
          return [ 'type' => 'string-list', 'value' => $val ];
      
  }
  private function a32($s) {
  
          return [ 'type' => 'integer', 'value' => $s ];
      
  }
  private function a33($s, $rest) {
  
          array_unshift($rest, $s);
          $val = array_map(function($item) {
              return [ 'value' => $item ];
          }, $rest);
          return [ 'type' => 'integer-list', 'value' => $val ];
      
  }
  private function a34($s) {
  
          return [ 'type' => 'decimal', 'value' => $s ];
      
  }
  private function a35($s, $rest) {
  
          array_unshift($rest, $s);
          $val = array_map(function($item) {
              return [ 'value' => $item ];
          }, $rest);
          return [ 'type' => 'decimal-list', 'value' => $val ];
      
  }
  private function a36($name, $t, $args) {
  
      return [
          'type' => 'callback',
           'name' => $name,
           'idlType' => self::setType($t, 'return-type'),
           'arguments' => $args,
      ];
  
  }
  private function a37($name, $inh, $m, $c) {
  
      return $this->withComments( [
          'type' => 'interface',
          'name' => $name,
          'inheritance' => $inh,
          'members' => $m,
          'partial' => false,
          'trailingComments' => $c,
      ] );
  
  }
  private function a38($name, $m) {
  
      return [
          'type' => 'interface mixin',
          'name' => $name,
          'inheritance' => null,
          'partial' => false,
          'members' => $m,
      ];
  
  }
  private function a39($k) {
   return $k; 
  }
  private function a40($s) {
   return $s; 
  }
  private function a41($a) {
   $a['readonly'] = true; return $a; 
  }
  private function a42($name, $m) {
  
         return [
             'type' => 'dictionary',
             'name' => $name,
             'partial' => true,
             'members' => $m,
             'inheritance' => null,
         ];
     
  }
  private function a43($t, $name) {
  
        return [
            'type' => 'field',
            'name' => $name,
            'required' => true,
            'idlType' => self::setType($t, 'dictionary-type'),
            'default' => null,
        ];
      
  }
  private function a44($t, $name, $d) {
  
        return [
            'type' => 'field',
            'name' => $name,
            'required' => false,
            'idlType' => self::setType($t, 'dictionary-type'),
            'default' => $d
        ];
    
  }
  private function a45($vals) {
   return $vals; 
  }
  private function a46($t, $n) {
   $t['nullable'] = ($n !== null); return $t; 
  }
  private function a47($e, $a) {
  
          $a['extAttrs'] = $e; return $a;
      
  }
  private function a48($m, $n) {
  
      return $m * $n;
  
  }
  private function a49($s) {
   return floatval( $s ); 
  }
  private function a50() {
   return null; 
  }
  private function a51($r) {
  
    return array_values(array_filter($r, function($v) { return $v !== null; }));
  
  }
  private function a52($t, $o) {
  
      $o['idlType'] = self::setType($t, 'return-type');
      return $o;
  
  }
  private function a53($t, $name, $c) {
  
      return $this->withComments( [
          'type' => 'attribute',
          'name' => $name,
          'special' => '',
          'readonly' => false,
          'idlType' => self::setType($t, 'attribute-type'),
          'trailingComments' => $c,
      ] );
  
  }
  private function a54($name, $m) {
  
      return [
          'type' => 'interface',
          'name' => $name,
          'inheritance' => null,
          'members' => $m,
      ];
  
  }
  private function a55($val) {
   return $val; 
  }
  private function a56($s, $vals) {
  
      array_unshift( $vals, [
          'type' => 'enum-value',
          'value' => $s,
      ] );
      return $vals;
    
  }
  private function a57($t, $t2) {
   return $t2; 
  }
  private function a58($t, $rest) {
  
          array_unshift($rest, $t);
          return [ 'idlType' => $rest, 'union' => true ] + self::$basicType;
      
  }
  private function a59() {
   return true; 
  }
  private function a60($t, $name, $d) {
  
        return [
            'type' => 'argument',
            'default' => $d,
            'optional' => true,
            'variadic' => false,
            'idlType' => self::setType($t, 'argument-type'),
            'name' => $name,
        ];
    
  }
  private function a61($t, $e, $name) {
  
        return [
            'type' => 'argument',
            'default' => null,
            'optional' => false,
            'variadic' => ($e !== null),
            'idlType' => self::setType($t, 'argument-type'),
            'name' => $name,
        ];
    
  }
  private function a62() {
   return -1; 
  }
  private function a63() {
   return 1; 
  }
  private function a64($s) {
   return intval($s); 
  }
  private function a65($s) {
   return hexdec($s); 
  }
  private function a66($s) {
   return octdec( $s ); 
  }
  private function a67($t, $name, $v, $c) {
  
      return $this->withComments( [
          'type' => 'const',
          'idlType' => $t,
          'name' => $name,
          'value' => $v,
          'trailingComments' => $c,
      ] );
  
  }
  private function a68($ro, $a) {
  
        $a['readonly'] = ($ro !== null);
        return $a;
    
  }
  private function a69($name, $args, $c) {
  
      return $this->withComments( [
          'type' => 'operation',
          'special' => '',
          'idlType' => null,
          'name' => $name,
          'arguments' => $args,
          'extAttrs' => null,
          'trailingComments' => $c,
      ] );
  
  }
  private function a70($id) {
   return $id; 
  }
  private function a71($s) {
   return [ 'type' => 'string', 'value' => $s ]; 
  }
  private function a72() {
   return [ 'type' => 'sequence', 'value' => [] ]; 
  }
  private function a73() {
   return [ 'type' => 'dictionary' ]; 
  }
  private function a74() {
   return [ 'type' => 'null' ]; 
  }
  private function a75($t) {
  
          return [ 'idlType' => $t ] + self::$basicType;
      
  }
  private function a76($g, $t) {
  
          return [ 'idlType' => [$t], 'generic' => $g ] + self::$basicType;
      
  }
  private function a77($t) {
  
        return [ 'idlType' => $t ] + self::$basicType;
    
  }
  private function a78($dt, $n) {
   $dt['nullable'] = ($n !== null); return $dt; 
  }
  private function a79() {
  
        return [ 'idlType' => 'any' ] + self::$basicType;
      
  }
  private function a80($t) {
  
          if ( $t['idlType'] === 'void' ) { $t['type'] = 'return-type'; }
          return [ 'idlType' => [$t], 'generic' => 'Promise' ] + self::$basicType;
      
  }
  private function a81($t, $n) {
  
          $t['nullable'] = ($n !== null); return $t;
      
  }
  private function a82($name) {
   return $name; 
  }
  private function a83($t) {
  
          return [ 'type' => 'const-type', 'idlType' => $t ] + self::$basicType;
      
  }
  private function a84($args, $c) {
  
          return $this->withComments( [
            'type' => 'constructor',
            'arguments' => $args,
            'trailingComments' => $c,
          ] );
    
  }
  private function a85($rest) {
  
      $rest['special'] = 'stringifier';
      return $rest;
  
  }
  private function a86($v) {
   return $v; 
  }
  private function a87($t1, $t2) {
  
          $t1 = [ 'idlType' => $t1 ] + self::$basicType;
          return [ 'idlType' => [$t1,$t2], 'generic' => 'record' ] + self::$basicType;
      
  }
  private function a88($v) {
  
          return [ 'type' => 'boolean', 'value' => ($v === "true") ];
      
  }
  private function a89($s) {
  
          return [ 'type' => 'number', 'value' => $s ];
      
  }
  private function a90() {
  
          return [ 'type' => 'Infinity', 'negative' => true ];
      
  }
  private function a91() {
  
          return [ 'type' => 'Infinity', 'negative' => false ];
      
  }
  private function a92() {
  
          return [ 'type' => 'NaN' ];
      
  }
  private function a93($rest) {
  
      $rest['special'] = 'static';
      return $rest;
  
  }
  private function a94($t1, $t2, $c) {
  
          return $this->withComments( [
              'type' => 'iterable',
              'idlType' => $t2 ? [ $t1, $t2 ] : [ $t1 ],
              'readonly' => false,
              'async' => false,
              'arguments' => [],
              'trailingComments' => $c,
          ] );
      
  }
  private function a95($t1, $t2, $args, $c) {
  
          return $this->withComments( [
              'type' => 'iterable',
              'idlType' => $t2 ? [ $t1, $t2 ] : [ $t1 ],
              'readonly' => false,
              'async' => true,
              'arguments' => $args ?? [],
              'trailingComments' => $c,
          ] );
      
  }
  private function a96($m) {
  
      $m['readonly'] = true; return $m;
  
  }
  private function a97($t1, $t2, $c) {
  
          return $this->withComments( [
              'type' => 'maplike',
              'idlType' => [ $t1, $t2 ],
              'readonly' => false,
              'async' => false,
              'arguments' => [],
              'trailingComments' => $c,
          ] );
      
  }
  private function a98($t, $c) {
  
           return $this->withComments( [
               'type' => 'setlike',
               'idlType' => [ $t ],
               'readonly' => false,
               'async' => false,
               'arguments' => [],
               'trailingComments' => $c,
           ] );
      
  }
  private function a99($a) {
  
      $a['special'] = 'inherit';
      return $a;
  
  }
  private function a100($ro, $a) {
  
      $a['readonly'] = ($ro !== null);
      return $a;
    
  }
  private function a101($c) {
  
      return $this->withComments( [
          'type' => 'operation',
          'name' => '',
          'arguments' => [],
          'special' => 'stringifier',
          'trailingComments' => $c,
      ] );
  
  }
  private function a102($t) {
   return "unsigned $t"; 
  }
  private function a103($t) {
   return "unrestricted $t"; 
  }
  private function a104($s, $o) {
  
      $o['special'] = $s; return $o;
  
  }
  private function a105($t) {
   return $t; 
  }
  private function a106($args) {
   return $args; 
  }
  private function a107($l) {
   return $l ? "long long" : "long"; 
  }

  // generated
  private function parsestart($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->discard_($silence);
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseDefinitions($silence);
    // d <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a0($r5);
    }
    // free $p3
    return $r1;
  }
  private function discard_($silence) {
    $p2 = $this->currPos;
    $r3 = [];
    for (;;) {
      // start choice_1
      $p5 = $this->currPos;
      $r4 = $this->discardwhitespace($silence);
      if ($r4!==self::$FAILED) {
        $r4 = substr($this->input, $p5, $this->currPos - $p5);
        goto choice_1;
      } else {
        $r4 = self::$FAILED;
      }
      // free $p5
      $p5 = $this->currPos;
      $r4 = $this->discardcomment($silence);
      if ($r4!==self::$FAILED) {
        $r4 = substr($this->input, $p5, $this->currPos - $p5);
      } else {
        $r4 = self::$FAILED;
      }
      // free $p5
      choice_1:
      if ($r4!==self::$FAILED) {
        $r3[] = $r4;
      } else {
        break;
      }
    }
    // r <- $r3
    // free $r4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a1($r3);
    }
    return $r1;
  }
  private function parseDefinitions($silence) {
    $r1 = [];
    for (;;) {
      $p3 = $this->currPos;
      // start seq_1
      $p4 = $this->currPos;
      $r5 = $this->parseleadingComments($silence);
      // c <- $r5
      if ($r5===self::$FAILED) {
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r6 = $this->parseExtendedAttributeList($silence);
      // e <- $r6
      if ($r6===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r7 = $this->parseDefinition($silence);
      // d <- $r7
      if ($r7===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r2 = true;
      seq_1:
      if ($r2!==self::$FAILED) {
        $this->savedPos = $p3;
        $r2 = $this->a2($r5, $r6, $r7);
        $r1[] = $r2;
      } else {
        break;
      }
      // free $p4
    }
    // free $r2
    return $r1;
  }
  private function discardwhitespace($silence) {
    $r1 = self::$FAILED;
    for (;;) {
      // start choice_1
      $r2 = $this->input[$this->currPos] ?? '';
      if ($r2 === "\x09" || $r2 === " ") {
        $this->currPos++;
        goto choice_1;
      } else {
        $r2 = self::$FAILED;
        if (!$silence) {$this->fail(1);}
      }
      $r2 = $this->discardeol($silence);
      choice_1:
      if ($r2!==self::$FAILED) {
        $r1 = true;
      } else {
        break;
      }
    }
    // free $r2
    return $r1;
  }
  private function discardcomment($silence) {
    // start choice_1
    // start seq_1
    $p2 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "//", $this->currPos, 2, false) === 0) {
      $r3 = "//";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(2);}
      $r3 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    for (;;) {
      $r5 = self::charAt($this->input, $this->currPos);
      if ($r5 !== '' && !($r5 === "\x0a" || $r5 === "\x0d")) {
        $this->currPos += strlen($r5);
      } else {
        $r5 = self::$FAILED;
        if (!$silence) {$this->fail(3);}
        break;
      }
    }
    // free $r5
    $r4 = true;
    if ($r4===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $r4
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_2
    $p2 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "/*", $this->currPos, 2, false) === 0) {
      $r4 = "/*";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(4);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    for (;;) {
      // start choice_2
      $r6 = self::$FAILED;
      for (;;) {
        if (strcspn($this->input, "\x0a\x0d*", $this->currPos, 1) !== 0) {
          $r7 = self::consumeChar($this->input, $this->currPos);
          $r6 = true;
        } else {
          $r7 = self::$FAILED;
          if (!$silence) {$this->fail(5);}
          break;
        }
      }
      if ($r6!==self::$FAILED) {
        goto choice_2;
      }
      // free $r7
      $r6 = $this->discardeol($silence);
      if ($r6!==self::$FAILED) {
        goto choice_2;
      }
      // start seq_3
      $p8 = $this->currPos;
      if (($this->input[$this->currPos] ?? null) === "*") {
        $this->currPos++;
        $r7 = "*";
      } else {
        if (!$silence) {$this->fail(6);}
        $r7 = self::$FAILED;
        $r6 = self::$FAILED;
        goto seq_3;
      }
      $p9 = $this->currPos;
      if (($this->input[$this->currPos] ?? null) === "/") {
        $this->currPos++;
        $r10 = "/";
      } else {
        $r10 = self::$FAILED;
      }
      if ($r10 === self::$FAILED) {
        $r10 = false;
      } else {
        $r10 = self::$FAILED;
        $this->currPos = $p9;
        $this->currPos = $p8;
        $r6 = self::$FAILED;
        goto seq_3;
      }
      // free $p9
      $r6 = true;
      seq_3:
      // free $p8
      choice_2:
      if ($r6===self::$FAILED) {
        break;
      }
    }
    // free $r6
    $r5 = true;
    if ($r5===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    // free $r5
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "*/", $this->currPos, 2, false) === 0) {
      $r5 = "*/";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(7);}
      $r5 = self::$FAILED;
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    // free $p2
    choice_1:
    return $r1;
  }
  private function parseleadingComments($silence) {
    $p2 = $this->currPos;
    $r1 = '';
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a3();
    }
    return $r1;
  }
  private function parseExtendedAttributeList($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "[") {
      $this->currPos++;
      $r4 = "[";
    } else {
      if (!$silence) {$this->fail(8);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseExtendedAttribute($silence);
    // e <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->parseExtendedAttributes($silence);
    // rest <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "]") {
      $this->currPos++;
      $r8 = "]";
    } else {
      if (!$silence) {$this->fail(9);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a4($r6, $r7);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p10 = $this->currPos;
    $p11 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "[") {
      $this->currPos++;
      $r12 = "[";
    } else {
      $r12 = self::$FAILED;
    }
    if ($r12 === self::$FAILED) {
      $r12 = false;
    } else {
      $r12 = self::$FAILED;
      $this->currPos = $p11;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    // free $p11
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a5();
    }
    // free $p10
    choice_1:
    return $r1;
  }
  private function parseDefinition($silence) {
    // start choice_1
    $r1 = $this->parseCallbackOrInterfaceOrMixin($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseNamespace($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parsePartial($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseDictionary($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseEnum($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseTypedef($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseIncludesStatement($silence);
    choice_1:
    return $r1;
  }
  private function discardeol($silence) {
    $p2 = $this->currPos;
    // start choice_1
    if (($this->input[$this->currPos] ?? null) === "\x0a") {
      $this->currPos++;
      $r3 = "\x0a";
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(10);}
      $r3 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "\x0d\x0a", $this->currPos, 2, false) === 0) {
      $r3 = "\x0d\x0a";
      $this->currPos += 2;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(11);}
      $r3 = self::$FAILED;
    }
    if (($this->input[$this->currPos] ?? null) === "\x0d") {
      $this->currPos++;
      $r3 = "\x0d";
    } else {
      if (!$silence) {$this->fail(12);}
      $r3 = self::$FAILED;
    }
    choice_1:
    // nl <- $r3
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a6($r3);
    }
    return $r1;
  }
  private function parseExtendedAttribute($silence) {
    // start choice_1
    $r1 = $this->parseExtendedAttributeNoArgs($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseExtendedAttributeArgList($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseExtendedAttributeIdent($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseExtendedAttributeNamedArgList($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    $r1 = $this->discardSpecCompliantExtendedAttribute($silence);
    if ($r1!==self::$FAILED) {
      $r1 = substr($this->input, $p2, $this->currPos - $p2);
    } else {
      $r1 = self::$FAILED;
    }
    // free $p2
    choice_1:
    return $r1;
  }
  private function parseExtendedAttributes($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === ",") {
      $this->currPos++;
      $r4 = ",";
    } else {
      if (!$silence) {$this->fail(13);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseExtendedAttribute($silence);
    // e <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->parseExtendedAttributes($silence);
    // rest <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a4($r6, $r7);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    $r1 = '';
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a5();
    }
    choice_1:
    return $r1;
  }
  private function parseCallbackOrInterfaceOrMixin($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "callback", $this->currPos, 8, false) === 0) {
      $r4 = "callback";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(14);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseCallbackRestOrInterface($silence);
    // c <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a7($r6);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p7 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "interface", $this->currPos, 9, false) === 0) {
      $r8 = "interface";
      $this->currPos += 9;
    } else {
      if (!$silence) {$this->fail(15);}
      $r8 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r9 = $this->discardi_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p7;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r10 = $this->parseInterfaceOrMixin($silence);
    // im <- $r10
    if ($r10===self::$FAILED) {
      $this->currPos = $p7;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a8($r10);
    }
    // free $p7
    choice_1:
    return $r1;
  }
  private function parseNamespace($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "namespace", $this->currPos, 9, false) === 0) {
      $r4 = "namespace";
      $this->currPos += 9;
    } else {
      if (!$silence) {$this->fail(16);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseidentifier($silence);
    // name <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r8 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->parseNamespaceMembers($silence);
    // m <- $r10
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r11 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r11 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r12 = $this->discard_($silence);
    if ($r12===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r13 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r13 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r14 = $this->discard_($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a9($r6, $r10);
    }
    // free $p3
    return $r1;
  }
  private function parsePartial($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "partial", $this->currPos, 7, false) === 0) {
      $r4 = "partial";
      $this->currPos += 7;
    } else {
      if (!$silence) {$this->fail(20);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parsePartialDefinition($silence);
    // p <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a10($r6);
    }
    // free $p3
    return $r1;
  }
  private function parseDictionary($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "dictionary", $this->currPos, 10, false) === 0) {
      $r4 = "dictionary";
      $this->currPos += 10;
    } else {
      if (!$silence) {$this->fail(21);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseidentifier($silence);
    // name <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseInheritance($silence);
    // inh <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r9 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->parseDictionaryMembers($silence);
    // m <- $r11
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r12 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r12 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r13 = $this->discard_($silence);
    if ($r13===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r14 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r14 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r15 = $this->discard_($silence);
    if ($r15===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a11($r6, $r8, $r11);
    }
    // free $p3
    return $r1;
  }
  private function parseEnum($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "enum", $this->currPos, 4, false) === 0) {
      $r4 = "enum";
      $this->currPos += 4;
    } else {
      if (!$silence) {$this->fail(22);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseidentifier($silence);
    // name <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r8 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->parseEnumValueList($silence);
    // vals <- $r10
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r11 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r11 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r12 = $this->discard_($silence);
    if ($r12===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r13 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r13 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r14 = $this->discard_($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a12($r6, $r10);
    }
    // free $p3
    return $r1;
  }
  private function parseTypedef($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "typedef", $this->currPos, 7, false) === 0) {
      $r4 = "typedef";
      $this->currPos += 7;
    } else {
      if (!$silence) {$this->fail(23);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseTypeWithExtendedAttributes($silence);
    // t <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->parseidentifier($silence);
    // name <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->discard_($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r9 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a13($r6, $r7);
    }
    // free $p3
    return $r1;
  }
  private function parseIncludesStatement($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // target <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "includes", $this->currPos, 8, false) === 0) {
      $r6 = "includes";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(24);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discardi_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseidentifier($silence);
    // incl <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r10 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r10 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->discard_($silence);
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a14($r4, $r8);
    }
    // free $p3
    return $r1;
  }
  private function parseExtendedAttributeNoArgs($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // name <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p6 = $this->currPos;
    $r7 = $this->input[$this->currPos] ?? '';
    if ($r7 === "(" || $r7 === "=") {
      $this->currPos++;
    } else {
      $r7 = self::$FAILED;
    }
    if ($r7 === self::$FAILED) {
      $r7 = false;
    } else {
      $r7 = self::$FAILED;
      $this->currPos = $p6;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p6
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a15($r4);
    }
    // free $p3
    return $r1;
  }
  private function parseExtendedAttributeArgList($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // name <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r6 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseArgumentList($silence);
    // args <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r9 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a16($r4, $r8);
    }
    // free $p3
    return $r1;
  }
  private function parseExtendedAttributeIdent($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // name <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "=") {
      $this->currPos++;
      $r6 = "=";
    } else {
      if (!$silence) {$this->fail(27);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseExtendedAttributeRHS($silence);
    // rhs <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a17($r4, $r8);
    }
    // free $p3
    return $r1;
  }
  private function parseExtendedAttributeNamedArgList($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // name <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "=") {
      $this->currPos++;
      $r6 = "=";
    } else {
      if (!$silence) {$this->fail(27);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseidentifier($silence);
    // rhs <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r10 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r10 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->discard_($silence);
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r12 = $this->parseArgumentList($silence);
    // args <- $r12
    if ($r12===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r13 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r13 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r14 = $this->discard_($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a18($r4, $r8, $r12);
    }
    // free $p3
    return $r1;
  }
  private function discardSpecCompliantExtendedAttribute($silence) {
    // start choice_1
    // start seq_1
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r3 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r3 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r4 = $this->discard_($silence);
    if ($r4===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardExtendedAttributeInner($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r6 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r6 = self::$FAILED;
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->discardExtendedAttributeRest($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_2
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "[") {
      $this->currPos++;
      $r9 = "[";
    } else {
      if (!$silence) {$this->fail(8);}
      $r9 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r11 = $this->discardExtendedAttributeInner($silence);
    if ($r11===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    if (($this->input[$this->currPos] ?? null) === "]") {
      $this->currPos++;
      $r12 = "]";
    } else {
      if (!$silence) {$this->fail(9);}
      $r12 = self::$FAILED;
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r13 = $this->discard_($silence);
    if ($r13===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r14 = $this->discardExtendedAttributeRest($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_3
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r15 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r15 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r16 = $this->discard_($silence);
    if ($r16===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r17 = $this->discardExtendedAttributeInner($silence);
    if ($r17===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r18 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r18 = self::$FAILED;
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r19 = $this->discard_($silence);
    if ($r19===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r20 = $this->discardExtendedAttributeRest($silence);
    if ($r20===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r1 = true;
    seq_3:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_4
    $p2 = $this->currPos;
    $r21 = self::$FAILED;
    for (;;) {
      $r22 = $this->discardOther($silence);
      if ($r22!==self::$FAILED) {
        $r21 = true;
      } else {
        break;
      }
    }
    if ($r21===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_4;
    }
    // free $r22
    $r22 = $this->discardExtendedAttributeRest($silence);
    if ($r22===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_4;
    }
    $r1 = true;
    seq_4:
    // free $p2
    choice_1:
    return $r1;
  }
  private function discardi_($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p4 = $this->currPos;
    $r5 = $this->input[$this->currPos] ?? '';
    if (preg_match("/^[\\-_0-9A-Za-z]/", $r5)) {
      $this->currPos++;
    } else {
      $r5 = self::$FAILED;
    }
    if ($r5 === self::$FAILED) {
      $r5 = false;
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p4;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p4
    $r6 = $this->parse_($silence);
    // c <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a7($r6);
    }
    // free $p3
    return $r1;
  }
  private function parseCallbackRestOrInterface($silence) {
    // start choice_1
    $r1 = $this->parseCallbackRest($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "interface", $this->currPos, 9, false) === 0) {
      $r4 = "interface";
      $this->currPos += 9;
    } else {
      if (!$silence) {$this->fail(15);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseidentifier($silence);
    // name <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r8 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->parseCallbackInterfaceMembers($silence);
    // m <- $r10
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r11 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r11 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r12 = $this->discard_($silence);
    if ($r12===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r13 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r13 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r14 = $this->discard_($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a19($r6, $r10);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parseInterfaceOrMixin($silence) {
    // start choice_1
    $r1 = $this->parseInterfaceRest($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseMixinRest($silence);
    choice_1:
    return $r1;
  }
  private function parseidentifier($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p4 = $this->currPos;
    // start choice_1
    $r5 = $this->discardArgumentNameKeyword(true);
    if ($r5!==self::$FAILED) {
      goto choice_1;
    }
    $r5 = $this->discardBufferRelatedType(true);
    if ($r5!==self::$FAILED) {
      goto choice_1;
    }
    $r5 = $this->discardOtherIdLike(true);
    choice_1:
    if ($r5 === self::$FAILED) {
      $r5 = false;
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p4;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p4
    $p4 = $this->currPos;
    // start seq_2
    $p7 = $this->currPos;
    // start choice_2
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "_constructor", $this->currPos, 12, false) === 0) {
      $r8 = "_constructor";
      $this->currPos += 12;
      goto choice_2;
    } else {
      $r8 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "_toString", $this->currPos, 9, false) === 0) {
      $r8 = "_toString";
      $this->currPos += 9;
      goto choice_2;
    } else {
      $r8 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "toString", $this->currPos, 8, false) === 0) {
      $r8 = "toString";
      $this->currPos += 8;
    } else {
      $r8 = self::$FAILED;
    }
    choice_2:
    if ($r8===self::$FAILED) {
      $r6 = self::$FAILED;
      goto seq_2;
    }
    $r9 = $this->discardi_(true);
    if ($r9===self::$FAILED) {
      $this->currPos = $p7;
      $r6 = self::$FAILED;
      goto seq_2;
    }
    $r6 = true;
    seq_2:
    // free $p7
    if ($r6 === self::$FAILED) {
      $r6 = false;
    } else {
      $r6 = self::$FAILED;
      $this->currPos = $p4;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p4
    $p4 = $this->currPos;
    // start seq_3
    $p7 = $this->currPos;
    $r11 = $this->input[$this->currPos] ?? '';
    if ($r11 === "-" || $r11 === "_") {
      $this->currPos++;
    } else {
      $r11 = self::$FAILED;
      if (!$silence) {$this->fail(28);}
      $r11 = null;
    }
    $r12 = $this->input[$this->currPos] ?? '';
    if (preg_match("/^[A-Za-z]/", $r12)) {
      $this->currPos++;
    } else {
      $r12 = self::$FAILED;
      if (!$silence) {$this->fail(29);}
      $this->currPos = $p7;
      $r10 = self::$FAILED;
      goto seq_3;
    }
    for (;;) {
      $r14 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[\\-_0-9A-Za-z]/", $r14)) {
        $this->currPos++;
      } else {
        $r14 = self::$FAILED;
        if (!$silence) {$this->fail(30);}
        break;
      }
    }
    // free $r14
    $r13 = true;
    if ($r13===self::$FAILED) {
      $this->currPos = $p7;
      $r10 = self::$FAILED;
      goto seq_3;
    }
    // free $r13
    $r10 = true;
    seq_3:
    // s <- $r10
    if ($r10!==self::$FAILED) {
      $r10 = substr($this->input, $p4, $this->currPos - $p4);
    } else {
      $r10 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p7
    // free $p4
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a20($r10);
    }
    // free $p3
    return $r1;
  }
  private function parseNamespaceMembers($silence) {
    $r1 = [];
    for (;;) {
      $p3 = $this->currPos;
      // start seq_1
      $p4 = $this->currPos;
      $r5 = $this->parseleadingComments($silence);
      // c <- $r5
      if ($r5===self::$FAILED) {
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r6 = $this->parseExtendedAttributeList($silence);
      // e <- $r6
      if ($r6===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r7 = $this->parseNamespaceMember($silence);
      // m <- $r7
      if ($r7===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r2 = true;
      seq_1:
      if ($r2!==self::$FAILED) {
        $this->savedPos = $p3;
        $r2 = $this->a21($r5, $r6, $r7);
        $r1[] = $r2;
      } else {
        break;
      }
      // free $p4
    }
    // free $r2
    return $r1;
  }
  private function parsePartialDefinition($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "interface", $this->currPos, 9, false) === 0) {
      $r4 = "interface";
      $this->currPos += 9;
    } else {
      if (!$silence) {$this->fail(15);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parsePartialInterfaceOrPartialMixin($silence);
    // p <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a22($r6);
      goto choice_1;
    }
    // free $p3
    $r1 = $this->parsePartialDictionary($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseNamespace($silence);
    choice_1:
    return $r1;
  }
  private function parseInheritance($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === ":") {
      $this->currPos++;
      $r4 = ":";
    } else {
      if (!$silence) {$this->fail(31);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseidentifier($silence);
    // name <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a23($r6);
    } else {
      $r1 = null;
    }
    // free $p3
    return $r1;
  }
  private function parseDictionaryMembers($silence) {
    $r1 = [];
    for (;;) {
      $p3 = $this->currPos;
      // start seq_1
      $p4 = $this->currPos;
      $r5 = $this->parseleadingComments($silence);
      // c <- $r5
      if ($r5===self::$FAILED) {
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r6 = $this->parseExtendedAttributeList($silence);
      // e <- $r6
      if ($r6===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r7 = $this->parseDictionaryMemberRest($silence);
      // m <- $r7
      if ($r7===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r2 = true;
      seq_1:
      if ($r2!==self::$FAILED) {
        $this->savedPos = $p3;
        $r2 = $this->a21($r5, $r6, $r7);
        $r1[] = $r2;
      } else {
        break;
      }
      // free $p4
    }
    // free $r2
    return $r1;
  }
  private function parseEnumValueList($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsestring($silence);
    // s <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseEnumValueListComma($silence);
    // vals <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a24($r4, $r6);
    }
    // free $p3
    return $r1;
  }
  private function parseTypeWithExtendedAttributes($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseExtendedAttributeList($silence);
    // e <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseType($silence);
    // t <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a25($r4, $r5);
    }
    // free $p3
    return $r1;
  }
  private function parseArgumentList($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseArgument($silence);
    // a <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseArguments($silence);
    // rest <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a26($r4, $r5);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    $r1 = '';
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a5();
    }
    choice_1:
    return $r1;
  }
  private function parseExtendedAttributeRHS($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // rhs <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p6 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r7 = "(";
    } else {
      $r7 = self::$FAILED;
    }
    if ($r7 === self::$FAILED) {
      $r7 = false;
    } else {
      $r7 = self::$FAILED;
      $this->currPos = $p6;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p6
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a27($r4);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p6 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r8 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r8 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p6;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r10 = $this->parseidentifier($silence);
    // s <- $r10
    if ($r10===self::$FAILED) {
      $this->currPos = $p6;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r11 = $this->discard_($silence);
    if ($r11===self::$FAILED) {
      $this->currPos = $p6;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r12 = [];
    for (;;) {
      $p14 = $this->currPos;
      // start seq_3
      $p15 = $this->currPos;
      if (($this->input[$this->currPos] ?? null) === ",") {
        $this->currPos++;
        $r16 = ",";
      } else {
        if (!$silence) {$this->fail(13);}
        $r16 = self::$FAILED;
        $r13 = self::$FAILED;
        goto seq_3;
      }
      $r17 = $this->discard_($silence);
      if ($r17===self::$FAILED) {
        $this->currPos = $p15;
        $r13 = self::$FAILED;
        goto seq_3;
      }
      $r18 = $this->parseidentifier($silence);
      // s2 <- $r18
      if ($r18===self::$FAILED) {
        $this->currPos = $p15;
        $r13 = self::$FAILED;
        goto seq_3;
      }
      $r19 = $this->discard_($silence);
      if ($r19===self::$FAILED) {
        $this->currPos = $p15;
        $r13 = self::$FAILED;
        goto seq_3;
      }
      $r13 = true;
      seq_3:
      if ($r13!==self::$FAILED) {
        $this->savedPos = $p14;
        $r13 = $this->a28($r10, $r18);
        $r12[] = $r13;
      } else {
        break;
      }
      // free $p15
    }
    // rest <- $r12
    // free $r13
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r13 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r13 = self::$FAILED;
      $this->currPos = $p6;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r20 = $this->discard_($silence);
    if ($r20===self::$FAILED) {
      $this->currPos = $p6;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a29($r10, $r12);
      goto choice_1;
    }
    // free $p6
    $p6 = $this->currPos;
    // start seq_4
    $p15 = $this->currPos;
    $p22 = $this->currPos;
    $r21 = $this->discardstring($silence);
    // s <- $r21
    if ($r21!==self::$FAILED) {
      $r21 = substr($this->input, $p22, $this->currPos - $p22);
    } else {
      $r21 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_4;
    }
    // free $p22
    $r23 = $this->discard_($silence);
    if ($r23===self::$FAILED) {
      $this->currPos = $p15;
      $r1 = self::$FAILED;
      goto seq_4;
    }
    $r1 = true;
    seq_4:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p6;
      $r1 = $this->a30($r21);
      goto choice_1;
    }
    // free $p15
    $p15 = $this->currPos;
    // start seq_5
    $p22 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r24 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r24 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_5;
    }
    $r25 = $this->discard_($silence);
    if ($r25===self::$FAILED) {
      $this->currPos = $p22;
      $r1 = self::$FAILED;
      goto seq_5;
    }
    $p27 = $this->currPos;
    $r26 = $this->discardstring($silence);
    // s <- $r26
    if ($r26!==self::$FAILED) {
      $r26 = substr($this->input, $p27, $this->currPos - $p27);
    } else {
      $r26 = self::$FAILED;
      $this->currPos = $p22;
      $r1 = self::$FAILED;
      goto seq_5;
    }
    // free $p27
    $r28 = $this->discard_($silence);
    if ($r28===self::$FAILED) {
      $this->currPos = $p22;
      $r1 = self::$FAILED;
      goto seq_5;
    }
    $r29 = [];
    for (;;) {
      $p27 = $this->currPos;
      // start seq_6
      $p31 = $this->currPos;
      if (($this->input[$this->currPos] ?? null) === ",") {
        $this->currPos++;
        $r32 = ",";
      } else {
        if (!$silence) {$this->fail(13);}
        $r32 = self::$FAILED;
        $r30 = self::$FAILED;
        goto seq_6;
      }
      $r33 = $this->discard_($silence);
      if ($r33===self::$FAILED) {
        $this->currPos = $p31;
        $r30 = self::$FAILED;
        goto seq_6;
      }
      $p35 = $this->currPos;
      $r34 = $this->discardstring($silence);
      // s2 <- $r34
      if ($r34!==self::$FAILED) {
        $r34 = substr($this->input, $p35, $this->currPos - $p35);
      } else {
        $r34 = self::$FAILED;
        $this->currPos = $p31;
        $r30 = self::$FAILED;
        goto seq_6;
      }
      // free $p35
      $r36 = $this->discard_($silence);
      if ($r36===self::$FAILED) {
        $this->currPos = $p31;
        $r30 = self::$FAILED;
        goto seq_6;
      }
      $r30 = true;
      seq_6:
      if ($r30!==self::$FAILED) {
        $this->savedPos = $p27;
        $r30 = $this->a28($r26, $r34);
        $r29[] = $r30;
      } else {
        break;
      }
      // free $p31
    }
    // rest <- $r29
    // free $r30
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r30 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r30 = self::$FAILED;
      $this->currPos = $p22;
      $r1 = self::$FAILED;
      goto seq_5;
    }
    $r37 = $this->discard_($silence);
    if ($r37===self::$FAILED) {
      $this->currPos = $p22;
      $r1 = self::$FAILED;
      goto seq_5;
    }
    $r1 = true;
    seq_5:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p15;
      $r1 = $this->a31($r26, $r29);
      goto choice_1;
    }
    // free $p22
    $p22 = $this->currPos;
    // start seq_7
    $p31 = $this->currPos;
    $p35 = $this->currPos;
    $r38 = $this->discardinteger($silence);
    // s <- $r38
    if ($r38!==self::$FAILED) {
      $r38 = substr($this->input, $p35, $this->currPos - $p35);
    } else {
      $r38 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_7;
    }
    // free $p35
    $r39 = $this->discard_($silence);
    if ($r39===self::$FAILED) {
      $this->currPos = $p31;
      $r1 = self::$FAILED;
      goto seq_7;
    }
    $p35 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === ".") {
      $this->currPos++;
      $r40 = ".";
    } else {
      $r40 = self::$FAILED;
    }
    if ($r40 === self::$FAILED) {
      $r40 = false;
    } else {
      $r40 = self::$FAILED;
      $this->currPos = $p35;
      $this->currPos = $p31;
      $r1 = self::$FAILED;
      goto seq_7;
    }
    // free $p35
    $r1 = true;
    seq_7:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p22;
      $r1 = $this->a32($r38);
      goto choice_1;
    }
    // free $p31
    $p31 = $this->currPos;
    // start seq_8
    $p35 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r41 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r41 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_8;
    }
    $r42 = $this->discard_($silence);
    if ($r42===self::$FAILED) {
      $this->currPos = $p35;
      $r1 = self::$FAILED;
      goto seq_8;
    }
    $p44 = $this->currPos;
    $r43 = $this->discardinteger($silence);
    // s <- $r43
    if ($r43!==self::$FAILED) {
      $r43 = substr($this->input, $p44, $this->currPos - $p44);
    } else {
      $r43 = self::$FAILED;
      $this->currPos = $p35;
      $r1 = self::$FAILED;
      goto seq_8;
    }
    // free $p44
    $r45 = $this->discard_($silence);
    if ($r45===self::$FAILED) {
      $this->currPos = $p35;
      $r1 = self::$FAILED;
      goto seq_8;
    }
    $r46 = [];
    for (;;) {
      $p44 = $this->currPos;
      // start seq_9
      $p48 = $this->currPos;
      if (($this->input[$this->currPos] ?? null) === ",") {
        $this->currPos++;
        $r49 = ",";
      } else {
        if (!$silence) {$this->fail(13);}
        $r49 = self::$FAILED;
        $r47 = self::$FAILED;
        goto seq_9;
      }
      $r50 = $this->discard_($silence);
      if ($r50===self::$FAILED) {
        $this->currPos = $p48;
        $r47 = self::$FAILED;
        goto seq_9;
      }
      $p52 = $this->currPos;
      $r51 = $this->discardinteger($silence);
      // s2 <- $r51
      if ($r51!==self::$FAILED) {
        $r51 = substr($this->input, $p52, $this->currPos - $p52);
      } else {
        $r51 = self::$FAILED;
        $this->currPos = $p48;
        $r47 = self::$FAILED;
        goto seq_9;
      }
      // free $p52
      $r53 = $this->discard_($silence);
      if ($r53===self::$FAILED) {
        $this->currPos = $p48;
        $r47 = self::$FAILED;
        goto seq_9;
      }
      $r47 = true;
      seq_9:
      if ($r47!==self::$FAILED) {
        $this->savedPos = $p44;
        $r47 = $this->a28($r43, $r51);
        $r46[] = $r47;
      } else {
        break;
      }
      // free $p48
    }
    // rest <- $r46
    // free $r47
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r47 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r47 = self::$FAILED;
      $this->currPos = $p35;
      $r1 = self::$FAILED;
      goto seq_8;
    }
    $r54 = $this->discard_($silence);
    if ($r54===self::$FAILED) {
      $this->currPos = $p35;
      $r1 = self::$FAILED;
      goto seq_8;
    }
    $r1 = true;
    seq_8:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p31;
      $r1 = $this->a33($r43, $r46);
      goto choice_1;
    }
    // free $p35
    $p35 = $this->currPos;
    // start seq_10
    $p48 = $this->currPos;
    $p52 = $this->currPos;
    $r55 = $this->discarddecimal($silence);
    // s <- $r55
    if ($r55!==self::$FAILED) {
      $r55 = substr($this->input, $p52, $this->currPos - $p52);
    } else {
      $r55 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_10;
    }
    // free $p52
    $r56 = $this->discard_($silence);
    if ($r56===self::$FAILED) {
      $this->currPos = $p48;
      $r1 = self::$FAILED;
      goto seq_10;
    }
    $r1 = true;
    seq_10:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p35;
      $r1 = $this->a34($r55);
      goto choice_1;
    }
    // free $p48
    $p48 = $this->currPos;
    // start seq_11
    $p52 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r57 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r57 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_11;
    }
    $r58 = $this->discard_($silence);
    if ($r58===self::$FAILED) {
      $this->currPos = $p52;
      $r1 = self::$FAILED;
      goto seq_11;
    }
    $p60 = $this->currPos;
    $r59 = $this->discarddecimal($silence);
    // s <- $r59
    if ($r59!==self::$FAILED) {
      $r59 = substr($this->input, $p60, $this->currPos - $p60);
    } else {
      $r59 = self::$FAILED;
      $this->currPos = $p52;
      $r1 = self::$FAILED;
      goto seq_11;
    }
    // free $p60
    $r61 = $this->discard_($silence);
    if ($r61===self::$FAILED) {
      $this->currPos = $p52;
      $r1 = self::$FAILED;
      goto seq_11;
    }
    $r62 = [];
    for (;;) {
      $p60 = $this->currPos;
      // start seq_12
      $p64 = $this->currPos;
      if (($this->input[$this->currPos] ?? null) === ",") {
        $this->currPos++;
        $r65 = ",";
      } else {
        if (!$silence) {$this->fail(13);}
        $r65 = self::$FAILED;
        $r63 = self::$FAILED;
        goto seq_12;
      }
      $r66 = $this->discard_($silence);
      if ($r66===self::$FAILED) {
        $this->currPos = $p64;
        $r63 = self::$FAILED;
        goto seq_12;
      }
      $p68 = $this->currPos;
      $r67 = $this->discarddecimal($silence);
      // s2 <- $r67
      if ($r67!==self::$FAILED) {
        $r67 = substr($this->input, $p68, $this->currPos - $p68);
      } else {
        $r67 = self::$FAILED;
        $this->currPos = $p64;
        $r63 = self::$FAILED;
        goto seq_12;
      }
      // free $p68
      $r69 = $this->discard_($silence);
      if ($r69===self::$FAILED) {
        $this->currPos = $p64;
        $r63 = self::$FAILED;
        goto seq_12;
      }
      $r63 = true;
      seq_12:
      if ($r63!==self::$FAILED) {
        $this->savedPos = $p60;
        $r63 = $this->a28($r59, $r67);
        $r62[] = $r63;
      } else {
        break;
      }
      // free $p64
    }
    // rest <- $r62
    // free $r63
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r63 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r63 = self::$FAILED;
      $this->currPos = $p52;
      $r1 = self::$FAILED;
      goto seq_11;
    }
    $r70 = $this->discard_($silence);
    if ($r70===self::$FAILED) {
      $this->currPos = $p52;
      $r1 = self::$FAILED;
      goto seq_11;
    }
    $r1 = true;
    seq_11:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p48;
      $r1 = $this->a35($r59, $r62);
    }
    // free $p52
    choice_1:
    return $r1;
  }
  private function discardExtendedAttributeInner($silence) {
    // start choice_1
    // start seq_1
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r3 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r3 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r4 = $this->discard_($silence);
    if ($r4===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardExtendedAttributeInner($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r6 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r6 = self::$FAILED;
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->discardExtendedAttributeInner($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_2
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "[") {
      $this->currPos++;
      $r9 = "[";
    } else {
      if (!$silence) {$this->fail(8);}
      $r9 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r11 = $this->discardExtendedAttributeInner($silence);
    if ($r11===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    if (($this->input[$this->currPos] ?? null) === "]") {
      $this->currPos++;
      $r12 = "]";
    } else {
      if (!$silence) {$this->fail(9);}
      $r12 = self::$FAILED;
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r13 = $this->discard_($silence);
    if ($r13===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r14 = $this->discardExtendedAttributeInner($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_3
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r15 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r15 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r16 = $this->discard_($silence);
    if ($r16===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r17 = $this->discardExtendedAttributeInner($silence);
    if ($r17===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r18 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r18 = self::$FAILED;
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r19 = $this->discard_($silence);
    if ($r19===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r20 = $this->discardExtendedAttributeInner($silence);
    if ($r20===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r1 = true;
    seq_3:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_4
    $p2 = $this->currPos;
    $r21 = $this->discardOtherOrComma($silence);
    if ($r21===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_4;
    }
    $r22 = $this->discardExtendedAttributeInner($silence);
    if ($r22===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_4;
    }
    $r1 = true;
    seq_4:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    $r1 = '';
    choice_1:
    return $r1;
  }
  private function discardExtendedAttributeRest($silence) {
    $r1 = $this->discardSpecCompliantExtendedAttribute($silence);
    if ($r1===self::$FAILED) {
      $r1 = null;
    }
    return $r1;
  }
  private function discardOther($silence) {
    // start choice_1
    $r1 = $this->discardOtherIdLike($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->discardArgumentNameKeyword($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->discardBufferRelatedType($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_2
    $r4 = $this->discardinteger($silence);
    if ($r4!==self::$FAILED) {
      goto choice_2;
    }
    $r4 = $this->discarddecimal($silence);
    if ($r4!==self::$FAILED) {
      goto choice_2;
    }
    $r4 = $this->discardidentifier($silence);
    if ($r4!==self::$FAILED) {
      goto choice_2;
    }
    $r4 = $this->discardstring($silence);
    if ($r4!==self::$FAILED) {
      goto choice_2;
    }
    $r4 = $this->discardotherchar($silence);
    if ($r4!==self::$FAILED) {
      goto choice_2;
    }
    $r4 = $this->discardotherterminals($silence);
    choice_2:
    // c <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discard_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a7($r4);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parse_($silence) {
    $p2 = $this->currPos;
    $r3 = [];
    for (;;) {
      // start choice_1
      $p5 = $this->currPos;
      $r4 = $this->discardwhitespace($silence);
      if ($r4!==self::$FAILED) {
        $r4 = substr($this->input, $p5, $this->currPos - $p5);
        goto choice_1;
      } else {
        $r4 = self::$FAILED;
      }
      // free $p5
      $p5 = $this->currPos;
      $r4 = $this->discardcomment($silence);
      if ($r4!==self::$FAILED) {
        $r4 = substr($this->input, $p5, $this->currPos - $p5);
      } else {
        $r4 = self::$FAILED;
      }
      // free $p5
      choice_1:
      if ($r4!==self::$FAILED) {
        $r3[] = $r4;
      } else {
        break;
      }
    }
    // r <- $r3
    // free $r4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a1($r3);
    }
    return $r1;
  }
  private function parseCallbackRest($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // name <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "=") {
      $this->currPos++;
      $r6 = "=";
    } else {
      if (!$silence) {$this->fail(27);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseType($silence);
    // t <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r9 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->parseArgumentList($silence);
    // args <- $r11
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r12 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r12 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r13 = $this->discard_($silence);
    if ($r13===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r14 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r14 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r15 = $this->discard_($silence);
    if ($r15===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a36($r4, $r8, $r11);
    }
    // free $p3
    return $r1;
  }
  private function parseCallbackInterfaceMembers($silence) {
    $r1 = [];
    for (;;) {
      $p3 = $this->currPos;
      // start seq_1
      $p4 = $this->currPos;
      $r5 = $this->parseleadingComments($silence);
      // c <- $r5
      if ($r5===self::$FAILED) {
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r6 = $this->parseExtendedAttributeList($silence);
      // e <- $r6
      if ($r6===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r7 = $this->parseCallbackInterfaceMember($silence);
      // m <- $r7
      if ($r7===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r2 = true;
      seq_1:
      if ($r2!==self::$FAILED) {
        $this->savedPos = $p3;
        $r2 = $this->a21($r5, $r6, $r7);
        $r1[] = $r2;
      } else {
        break;
      }
      // free $p4
    }
    // free $r2
    return $r1;
  }
  private function parseInterfaceRest($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // name <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseInheritance($silence);
    // inh <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r7 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r7 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->discard_($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->parseInterfaceMembers($silence);
    // m <- $r9
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r10 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r10 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->discard_($silence);
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r12 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r12 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r13 = $this->parsec_($silence);
    // c <- $r13
    if ($r13===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a37($r4, $r6, $r9, $r13);
    }
    // free $p3
    return $r1;
  }
  private function parseMixinRest($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "mixin", $this->currPos, 5, false) === 0) {
      $r4 = "mixin";
      $this->currPos += 5;
    } else {
      if (!$silence) {$this->fail(32);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseidentifier($silence);
    // name <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r8 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->parseMixinMembers($silence);
    // m <- $r10
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r11 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r11 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r12 = $this->discard_($silence);
    if ($r12===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r13 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r13 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r14 = $this->discard_($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a38($r6, $r10);
    }
    // free $p3
    return $r1;
  }
  private function discardArgumentNameKeyword($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "async", $this->currPos, 5, false) === 0) {
      $r4 = "async";
      $this->currPos += 5;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(33);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "attribute", $this->currPos, 9, false) === 0) {
      $r4 = "attribute";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(34);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "callback", $this->currPos, 8, false) === 0) {
      $r4 = "callback";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(14);}
      $r4 = self::$FAILED;
    }
    // start seq_2
    $p6 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "const", $this->currPos, 5, false) === 0) {
      $r7 = "const";
      $this->currPos += 5;
    } else {
      if (!$silence) {$this->fail(35);}
      $r7 = self::$FAILED;
      $r4 = self::$FAILED;
      goto seq_2;
    }
    $p8 = $this->currPos;
    $r9 = $this->input[$this->currPos] ?? '';
    if (preg_match("/^[\\-_0-9A-Za-z]/", $r9)) {
      $this->currPos++;
    } else {
      $r9 = self::$FAILED;
    }
    if ($r9 === self::$FAILED) {
      $r9 = false;
    } else {
      $r9 = self::$FAILED;
      $this->currPos = $p8;
      $this->currPos = $p6;
      $r4 = self::$FAILED;
      goto seq_2;
    }
    // free $p8
    $r4 = true;
    seq_2:
    if ($r4!==self::$FAILED) {
      goto choice_1;
    }
    // free $p6
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "constructor", $this->currPos, 11, false) === 0) {
      $r4 = "constructor";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(36);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "deleter", $this->currPos, 7, false) === 0) {
      $r4 = "deleter";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(37);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "dictionary", $this->currPos, 10, false) === 0) {
      $r4 = "dictionary";
      $this->currPos += 10;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(21);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "enum", $this->currPos, 4, false) === 0) {
      $r4 = "enum";
      $this->currPos += 4;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(22);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "getter", $this->currPos, 6, false) === 0) {
      $r4 = "getter";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(38);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "includes", $this->currPos, 8, false) === 0) {
      $r4 = "includes";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(24);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "inherit", $this->currPos, 7, false) === 0) {
      $r4 = "inherit";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(39);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "interface", $this->currPos, 9, false) === 0) {
      $r4 = "interface";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(15);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "iterable", $this->currPos, 8, false) === 0) {
      $r4 = "iterable";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(40);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "maplike", $this->currPos, 7, false) === 0) {
      $r4 = "maplike";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(41);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "mixin", $this->currPos, 5, false) === 0) {
      $r4 = "mixin";
      $this->currPos += 5;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(32);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "namespace", $this->currPos, 9, false) === 0) {
      $r4 = "namespace";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(16);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "partial", $this->currPos, 7, false) === 0) {
      $r4 = "partial";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(20);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "readonly", $this->currPos, 8, false) === 0) {
      $r4 = "readonly";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(42);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "required", $this->currPos, 8, false) === 0) {
      $r4 = "required";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(43);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "setlike", $this->currPos, 7, false) === 0) {
      $r4 = "setlike";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(44);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "setter", $this->currPos, 6, false) === 0) {
      $r4 = "setter";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(45);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "static", $this->currPos, 6, false) === 0) {
      $r4 = "static";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(46);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "stringifier", $this->currPos, 11, false) === 0) {
      $r4 = "stringifier";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(47);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "typedef", $this->currPos, 7, false) === 0) {
      $r4 = "typedef";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(23);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "unrestricted", $this->currPos, 12, false) === 0) {
      $r4 = "unrestricted";
      $this->currPos += 12;
    } else {
      if (!$silence) {$this->fail(48);}
      $r4 = self::$FAILED;
    }
    choice_1:
    // k <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r10 = $this->discardi_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a39($r4);
    }
    // free $p3
    return $r1;
  }
  private function discardBufferRelatedType($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "ArrayBuffer", $this->currPos, 11, false) === 0) {
      $r4 = "ArrayBuffer";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(49);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "DataView", $this->currPos, 8, false) === 0) {
      $r4 = "DataView";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(50);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Int8Array", $this->currPos, 9, false) === 0) {
      $r4 = "Int8Array";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(51);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Int16Array", $this->currPos, 10, false) === 0) {
      $r4 = "Int16Array";
      $this->currPos += 10;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(52);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Int32Array", $this->currPos, 10, false) === 0) {
      $r4 = "Int32Array";
      $this->currPos += 10;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(53);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Uint8Array", $this->currPos, 10, false) === 0) {
      $r4 = "Uint8Array";
      $this->currPos += 10;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(54);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Uint16Array", $this->currPos, 11, false) === 0) {
      $r4 = "Uint16Array";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(55);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Uint32Array", $this->currPos, 11, false) === 0) {
      $r4 = "Uint32Array";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(56);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Uint8ClampedArray", $this->currPos, 17, false) === 0) {
      $r4 = "Uint8ClampedArray";
      $this->currPos += 17;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(57);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Float32Array", $this->currPos, 12, false) === 0) {
      $r4 = "Float32Array";
      $this->currPos += 12;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(58);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Float64Array", $this->currPos, 12, false) === 0) {
      $r4 = "Float64Array";
      $this->currPos += 12;
    } else {
      if (!$silence) {$this->fail(59);}
      $r4 = self::$FAILED;
    }
    choice_1:
    // s <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discardi_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a40($r4);
    }
    // free $p3
    return $r1;
  }
  private function discardOtherIdLike($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "ByteString", $this->currPos, 10, false) === 0) {
      $r4 = "ByteString";
      $this->currPos += 10;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(60);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "DOMString", $this->currPos, 9, false) === 0) {
      $r4 = "DOMString";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(61);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "FrozenArray", $this->currPos, 11, false) === 0) {
      $r4 = "FrozenArray";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(62);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Infinity", $this->currPos, 8, false) === 0) {
      $r4 = "Infinity";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(63);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "NaN", $this->currPos, 3, false) === 0) {
      $r4 = "NaN";
      $this->currPos += 3;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(64);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "ObservableArray", $this->currPos, 15, false) === 0) {
      $r4 = "ObservableArray";
      $this->currPos += 15;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(65);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Promise", $this->currPos, 7, false) === 0) {
      $r4 = "Promise";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(66);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "USVString", $this->currPos, 9, false) === 0) {
      $r4 = "USVString";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(67);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "any", $this->currPos, 3, false) === 0) {
      $r4 = "any";
      $this->currPos += 3;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(68);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "bigint", $this->currPos, 6, false) === 0) {
      $r4 = "bigint";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(69);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "boolean", $this->currPos, 7, false) === 0) {
      $r4 = "boolean";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(70);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "byte", $this->currPos, 4, false) === 0) {
      $r4 = "byte";
      $this->currPos += 4;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(71);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "double", $this->currPos, 6, false) === 0) {
      $r4 = "double";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(72);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "false", $this->currPos, 5, false) === 0) {
      $r4 = "false";
      $this->currPos += 5;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(73);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "float", $this->currPos, 5, false) === 0) {
      $r4 = "float";
      $this->currPos += 5;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(74);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "long", $this->currPos, 4, false) === 0) {
      $r4 = "long";
      $this->currPos += 4;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(75);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "null", $this->currPos, 4, false) === 0) {
      $r4 = "null";
      $this->currPos += 4;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(76);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "object", $this->currPos, 6, false) === 0) {
      $r4 = "object";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(77);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "octet", $this->currPos, 5, false) === 0) {
      $r4 = "octet";
      $this->currPos += 5;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(78);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "or", $this->currPos, 2, false) === 0) {
      $r4 = "or";
      $this->currPos += 2;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(79);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "optional", $this->currPos, 8, false) === 0) {
      $r4 = "optional";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(80);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "record", $this->currPos, 6, false) === 0) {
      $r4 = "record";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(81);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "sequence", $this->currPos, 8, false) === 0) {
      $r4 = "sequence";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(82);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "short", $this->currPos, 5, false) === 0) {
      $r4 = "short";
      $this->currPos += 5;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(83);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "symbol", $this->currPos, 6, false) === 0) {
      $r4 = "symbol";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(84);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "true", $this->currPos, 4, false) === 0) {
      $r4 = "true";
      $this->currPos += 4;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(85);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "unsigned", $this->currPos, 8, false) === 0) {
      $r4 = "unsigned";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(86);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "undefined", $this->currPos, 9, false) === 0) {
      $r4 = "undefined";
      $this->currPos += 9;
    } else {
      if (!$silence) {$this->fail(87);}
      $r4 = self::$FAILED;
    }
    choice_1:
    // c <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discardi_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a7($r4);
    }
    // free $p3
    return $r1;
  }
  private function parseNamespaceMember($silence) {
    // start choice_1
    $r1 = $this->parseRegularOperation($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "readonly", $this->currPos, 8, false) === 0) {
      $r4 = "readonly";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(42);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseAttributeRest($silence);
    // a <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a41($r6);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parsePartialInterfaceOrPartialMixin($silence) {
    // start choice_1
    $r1 = $this->parsePartialInterfaceRest($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseMixinRest($silence);
    choice_1:
    return $r1;
  }
  private function parsePartialDictionary($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "dictionary", $this->currPos, 10, false) === 0) {
      $r4 = "dictionary";
      $this->currPos += 10;
    } else {
      if (!$silence) {$this->fail(21);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseidentifier($silence);
    // name <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r8 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->parseDictionaryMembers($silence);
    // m <- $r10
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r11 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r11 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r12 = $this->discard_($silence);
    if ($r12===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r13 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r13 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r14 = $this->discard_($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a42($r6, $r10);
    }
    // free $p3
    return $r1;
  }
  private function parseDictionaryMemberRest($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "required", $this->currPos, 8, false) === 0) {
      $r4 = "required";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(43);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseTypeWithExtendedAttributes($silence);
    // t <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->parseidentifier($silence);
    // name <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->discard_($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r9 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a43($r6, $r7);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p11 = $this->currPos;
    $r12 = $this->parseType($silence);
    // t <- $r12
    if ($r12===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r13 = $this->parseidentifier($silence);
    // name <- $r13
    if ($r13===self::$FAILED) {
      $this->currPos = $p11;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r14 = $this->discard_($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p11;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r15 = $this->parseDefault($silence);
    // d <- $r15
    if ($r15===self::$FAILED) {
      $this->currPos = $p11;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r16 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r16 = self::$FAILED;
      $this->currPos = $p11;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r17 = $this->discard_($silence);
    if ($r17===self::$FAILED) {
      $this->currPos = $p11;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a44($r12, $r13, $r15);
    }
    // free $p11
    choice_1:
    return $r1;
  }
  private function parsestring($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "\"") {
      $this->currPos++;
      $r4 = "\"";
    } else {
      if (!$silence) {$this->fail(88);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p6 = $this->currPos;
    for (;;) {
      $r7 = self::charAt($this->input, $this->currPos);
      if ($r7 !== '' && !($r7 === "\"")) {
        $this->currPos += strlen($r7);
      } else {
        $r7 = self::$FAILED;
        if (!$silence) {$this->fail(89);}
        break;
      }
    }
    // free $r7
    $r5 = true;
    // s <- $r5
    if ($r5!==self::$FAILED) {
      $r5 = substr($this->input, $p6, $this->currPos - $p6);
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p6
    if (($this->input[$this->currPos] ?? null) === "\"") {
      $this->currPos++;
      $r7 = "\"";
    } else {
      if (!$silence) {$this->fail(88);}
      $r7 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a40($r5);
    }
    // free $p3
    return $r1;
  }
  private function parseEnumValueListComma($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === ",") {
      $this->currPos++;
      $r4 = ",";
    } else {
      if (!$silence) {$this->fail(13);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseEnumValueListString($silence);
    // vals <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a45($r6);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    $r1 = '';
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a5();
    }
    choice_1:
    return $r1;
  }
  private function parseType($silence) {
    // start choice_1
    $r1 = $this->parseSingleType($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseUnionType($silence);
    // t <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseNull($silence);
    // n <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a46($r4, $r5);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parseArgument($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseExtendedAttributeList($silence);
    // e <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseArgumentRest($silence);
    // a <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a47($r4, $r5);
    }
    // free $p3
    return $r1;
  }
  private function parseArguments($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === ",") {
      $this->currPos++;
      $r4 = ",";
    } else {
      if (!$silence) {$this->fail(13);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseArgument($silence);
    // a <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->parseArguments($silence);
    // rest <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a26($r6, $r7);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    $r1 = '';
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a5();
    }
    choice_1:
    return $r1;
  }
  private function discardstring($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "\"") {
      $this->currPos++;
      $r4 = "\"";
    } else {
      if (!$silence) {$this->fail(88);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p6 = $this->currPos;
    for (;;) {
      $r7 = self::charAt($this->input, $this->currPos);
      if ($r7 !== '' && !($r7 === "\"")) {
        $this->currPos += strlen($r7);
      } else {
        $r7 = self::$FAILED;
        if (!$silence) {$this->fail(89);}
        break;
      }
    }
    // free $r7
    $r5 = true;
    // s <- $r5
    if ($r5!==self::$FAILED) {
      $r5 = substr($this->input, $p6, $this->currPos - $p6);
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p6
    if (($this->input[$this->currPos] ?? null) === "\"") {
      $this->currPos++;
      $r7 = "\"";
    } else {
      if (!$silence) {$this->fail(88);}
      $r7 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a40($r5);
    }
    // free $p3
    return $r1;
  }
  private function discardinteger($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseopt_minus($silence);
    // m <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // start choice_1
    $r5 = $this->parsedecimal_integer($silence);
    if ($r5!==self::$FAILED) {
      goto choice_1;
    }
    $r5 = $this->parsehex_integer($silence);
    if ($r5!==self::$FAILED) {
      goto choice_1;
    }
    $r5 = $this->parseoctal_integer($silence);
    choice_1:
    // n <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a48($r4, $r5);
    }
    // free $p3
    return $r1;
  }
  private function discarddecimal($silence) {
    $p2 = $this->currPos;
    $p4 = $this->currPos;
    // start seq_1
    $p5 = $this->currPos;
    $r6 = $this->parseopt_minus($silence);
    // m <- $r6
    if ($r6===self::$FAILED) {
      $r3 = self::$FAILED;
      goto seq_1;
    }
    // start choice_1
    // start seq_2
    $p8 = $this->currPos;
    // start choice_2
    // start seq_3
    $p10 = $this->currPos;
    $r11 = self::$FAILED;
    for (;;) {
      $r12 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-9]/", $r12)) {
        $this->currPos++;
        $r11 = true;
      } else {
        $r12 = self::$FAILED;
        if (!$silence) {$this->fail(90);}
        break;
      }
    }
    if ($r11===self::$FAILED) {
      $r9 = self::$FAILED;
      goto seq_3;
    }
    // free $r12
    if (($this->input[$this->currPos] ?? null) === ".") {
      $this->currPos++;
      $r12 = ".";
    } else {
      if (!$silence) {$this->fail(91);}
      $r12 = self::$FAILED;
      $this->currPos = $p10;
      $r9 = self::$FAILED;
      goto seq_3;
    }
    for (;;) {
      $r14 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-9]/", $r14)) {
        $this->currPos++;
      } else {
        $r14 = self::$FAILED;
        if (!$silence) {$this->fail(90);}
        break;
      }
    }
    // free $r14
    $r13 = true;
    if ($r13===self::$FAILED) {
      $this->currPos = $p10;
      $r9 = self::$FAILED;
      goto seq_3;
    }
    // free $r13
    $r9 = true;
    seq_3:
    if ($r9!==self::$FAILED) {
      goto choice_2;
    }
    // free $p10
    // start seq_4
    $p10 = $this->currPos;
    for (;;) {
      $r14 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-9]/", $r14)) {
        $this->currPos++;
      } else {
        $r14 = self::$FAILED;
        if (!$silence) {$this->fail(90);}
        break;
      }
    }
    // free $r14
    $r13 = true;
    if ($r13===self::$FAILED) {
      $r9 = self::$FAILED;
      goto seq_4;
    }
    // free $r13
    if (($this->input[$this->currPos] ?? null) === ".") {
      $this->currPos++;
      $r13 = ".";
    } else {
      if (!$silence) {$this->fail(91);}
      $r13 = self::$FAILED;
      $this->currPos = $p10;
      $r9 = self::$FAILED;
      goto seq_4;
    }
    $r14 = self::$FAILED;
    for (;;) {
      $r15 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-9]/", $r15)) {
        $this->currPos++;
        $r14 = true;
      } else {
        $r15 = self::$FAILED;
        if (!$silence) {$this->fail(90);}
        break;
      }
    }
    if ($r14===self::$FAILED) {
      $this->currPos = $p10;
      $r9 = self::$FAILED;
      goto seq_4;
    }
    // free $r15
    $r9 = true;
    seq_4:
    // free $p10
    choice_2:
    if ($r9===self::$FAILED) {
      $r7 = self::$FAILED;
      goto seq_2;
    }
    // start seq_5
    $p10 = $this->currPos;
    $r16 = $this->input[$this->currPos] ?? '';
    if ($r16 === "E" || $r16 === "e") {
      $this->currPos++;
    } else {
      $r16 = self::$FAILED;
      if (!$silence) {$this->fail(92);}
      $r15 = self::$FAILED;
      goto seq_5;
    }
    $r17 = $this->input[$this->currPos] ?? '';
    if ($r17 === "+" || $r17 === "-") {
      $this->currPos++;
    } else {
      $r17 = self::$FAILED;
      if (!$silence) {$this->fail(93);}
      $r17 = null;
    }
    $r18 = self::$FAILED;
    for (;;) {
      $r19 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-9]/", $r19)) {
        $this->currPos++;
        $r18 = true;
      } else {
        $r19 = self::$FAILED;
        if (!$silence) {$this->fail(90);}
        break;
      }
    }
    if ($r18===self::$FAILED) {
      $this->currPos = $p10;
      $r15 = self::$FAILED;
      goto seq_5;
    }
    // free $r19
    $r15 = true;
    seq_5:
    if ($r15===self::$FAILED) {
      $r15 = null;
    }
    // free $p10
    $r7 = true;
    seq_2:
    if ($r7!==self::$FAILED) {
      goto choice_1;
    }
    // free $p8
    // start seq_6
    $p8 = $this->currPos;
    $r19 = self::$FAILED;
    for (;;) {
      $r20 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-9]/", $r20)) {
        $this->currPos++;
        $r19 = true;
      } else {
        $r20 = self::$FAILED;
        if (!$silence) {$this->fail(90);}
        break;
      }
    }
    if ($r19===self::$FAILED) {
      $r7 = self::$FAILED;
      goto seq_6;
    }
    // free $r20
    $r20 = $this->input[$this->currPos] ?? '';
    if ($r20 === "E" || $r20 === "e") {
      $this->currPos++;
    } else {
      $r20 = self::$FAILED;
      if (!$silence) {$this->fail(92);}
      $this->currPos = $p8;
      $r7 = self::$FAILED;
      goto seq_6;
    }
    $r21 = $this->input[$this->currPos] ?? '';
    if ($r21 === "+" || $r21 === "-") {
      $this->currPos++;
    } else {
      $r21 = self::$FAILED;
      if (!$silence) {$this->fail(93);}
      $r21 = null;
    }
    $r22 = self::$FAILED;
    for (;;) {
      $r23 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-9]/", $r23)) {
        $this->currPos++;
        $r22 = true;
      } else {
        $r23 = self::$FAILED;
        if (!$silence) {$this->fail(90);}
        break;
      }
    }
    if ($r22===self::$FAILED) {
      $this->currPos = $p8;
      $r7 = self::$FAILED;
      goto seq_6;
    }
    // free $r23
    $r7 = true;
    seq_6:
    // free $p8
    choice_1:
    if ($r7===self::$FAILED) {
      $this->currPos = $p5;
      $r3 = self::$FAILED;
      goto seq_1;
    }
    $r3 = true;
    seq_1:
    // s <- $r3
    if ($r3!==self::$FAILED) {
      $r3 = substr($this->input, $p4, $this->currPos - $p4);
    } else {
      $r3 = self::$FAILED;
    }
    // free $p5
    // free $p4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a49($r3);
    }
    return $r1;
  }
  private function discardOtherOrComma($silence) {
    // start choice_1
    $r1 = $this->discardOther($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // start seq_1
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === ",") {
      $this->currPos++;
      $r3 = ",";
    } else {
      if (!$silence) {$this->fail(13);}
      $r3 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r4 = $this->discard_($silence);
    if ($r4===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    // free $p2
    choice_1:
    return $r1;
  }
  private function discardidentifier($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p4 = $this->currPos;
    // start choice_1
    $r5 = $this->discardArgumentNameKeyword(true);
    if ($r5!==self::$FAILED) {
      goto choice_1;
    }
    $r5 = $this->discardBufferRelatedType(true);
    if ($r5!==self::$FAILED) {
      goto choice_1;
    }
    $r5 = $this->discardOtherIdLike(true);
    choice_1:
    if ($r5 === self::$FAILED) {
      $r5 = false;
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p4;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p4
    $p4 = $this->currPos;
    // start seq_2
    $p7 = $this->currPos;
    // start choice_2
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "_constructor", $this->currPos, 12, false) === 0) {
      $r8 = "_constructor";
      $this->currPos += 12;
      goto choice_2;
    } else {
      $r8 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "_toString", $this->currPos, 9, false) === 0) {
      $r8 = "_toString";
      $this->currPos += 9;
      goto choice_2;
    } else {
      $r8 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "toString", $this->currPos, 8, false) === 0) {
      $r8 = "toString";
      $this->currPos += 8;
    } else {
      $r8 = self::$FAILED;
    }
    choice_2:
    if ($r8===self::$FAILED) {
      $r6 = self::$FAILED;
      goto seq_2;
    }
    $r9 = $this->discardi_(true);
    if ($r9===self::$FAILED) {
      $this->currPos = $p7;
      $r6 = self::$FAILED;
      goto seq_2;
    }
    $r6 = true;
    seq_2:
    // free $p7
    if ($r6 === self::$FAILED) {
      $r6 = false;
    } else {
      $r6 = self::$FAILED;
      $this->currPos = $p4;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p4
    $p4 = $this->currPos;
    // start seq_3
    $p7 = $this->currPos;
    $r11 = $this->input[$this->currPos] ?? '';
    if ($r11 === "-" || $r11 === "_") {
      $this->currPos++;
    } else {
      $r11 = self::$FAILED;
      if (!$silence) {$this->fail(28);}
      $r11 = null;
    }
    $r12 = $this->input[$this->currPos] ?? '';
    if (preg_match("/^[A-Za-z]/", $r12)) {
      $this->currPos++;
    } else {
      $r12 = self::$FAILED;
      if (!$silence) {$this->fail(29);}
      $this->currPos = $p7;
      $r10 = self::$FAILED;
      goto seq_3;
    }
    for (;;) {
      $r14 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[\\-_0-9A-Za-z]/", $r14)) {
        $this->currPos++;
      } else {
        $r14 = self::$FAILED;
        if (!$silence) {$this->fail(30);}
        break;
      }
    }
    // free $r14
    $r13 = true;
    if ($r13===self::$FAILED) {
      $this->currPos = $p7;
      $r10 = self::$FAILED;
      goto seq_3;
    }
    // free $r13
    $r10 = true;
    seq_3:
    // s <- $r10
    if ($r10!==self::$FAILED) {
      $r10 = substr($this->input, $p4, $this->currPos - $p4);
    } else {
      $r10 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p7
    // free $p4
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a20($r10);
    }
    // free $p3
    return $r1;
  }
  private function discardotherchar($silence) {
    // start seq_1
    $p1 = $this->currPos;
    $p3 = $this->currPos;
    $r4 = $this->discardotherterminals(true);
    if ($r4 === self::$FAILED) {
      $r4 = false;
    } else {
      $r4 = self::$FAILED;
      $this->currPos = $p3;
      $r2 = self::$FAILED;
      goto seq_1;
    }
    // free $p3
    $p3 = $this->currPos;
    if (strspn($this->input, "()[]{},", $this->currPos, 1) !== 0) {
      $r5 = $this->input[$this->currPos++];
    } else {
      $r5 = self::$FAILED;
    }
    if ($r5 === self::$FAILED) {
      $r5 = false;
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p3;
      $this->currPos = $p1;
      $r2 = self::$FAILED;
      goto seq_1;
    }
    // free $p3
    $r6 = self::charAt($this->input, $this->currPos);
    if (preg_match("/^[^\\x09\\x0a\\x0d 0-9A-Za-z]/", $r6)) {
      $this->currPos += strlen($r6);
    } else {
      $r6 = self::$FAILED;
      if (!$silence) {$this->fail(94);}
      $this->currPos = $p1;
      $r2 = self::$FAILED;
      goto seq_1;
    }
    $r2 = true;
    seq_1:
    // free $r2,$p1
    return $r2;
  }
  private function discardotherterminals($silence) {
    // start choice_1
    if (($this->input[$this->currPos] ?? null) === "-") {
      $this->currPos++;
      $r1 = "-";
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(95);}
      $r1 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "-Infinity", $this->currPos, 9, false) === 0) {
      $r1 = "-Infinity";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(96);}
      $r1 = self::$FAILED;
    }
    if (($this->input[$this->currPos] ?? null) === ".") {
      $this->currPos++;
      $r1 = ".";
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(91);}
      $r1 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "...", $this->currPos, 3, false) === 0) {
      $r1 = "...";
      $this->currPos += 3;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(97);}
      $r1 = self::$FAILED;
    }
    if (($this->input[$this->currPos] ?? null) === ":") {
      $this->currPos++;
      $r1 = ":";
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(31);}
      $r1 = self::$FAILED;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r1 = ";";
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(19);}
      $r1 = self::$FAILED;
    }
    if (($this->input[$this->currPos] ?? null) === "<") {
      $this->currPos++;
      $r1 = "<";
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(98);}
      $r1 = self::$FAILED;
    }
    if (($this->input[$this->currPos] ?? null) === "=") {
      $this->currPos++;
      $r1 = "=";
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(27);}
      $r1 = self::$FAILED;
    }
    if (($this->input[$this->currPos] ?? null) === ">") {
      $this->currPos++;
      $r1 = ">";
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(99);}
      $r1 = self::$FAILED;
    }
    if (($this->input[$this->currPos] ?? null) === "?") {
      $this->currPos++;
      $r1 = "?";
    } else {
      if (!$silence) {$this->fail(100);}
      $r1 = self::$FAILED;
    }
    choice_1:
    return $r1;
  }
  private function parseCallbackInterfaceMember($silence) {
    // start choice_1
    $r1 = $this->parseConst($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseRegularOperation($silence);
    choice_1:
    return $r1;
  }
  private function parseInterfaceMembers($silence) {
    $r1 = [];
    for (;;) {
      $p3 = $this->currPos;
      // start seq_1
      $p4 = $this->currPos;
      $r5 = $this->parseleadingComments($silence);
      // c <- $r5
      if ($r5===self::$FAILED) {
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r6 = $this->parseExtendedAttributeList($silence);
      // e <- $r6
      if ($r6===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r7 = $this->parseInterfaceMember($silence);
      // m <- $r7
      if ($r7===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r2 = true;
      seq_1:
      if ($r2!==self::$FAILED) {
        $this->savedPos = $p3;
        $r2 = $this->a21($r5, $r6, $r7);
        $r1[] = $r2;
      } else {
        break;
      }
      // free $p4
    }
    // free $r2
    return $r1;
  }
  private function parsec_($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = [];
    for (;;) {
      // start choice_1
      $p6 = $this->currPos;
      $r5 = $this->input[$this->currPos] ?? '';
      if ($r5 === " " || $r5 === "\x09") {
        $this->currPos++;
        $this->savedPos = $p6;
        $r5 = $this->a50();
        goto choice_1;
      } else {
        $r5 = self::$FAILED;
        if (!$silence) {$this->fail(101);}
      }
      $p7 = $this->currPos;
      $r5 = $this->discardcomment($silence);
      if ($r5!==self::$FAILED) {
        $r5 = substr($this->input, $p7, $this->currPos - $p7);
      } else {
        $r5 = self::$FAILED;
      }
      // free $p7
      choice_1:
      if ($r5!==self::$FAILED) {
        $r4[] = $r5;
      } else {
        break;
      }
    }
    // r <- $r4
    // free $r5
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a51($r4);
    }
    // free $p3
    return $r1;
  }
  private function parseMixinMembers($silence) {
    $r1 = [];
    for (;;) {
      $p3 = $this->currPos;
      // start seq_1
      $p4 = $this->currPos;
      $r5 = $this->parseleadingComments($silence);
      // c <- $r5
      if ($r5===self::$FAILED) {
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r6 = $this->parseExtendedAttributeList($silence);
      // e <- $r6
      if ($r6===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r7 = $this->parseMixinMember($silence);
      // m <- $r7
      if ($r7===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r2 = true;
      seq_1:
      if ($r2!==self::$FAILED) {
        $this->savedPos = $p3;
        $r2 = $this->a21($r5, $r6, $r7);
        $r1[] = $r2;
      } else {
        break;
      }
      // free $p4
    }
    // free $r2
    return $r1;
  }
  private function parseRegularOperation($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseType($silence);
    // t <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseOperationRest($silence);
    // o <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a52($r4, $r5);
    }
    // free $p3
    return $r1;
  }
  private function parseAttributeRest($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "attribute", $this->currPos, 9, false) === 0) {
      $r4 = "attribute";
      $this->currPos += 9;
    } else {
      if (!$silence) {$this->fail(34);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseTypeWithExtendedAttributes($silence);
    // t <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->parseAttributeName($silence);
    // name <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r8 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->parsec_($silence);
    // c <- $r9
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a53($r6, $r7, $r9);
    }
    // free $p3
    return $r1;
  }
  private function parsePartialInterfaceRest($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // name <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r6 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parsePartialInterfaceMembers($silence);
    // m <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r9 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r11 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r11 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r12 = $this->discard_($silence);
    if ($r12===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a54($r4, $r8);
    }
    // free $p3
    return $r1;
  }
  private function parseDefault($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "=") {
      $this->currPos++;
      $r4 = "=";
    } else {
      if (!$silence) {$this->fail(27);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseDefaultValue($silence);
    // val <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a55($r6);
    } else {
      $r1 = null;
    }
    // free $p3
    return $r1;
  }
  private function parseEnumValueListString($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsestring($silence);
    // s <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseEnumValueListComma($silence);
    // vals <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a56($r4, $r6);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    $r1 = '';
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a5();
    }
    choice_1:
    return $r1;
  }
  private function parseSingleType($silence) {
    // start choice_1
    $r1 = $this->parseDistinguishableType($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseAnyType($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parsePromiseType($silence);
    choice_1:
    return $r1;
  }
  private function parseUnionType($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r4 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseUnionMemberType($silence);
    // t <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = [];
    for (;;) {
      $p9 = $this->currPos;
      // start seq_2
      $p10 = $this->currPos;
      if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "or", $this->currPos, 2, false) === 0) {
        $r11 = "or";
        $this->currPos += 2;
      } else {
        if (!$silence) {$this->fail(79);}
        $r11 = self::$FAILED;
        $r8 = self::$FAILED;
        goto seq_2;
      }
      $r12 = $this->discardi_($silence);
      if ($r12===self::$FAILED) {
        $this->currPos = $p10;
        $r8 = self::$FAILED;
        goto seq_2;
      }
      $r13 = $this->parseUnionMemberType($silence);
      // t2 <- $r13
      if ($r13===self::$FAILED) {
        $this->currPos = $p10;
        $r8 = self::$FAILED;
        goto seq_2;
      }
      $r8 = true;
      seq_2:
      if ($r8!==self::$FAILED) {
        $this->savedPos = $p9;
        $r8 = $this->a57($r6, $r13);
        $r7[] = $r8;
      } else {
        break;
      }
      // free $p10
    }
    if (count($r7) === 0) {
      $r7 = self::$FAILED;
    }
    // rest <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $r8
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r8 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r14 = $this->discard_($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a58($r6, $r7);
    }
    // free $p3
    return $r1;
  }
  private function parseNull($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "?") {
      $this->currPos++;
      $r4 = "?";
    } else {
      if (!$silence) {$this->fail(100);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a59();
    } else {
      $r1 = null;
    }
    // free $p3
    return $r1;
  }
  private function parseArgumentRest($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "optional", $this->currPos, 8, false) === 0) {
      $r4 = "optional";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(80);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseTypeWithExtendedAttributes($silence);
    // t <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->parseArgumentName($silence);
    // name <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseDefault($silence);
    // d <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a60($r6, $r7, $r8);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p9 = $this->currPos;
    $r10 = $this->parseType($silence);
    // t <- $r10
    if ($r10===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r11 = $this->parseEllipsis($silence);
    // e <- $r11
    if ($r11===self::$FAILED) {
      $this->currPos = $p9;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r12 = $this->parseArgumentName($silence);
    // name <- $r12
    if ($r12===self::$FAILED) {
      $this->currPos = $p9;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a61($r10, $r11, $r12);
    }
    // free $p9
    choice_1:
    return $r1;
  }
  private function parseopt_minus($silence) {
    // start choice_1
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "-") {
      $this->currPos++;
      $r1 = "-";
      $this->savedPos = $p2;
      $r1 = $this->a62();
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(95);}
      $r1 = self::$FAILED;
    }
    $p3 = $this->currPos;
    $r1 = '';
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a63();
    }
    choice_1:
    return $r1;
  }
  private function parsedecimal_integer($silence) {
    $p2 = $this->currPos;
    $p4 = $this->currPos;
    // start seq_1
    $p5 = $this->currPos;
    $r6 = $this->input[$this->currPos] ?? '';
    if (preg_match("/^[1-9]/", $r6)) {
      $this->currPos++;
    } else {
      $r6 = self::$FAILED;
      if (!$silence) {$this->fail(102);}
      $r3 = self::$FAILED;
      goto seq_1;
    }
    for (;;) {
      $r8 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-9]/", $r8)) {
        $this->currPos++;
      } else {
        $r8 = self::$FAILED;
        if (!$silence) {$this->fail(90);}
        break;
      }
    }
    // free $r8
    $r7 = true;
    if ($r7===self::$FAILED) {
      $this->currPos = $p5;
      $r3 = self::$FAILED;
      goto seq_1;
    }
    // free $r7
    $r3 = true;
    seq_1:
    // s <- $r3
    if ($r3!==self::$FAILED) {
      $r3 = substr($this->input, $p4, $this->currPos - $p4);
    } else {
      $r3 = self::$FAILED;
    }
    // free $p5
    // free $p4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a64($r3);
    }
    return $r1;
  }
  private function parsehex_integer($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "0x", $this->currPos, 2, false) === 0) {
      $r4 = "0x";
      $this->currPos += 2;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(103);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "0X", $this->currPos, 2, false) === 0) {
      $r4 = "0X";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(104);}
      $r4 = self::$FAILED;
    }
    choice_1:
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p6 = $this->currPos;
    $r5 = self::$FAILED;
    for (;;) {
      $r7 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-9A-Fa-f]/", $r7)) {
        $this->currPos++;
        $r5 = true;
      } else {
        $r7 = self::$FAILED;
        if (!$silence) {$this->fail(105);}
        break;
      }
    }
    // s <- $r5
    if ($r5!==self::$FAILED) {
      $r5 = substr($this->input, $p6, $this->currPos - $p6);
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $r7
    // free $p6
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a65($r5);
    }
    // free $p3
    return $r1;
  }
  private function parseoctal_integer($silence) {
    $p2 = $this->currPos;
    $p4 = $this->currPos;
    // start seq_1
    $p5 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "0") {
      $this->currPos++;
      $r6 = "0";
    } else {
      if (!$silence) {$this->fail(106);}
      $r6 = self::$FAILED;
      $r3 = self::$FAILED;
      goto seq_1;
    }
    for (;;) {
      $r8 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-7]/", $r8)) {
        $this->currPos++;
      } else {
        $r8 = self::$FAILED;
        if (!$silence) {$this->fail(107);}
        break;
      }
    }
    // free $r8
    $r7 = true;
    if ($r7===self::$FAILED) {
      $this->currPos = $p5;
      $r3 = self::$FAILED;
      goto seq_1;
    }
    // free $r7
    $r3 = true;
    seq_1:
    // s <- $r3
    if ($r3!==self::$FAILED) {
      $r3 = substr($this->input, $p4, $this->currPos - $p4);
    } else {
      $r3 = self::$FAILED;
    }
    // free $p5
    // free $p4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a66($r3);
    }
    return $r1;
  }
  private function parseConst($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "const", $this->currPos, 5, false) === 0) {
      $r4 = "const";
      $this->currPos += 5;
    } else {
      if (!$silence) {$this->fail(35);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseConstType($silence);
    // t <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->parseidentifier($silence);
    // name <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->discard_($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "=") {
      $this->currPos++;
      $r9 = "=";
    } else {
      if (!$silence) {$this->fail(27);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->parseConstValue($silence);
    // v <- $r11
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r12 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r12 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r13 = $this->parsec_($silence);
    // c <- $r13
    if ($r13===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a67($r6, $r7, $r11, $r13);
    }
    // free $p3
    return $r1;
  }
  private function parseInterfaceMember($silence) {
    // start choice_1
    $r1 = $this->parsePartialInterfaceMember($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseConstructor($silence);
    choice_1:
    return $r1;
  }
  private function parseMixinMember($silence) {
    // start choice_1
    $r1 = $this->parseConst($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseRegularOperation($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseStringifier($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseOptionalReadOnly($silence);
    // ro <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseAttributeRest($silence);
    // a <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a68($r4, $r5);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parseOperationRest($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseOptionalOperationName($silence);
    // name <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r5 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r5 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->discard_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->parseArgumentList($silence);
    // args <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r8 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r10 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r10 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->parsec_($silence);
    // c <- $r11
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a69($r4, $r7, $r11);
    }
    // free $p3
    return $r1;
  }
  private function parseAttributeName($silence) {
    // start choice_1
    $r1 = $this->parseAttributeNameKeyword($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // id <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a70($r4);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parsePartialInterfaceMembers($silence) {
    $r1 = [];
    for (;;) {
      $p3 = $this->currPos;
      // start seq_1
      $p4 = $this->currPos;
      $r5 = $this->parseleadingComments($silence);
      // c <- $r5
      if ($r5===self::$FAILED) {
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r6 = $this->parseExtendedAttributeList($silence);
      // e <- $r6
      if ($r6===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r7 = $this->parsePartialInterfaceMember($silence);
      // m <- $r7
      if ($r7===self::$FAILED) {
        $this->currPos = $p4;
        $r2 = self::$FAILED;
        goto seq_1;
      }
      $r2 = true;
      seq_1:
      if ($r2!==self::$FAILED) {
        $this->savedPos = $p3;
        $r2 = $this->a21($r5, $r6, $r7);
        $r1[] = $r2;
      } else {
        break;
      }
      // free $p4
    }
    // free $r2
    return $r1;
  }
  private function parseDefaultValue($silence) {
    // start choice_1
    $r1 = $this->parseConstValue($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsestring($silence);
    // s <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a71($r4);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p6 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "[") {
      $this->currPos++;
      $r7 = "[";
    } else {
      if (!$silence) {$this->fail(8);}
      $r7 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r8 = $this->discard_($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p6;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    if (($this->input[$this->currPos] ?? null) === "]") {
      $this->currPos++;
      $r9 = "]";
    } else {
      if (!$silence) {$this->fail(9);}
      $r9 = self::$FAILED;
      $this->currPos = $p6;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p6;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a72();
      goto choice_1;
    }
    // free $p6
    $p6 = $this->currPos;
    // start seq_3
    $p11 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "{") {
      $this->currPos++;
      $r12 = "{";
    } else {
      if (!$silence) {$this->fail(17);}
      $r12 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r13 = $this->discard_($silence);
    if ($r13===self::$FAILED) {
      $this->currPos = $p11;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    if (($this->input[$this->currPos] ?? null) === "}") {
      $this->currPos++;
      $r14 = "}";
    } else {
      if (!$silence) {$this->fail(18);}
      $r14 = self::$FAILED;
      $this->currPos = $p11;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r15 = $this->discard_($silence);
    if ($r15===self::$FAILED) {
      $this->currPos = $p11;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r1 = true;
    seq_3:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p6;
      $r1 = $this->a73();
      goto choice_1;
    }
    // free $p11
    $p11 = $this->currPos;
    // start seq_4
    $p16 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "null", $this->currPos, 4, false) === 0) {
      $r17 = "null";
      $this->currPos += 4;
    } else {
      if (!$silence) {$this->fail(76);}
      $r17 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_4;
    }
    $r18 = $this->discardi_($silence);
    if ($r18===self::$FAILED) {
      $this->currPos = $p16;
      $r1 = self::$FAILED;
      goto seq_4;
    }
    $r1 = true;
    seq_4:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p11;
      $r1 = $this->a74();
    }
    // free $p16
    choice_1:
    return $r1;
  }
  private function parseDistinguishableType($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    // start choice_1
    $p5 = $this->currPos;
    $r6 = $this->parsePrimitiveType($silence);
    // t <- $r6
    $r4 = $r6;
    if ($r4!==self::$FAILED) {
      $this->savedPos = $p5;
      $r4 = $this->a75($r6);
      goto choice_1;
    }
    $p7 = $this->currPos;
    $r8 = $this->parseStringType($silence);
    // t <- $r8
    $r4 = $r8;
    if ($r4!==self::$FAILED) {
      $this->savedPos = $p7;
      $r4 = $this->a75($r8);
      goto choice_1;
    }
    $p9 = $this->currPos;
    // start seq_2
    $p10 = $this->currPos;
    // start choice_2
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "object", $this->currPos, 6, false) === 0) {
      $r11 = "object";
      $this->currPos += 6;
      goto choice_2;
    } else {
      if (!$silence) {$this->fail(77);}
      $r11 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "symbol", $this->currPos, 6, false) === 0) {
      $r11 = "symbol";
      $this->currPos += 6;
    } else {
      if (!$silence) {$this->fail(84);}
      $r11 = self::$FAILED;
    }
    choice_2:
    // t <- $r11
    if ($r11===self::$FAILED) {
      $r4 = self::$FAILED;
      goto seq_2;
    }
    $r12 = $this->discardi_($silence);
    if ($r12===self::$FAILED) {
      $this->currPos = $p10;
      $r4 = self::$FAILED;
      goto seq_2;
    }
    $r4 = true;
    seq_2:
    if ($r4!==self::$FAILED) {
      $this->savedPos = $p9;
      $r4 = $this->a75($r11);
      goto choice_1;
    }
    // free $p10
    $p10 = $this->currPos;
    $r13 = $this->parseBufferRelatedType($silence);
    // t <- $r13
    $r4 = $r13;
    if ($r4!==self::$FAILED) {
      $this->savedPos = $p10;
      $r4 = $this->a75($r13);
      goto choice_1;
    }
    $p14 = $this->currPos;
    // start seq_3
    $p15 = $this->currPos;
    // start choice_3
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "sequence", $this->currPos, 8, false) === 0) {
      $r16 = "sequence";
      $this->currPos += 8;
      goto choice_3;
    } else {
      if (!$silence) {$this->fail(82);}
      $r16 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "FrozenArray", $this->currPos, 11, false) === 0) {
      $r16 = "FrozenArray";
      $this->currPos += 11;
      goto choice_3;
    } else {
      if (!$silence) {$this->fail(62);}
      $r16 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "ObservableArray", $this->currPos, 15, false) === 0) {
      $r16 = "ObservableArray";
      $this->currPos += 15;
    } else {
      if (!$silence) {$this->fail(65);}
      $r16 = self::$FAILED;
    }
    choice_3:
    // g <- $r16
    if ($r16===self::$FAILED) {
      $r4 = self::$FAILED;
      goto seq_3;
    }
    $r17 = $this->discardi_($silence);
    if ($r17===self::$FAILED) {
      $this->currPos = $p15;
      $r4 = self::$FAILED;
      goto seq_3;
    }
    if (($this->input[$this->currPos] ?? null) === "<") {
      $this->currPos++;
      $r18 = "<";
    } else {
      if (!$silence) {$this->fail(98);}
      $r18 = self::$FAILED;
      $this->currPos = $p15;
      $r4 = self::$FAILED;
      goto seq_3;
    }
    $r19 = $this->discard_($silence);
    if ($r19===self::$FAILED) {
      $this->currPos = $p15;
      $r4 = self::$FAILED;
      goto seq_3;
    }
    $r20 = $this->parseTypeWithExtendedAttributes($silence);
    // t <- $r20
    if ($r20===self::$FAILED) {
      $this->currPos = $p15;
      $r4 = self::$FAILED;
      goto seq_3;
    }
    if (($this->input[$this->currPos] ?? null) === ">") {
      $this->currPos++;
      $r21 = ">";
    } else {
      if (!$silence) {$this->fail(99);}
      $r21 = self::$FAILED;
      $this->currPos = $p15;
      $r4 = self::$FAILED;
      goto seq_3;
    }
    $r22 = $this->discard_($silence);
    if ($r22===self::$FAILED) {
      $this->currPos = $p15;
      $r4 = self::$FAILED;
      goto seq_3;
    }
    $r4 = true;
    seq_3:
    if ($r4!==self::$FAILED) {
      $this->savedPos = $p14;
      $r4 = $this->a76($r16, $r20);
      goto choice_1;
    }
    // free $p15
    $r4 = $this->parseRecordType($silence);
    if ($r4!==self::$FAILED) {
      goto choice_1;
    }
    $p15 = $this->currPos;
    // start seq_4
    $p23 = $this->currPos;
    $r24 = $this->parseidentifier($silence);
    // t <- $r24
    if ($r24===self::$FAILED) {
      $r4 = self::$FAILED;
      goto seq_4;
    }
    $r25 = $this->discard_($silence);
    if ($r25===self::$FAILED) {
      $this->currPos = $p23;
      $r4 = self::$FAILED;
      goto seq_4;
    }
    $r4 = true;
    seq_4:
    if ($r4!==self::$FAILED) {
      $this->savedPos = $p15;
      $r4 = $this->a77($r24);
    }
    // free $p23
    choice_1:
    // dt <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r26 = $this->parseNull($silence);
    // n <- $r26
    if ($r26===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a78($r4, $r26);
    }
    // free $p3
    return $r1;
  }
  private function parseAnyType($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "any", $this->currPos, 3, false) === 0) {
      $r4 = "any";
      $this->currPos += 3;
    } else {
      if (!$silence) {$this->fail(68);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a79();
    }
    // free $p3
    return $r1;
  }
  private function parsePromiseType($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Promise", $this->currPos, 7, false) === 0) {
      $r4 = "Promise";
      $this->currPos += 7;
    } else {
      if (!$silence) {$this->fail(66);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "<") {
      $this->currPos++;
      $r6 = "<";
    } else {
      if (!$silence) {$this->fail(98);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseType($silence);
    // t <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ">") {
      $this->currPos++;
      $r9 = ">";
    } else {
      if (!$silence) {$this->fail(99);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a80($r8);
    }
    // free $p3
    return $r1;
  }
  private function parseUnionMemberType($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseExtendedAttributeList($silence);
    // e <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseDistinguishableType($silence);
    // t <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a25($r4, $r5);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p6 = $this->currPos;
    $r7 = $this->parseUnionType($silence);
    // t <- $r7
    if ($r7===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r8 = $this->parseNull($silence);
    // n <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p6;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a81($r7, $r8);
    }
    // free $p6
    choice_1:
    return $r1;
  }
  private function parseArgumentName($silence) {
    // start choice_1
    $r1 = $this->parseArgumentNameKeyword($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // name <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a82($r4);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parseEllipsis($silence) {
    // start seq_1
    $p2 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "...", $this->currPos, 3, false) === 0) {
      $r3 = "...";
      $this->currPos += 3;
    } else {
      if (!$silence) {$this->fail(97);}
      $r3 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r4 = $this->parse_($silence);
    if ($r4===self::$FAILED) {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = [$r3,$r4];
    seq_1:
    if ($r1===self::$FAILED) {
      $r1 = null;
    }
    // free $p2
    return $r1;
  }
  private function parseConstType($silence) {
    // start choice_1
    $p2 = $this->currPos;
    $r3 = $this->parsePrimitiveType($silence);
    // t <- $r3
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a83($r3);
      goto choice_1;
    }
    $p4 = $this->currPos;
    // start seq_1
    $p5 = $this->currPos;
    $r6 = $this->parseidentifier($silence);
    // t <- $r6
    if ($r6===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p5;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p4;
      $r1 = $this->a83($r6);
    }
    // free $p5
    choice_1:
    return $r1;
  }
  private function parseConstValue($silence) {
    // start choice_1
    $r1 = $this->parseBooleanLiteral($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseFloatLiteral($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseIntLiteral($silence);
    choice_1:
    return $r1;
  }
  private function parsePartialInterfaceMember($silence) {
    // start choice_1
    $r1 = $this->parseConst($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseOperation($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseStringifier($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseStaticMember($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseIterable($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseAsyncIterable($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseReadOnlyMember($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseAttributeRest($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseMaplikeRest($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseSetlikeRest($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseInheritAttribute($silence);
    choice_1:
    return $r1;
  }
  private function parseConstructor($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "constructor", $this->currPos, 11, false) === 0) {
      $r4 = "constructor";
      $this->currPos += 11;
    } else {
      if (!$silence) {$this->fail(36);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r6 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseArgumentList($silence);
    // args <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r9 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r11 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r11 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r12 = $this->parsec_($silence);
    // c <- $r12
    if ($r12===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a84($r8, $r12);
    }
    // free $p3
    return $r1;
  }
  private function parseStringifier($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "stringifier", $this->currPos, 11, false) === 0) {
      $r4 = "stringifier";
      $this->currPos += 11;
    } else {
      if (!$silence) {$this->fail(47);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseStringifierRest($silence);
    // rest <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a85($r6);
    }
    // free $p3
    return $r1;
  }
  private function parseOptionalReadOnly($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "readonly", $this->currPos, 8, false) === 0) {
      $r4 = "readonly";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(42);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a59();
    } else {
      $r1 = null;
    }
    // free $p3
    return $r1;
  }
  private function parseOptionalOperationName($silence) {
    $r1 = $this->parseOperationName($silence);
    if ($r1===self::$FAILED) {
      $r1 = null;
    }
    return $r1;
  }
  private function parseAttributeNameKeyword($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "async", $this->currPos, 5, false) === 0) {
      $r4 = "async";
      $this->currPos += 5;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(33);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "required", $this->currPos, 8, false) === 0) {
      $r4 = "required";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(43);}
      $r4 = self::$FAILED;
    }
    choice_1:
    // id <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discardi_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a70($r4);
    }
    // free $p3
    return $r1;
  }
  private function parsePrimitiveType($silence) {
    // start choice_1
    $r1 = $this->parseUnsignedIntegerType($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseUnrestrictedFloatType($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_2
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "undefined", $this->currPos, 9, false) === 0) {
      $r4 = "undefined";
      $this->currPos += 9;
      goto choice_2;
    } else {
      if (!$silence) {$this->fail(87);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "boolean", $this->currPos, 7, false) === 0) {
      $r4 = "boolean";
      $this->currPos += 7;
      goto choice_2;
    } else {
      if (!$silence) {$this->fail(70);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "byte", $this->currPos, 4, false) === 0) {
      $r4 = "byte";
      $this->currPos += 4;
      goto choice_2;
    } else {
      if (!$silence) {$this->fail(71);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "octet", $this->currPos, 5, false) === 0) {
      $r4 = "octet";
      $this->currPos += 5;
      goto choice_2;
    } else {
      if (!$silence) {$this->fail(78);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "bigint", $this->currPos, 6, false) === 0) {
      $r4 = "bigint";
      $this->currPos += 6;
    } else {
      if (!$silence) {$this->fail(69);}
      $r4 = self::$FAILED;
    }
    choice_2:
    // v <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discardi_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a86($r4);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parseStringType($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "ByteString", $this->currPos, 10, false) === 0) {
      $r4 = "ByteString";
      $this->currPos += 10;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(60);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "DOMString", $this->currPos, 9, false) === 0) {
      $r4 = "DOMString";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(61);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "USVString", $this->currPos, 9, false) === 0) {
      $r4 = "USVString";
      $this->currPos += 9;
    } else {
      if (!$silence) {$this->fail(67);}
      $r4 = self::$FAILED;
    }
    choice_1:
    // s <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discardi_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a40($r4);
    }
    // free $p3
    return $r1;
  }
  private function parseBufferRelatedType($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "ArrayBuffer", $this->currPos, 11, false) === 0) {
      $r4 = "ArrayBuffer";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(49);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "DataView", $this->currPos, 8, false) === 0) {
      $r4 = "DataView";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(50);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Int8Array", $this->currPos, 9, false) === 0) {
      $r4 = "Int8Array";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(51);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Int16Array", $this->currPos, 10, false) === 0) {
      $r4 = "Int16Array";
      $this->currPos += 10;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(52);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Int32Array", $this->currPos, 10, false) === 0) {
      $r4 = "Int32Array";
      $this->currPos += 10;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(53);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Uint8Array", $this->currPos, 10, false) === 0) {
      $r4 = "Uint8Array";
      $this->currPos += 10;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(54);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Uint16Array", $this->currPos, 11, false) === 0) {
      $r4 = "Uint16Array";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(55);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Uint32Array", $this->currPos, 11, false) === 0) {
      $r4 = "Uint32Array";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(56);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Uint8ClampedArray", $this->currPos, 17, false) === 0) {
      $r4 = "Uint8ClampedArray";
      $this->currPos += 17;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(57);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Float32Array", $this->currPos, 12, false) === 0) {
      $r4 = "Float32Array";
      $this->currPos += 12;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(58);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Float64Array", $this->currPos, 12, false) === 0) {
      $r4 = "Float64Array";
      $this->currPos += 12;
    } else {
      if (!$silence) {$this->fail(59);}
      $r4 = self::$FAILED;
    }
    choice_1:
    // s <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discardi_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a40($r4);
    }
    // free $p3
    return $r1;
  }
  private function parseRecordType($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "record", $this->currPos, 6, false) === 0) {
      $r4 = "record";
      $this->currPos += 6;
    } else {
      if (!$silence) {$this->fail(81);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "<") {
      $this->currPos++;
      $r6 = "<";
    } else {
      if (!$silence) {$this->fail(98);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseStringType($silence);
    // t1 <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ",") {
      $this->currPos++;
      $r9 = ",";
    } else {
      if (!$silence) {$this->fail(13);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->parseTypeWithExtendedAttributes($silence);
    // t2 <- $r11
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ">") {
      $this->currPos++;
      $r12 = ">";
    } else {
      if (!$silence) {$this->fail(99);}
      $r12 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r13 = $this->discard_($silence);
    if ($r13===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a87($r8, $r11);
    }
    // free $p3
    return $r1;
  }
  private function parseArgumentNameKeyword($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "async", $this->currPos, 5, false) === 0) {
      $r4 = "async";
      $this->currPos += 5;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(33);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "attribute", $this->currPos, 9, false) === 0) {
      $r4 = "attribute";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(34);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "callback", $this->currPos, 8, false) === 0) {
      $r4 = "callback";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(14);}
      $r4 = self::$FAILED;
    }
    // start seq_2
    $p6 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "const", $this->currPos, 5, false) === 0) {
      $r7 = "const";
      $this->currPos += 5;
    } else {
      if (!$silence) {$this->fail(35);}
      $r7 = self::$FAILED;
      $r4 = self::$FAILED;
      goto seq_2;
    }
    $p8 = $this->currPos;
    $r9 = $this->input[$this->currPos] ?? '';
    if (preg_match("/^[\\-_0-9A-Za-z]/", $r9)) {
      $this->currPos++;
    } else {
      $r9 = self::$FAILED;
    }
    if ($r9 === self::$FAILED) {
      $r9 = false;
    } else {
      $r9 = self::$FAILED;
      $this->currPos = $p8;
      $this->currPos = $p6;
      $r4 = self::$FAILED;
      goto seq_2;
    }
    // free $p8
    $r4 = true;
    seq_2:
    if ($r4!==self::$FAILED) {
      goto choice_1;
    }
    // free $p6
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "constructor", $this->currPos, 11, false) === 0) {
      $r4 = "constructor";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(36);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "deleter", $this->currPos, 7, false) === 0) {
      $r4 = "deleter";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(37);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "dictionary", $this->currPos, 10, false) === 0) {
      $r4 = "dictionary";
      $this->currPos += 10;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(21);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "enum", $this->currPos, 4, false) === 0) {
      $r4 = "enum";
      $this->currPos += 4;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(22);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "getter", $this->currPos, 6, false) === 0) {
      $r4 = "getter";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(38);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "includes", $this->currPos, 8, false) === 0) {
      $r4 = "includes";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(24);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "inherit", $this->currPos, 7, false) === 0) {
      $r4 = "inherit";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(39);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "interface", $this->currPos, 9, false) === 0) {
      $r4 = "interface";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(15);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "iterable", $this->currPos, 8, false) === 0) {
      $r4 = "iterable";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(40);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "maplike", $this->currPos, 7, false) === 0) {
      $r4 = "maplike";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(41);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "mixin", $this->currPos, 5, false) === 0) {
      $r4 = "mixin";
      $this->currPos += 5;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(32);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "namespace", $this->currPos, 9, false) === 0) {
      $r4 = "namespace";
      $this->currPos += 9;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(16);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "partial", $this->currPos, 7, false) === 0) {
      $r4 = "partial";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(20);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "readonly", $this->currPos, 8, false) === 0) {
      $r4 = "readonly";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(42);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "required", $this->currPos, 8, false) === 0) {
      $r4 = "required";
      $this->currPos += 8;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(43);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "setlike", $this->currPos, 7, false) === 0) {
      $r4 = "setlike";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(44);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "setter", $this->currPos, 6, false) === 0) {
      $r4 = "setter";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(45);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "static", $this->currPos, 6, false) === 0) {
      $r4 = "static";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(46);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "stringifier", $this->currPos, 11, false) === 0) {
      $r4 = "stringifier";
      $this->currPos += 11;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(47);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "typedef", $this->currPos, 7, false) === 0) {
      $r4 = "typedef";
      $this->currPos += 7;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(23);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "unrestricted", $this->currPos, 12, false) === 0) {
      $r4 = "unrestricted";
      $this->currPos += 12;
    } else {
      if (!$silence) {$this->fail(48);}
      $r4 = self::$FAILED;
    }
    choice_1:
    // k <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r10 = $this->discardi_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a39($r4);
    }
    // free $p3
    return $r1;
  }
  private function parseBooleanLiteral($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "true", $this->currPos, 4, false) === 0) {
      $r4 = "true";
      $this->currPos += 4;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(85);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "false", $this->currPos, 5, false) === 0) {
      $r4 = "false";
      $this->currPos += 5;
    } else {
      if (!$silence) {$this->fail(73);}
      $r4 = self::$FAILED;
    }
    choice_1:
    // v <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discardi_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a88($r4);
    }
    // free $p3
    return $r1;
  }
  private function parseFloatLiteral($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    $r4 = $this->discarddecimal($silence);
    // s <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discard_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a89($r4);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p5 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "-Infinity", $this->currPos, 9, false) === 0) {
      $r7 = "-Infinity";
      $this->currPos += 9;
    } else {
      if (!$silence) {$this->fail(96);}
      $r7 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r8 = $this->discardi_($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p5;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a90();
      goto choice_1;
    }
    // free $p5
    $p5 = $this->currPos;
    // start seq_3
    $p9 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "Infinity", $this->currPos, 8, false) === 0) {
      $r10 = "Infinity";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(63);}
      $r10 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r11 = $this->discardi_($silence);
    if ($r11===self::$FAILED) {
      $this->currPos = $p9;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $r1 = true;
    seq_3:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p5;
      $r1 = $this->a91();
      goto choice_1;
    }
    // free $p9
    $p9 = $this->currPos;
    // start seq_4
    $p12 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "NaN", $this->currPos, 3, false) === 0) {
      $r13 = "NaN";
      $this->currPos += 3;
    } else {
      if (!$silence) {$this->fail(64);}
      $r13 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_4;
    }
    $r14 = $this->discardi_($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p12;
      $r1 = self::$FAILED;
      goto seq_4;
    }
    $r1 = true;
    seq_4:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p9;
      $r1 = $this->a92();
    }
    // free $p12
    choice_1:
    return $r1;
  }
  private function parseIntLiteral($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    $r6 = $this->parseinteger($silence);
    // i <- $r6
    $r4 = $r6;
    // s <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a89($r4);
    }
    // free $p3
    return $r1;
  }
  private function parseOperation($silence) {
    // start choice_1
    $r1 = $this->parseRegularOperation($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseSpecialOperation($silence);
    choice_1:
    return $r1;
  }
  private function parseStaticMember($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "static", $this->currPos, 6, false) === 0) {
      $r4 = "static";
      $this->currPos += 6;
    } else {
      if (!$silence) {$this->fail(46);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseStaticMemberRest($silence);
    // rest <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a93($r6);
    }
    // free $p3
    return $r1;
  }
  private function parseIterable($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "iterable", $this->currPos, 8, false) === 0) {
      $r4 = "iterable";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(40);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "<") {
      $this->currPos++;
      $r6 = "<";
    } else {
      if (!$silence) {$this->fail(98);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseTypeWithExtendedAttributes($silence);
    // t1 <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->parseOptionalType($silence);
    // t2 <- $r9
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ">") {
      $this->currPos++;
      $r10 = ">";
    } else {
      if (!$silence) {$this->fail(99);}
      $r10 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->discard_($silence);
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r12 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r12 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r13 = $this->parsec_($silence);
    // c <- $r13
    if ($r13===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a94($r8, $r9, $r13);
    }
    // free $p3
    return $r1;
  }
  private function parseAsyncIterable($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "async", $this->currPos, 5, false) === 0) {
      $r4 = "async";
      $this->currPos += 5;
    } else {
      if (!$silence) {$this->fail(33);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "iterable", $this->currPos, 8, false) === 0) {
      $r6 = "iterable";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(40);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discardi_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "<") {
      $this->currPos++;
      $r8 = "<";
    } else {
      if (!$silence) {$this->fail(98);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r9 = $this->discard_($silence);
    if ($r9===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->parseTypeWithExtendedAttributes($silence);
    // t1 <- $r10
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->parseOptionalType($silence);
    // t2 <- $r11
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ">") {
      $this->currPos++;
      $r12 = ">";
    } else {
      if (!$silence) {$this->fail(99);}
      $r12 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r13 = $this->discard_($silence);
    if ($r13===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r14 = $this->parseOptionalArgumentList($silence);
    // args <- $r14
    if ($r14===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r15 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r15 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r16 = $this->parsec_($silence);
    // c <- $r16
    if ($r16===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a95($r10, $r11, $r14, $r16);
    }
    // free $p3
    return $r1;
  }
  private function parseReadOnlyMember($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "readonly", $this->currPos, 8, false) === 0) {
      $r4 = "readonly";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(42);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseReadOnlyMemberRest($silence);
    // m <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a96($r6);
    }
    // free $p3
    return $r1;
  }
  private function parseMaplikeRest($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "maplike", $this->currPos, 7, false) === 0) {
      $r4 = "maplike";
      $this->currPos += 7;
    } else {
      if (!$silence) {$this->fail(41);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "<") {
      $this->currPos++;
      $r6 = "<";
    } else {
      if (!$silence) {$this->fail(98);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseTypeWithExtendedAttributes($silence);
    // t1 <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ",") {
      $this->currPos++;
      $r9 = ",";
    } else {
      if (!$silence) {$this->fail(13);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r11 = $this->parseTypeWithExtendedAttributes($silence);
    // t2 <- $r11
    if ($r11===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ">") {
      $this->currPos++;
      $r12 = ">";
    } else {
      if (!$silence) {$this->fail(99);}
      $r12 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r13 = $this->discard_($silence);
    if ($r13===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r14 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r14 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r15 = $this->parsec_($silence);
    // c <- $r15
    if ($r15===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a97($r8, $r11, $r15);
    }
    // free $p3
    return $r1;
  }
  private function parseSetlikeRest($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "setlike", $this->currPos, 7, false) === 0) {
      $r4 = "setlike";
      $this->currPos += 7;
    } else {
      if (!$silence) {$this->fail(44);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "<") {
      $this->currPos++;
      $r6 = "<";
    } else {
      if (!$silence) {$this->fail(98);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discard_($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parseTypeWithExtendedAttributes($silence);
    // t <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ">") {
      $this->currPos++;
      $r9 = ">";
    } else {
      if (!$silence) {$this->fail(99);}
      $r9 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r10 = $this->discard_($silence);
    if ($r10===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r11 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r11 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r12 = $this->parsec_($silence);
    // c <- $r12
    if ($r12===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a98($r8, $r12);
    }
    // free $p3
    return $r1;
  }
  private function parseInheritAttribute($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "inherit", $this->currPos, 7, false) === 0) {
      $r4 = "inherit";
      $this->currPos += 7;
    } else {
      if (!$silence) {$this->fail(39);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseAttributeRest($silence);
    // a <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a99($r6);
    }
    // free $p3
    return $r1;
  }
  private function parseStringifierRest($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseOptionalReadOnly($silence);
    // ro <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseAttributeRest($silence);
    // a <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a100($r4, $r5);
      goto choice_1;
    }
    // free $p3
    $r1 = $this->parseRegularOperation($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p3 = $this->currPos;
    // start seq_2
    $p6 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === ";") {
      $this->currPos++;
      $r7 = ";";
    } else {
      if (!$silence) {$this->fail(19);}
      $r7 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r8 = $this->parsec_($silence);
    // c <- $r8
    if ($r8===self::$FAILED) {
      $this->currPos = $p6;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a101($r8);
    }
    // free $p6
    choice_1:
    return $r1;
  }
  private function parseOperationName($silence) {
    // start choice_1
    $r1 = $this->parseOperationNameKeyword($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseidentifier($silence);
    // name <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a82($r4);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parseUnsignedIntegerType($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "unsigned", $this->currPos, 8, false) === 0) {
      $r4 = "unsigned";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(86);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseIntegerType($silence);
    // t <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a102($r6);
      goto choice_1;
    }
    // free $p3
    $r1 = $this->parseIntegerType($silence);
    choice_1:
    return $r1;
  }
  private function parseUnrestrictedFloatType($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "unrestricted", $this->currPos, 12, false) === 0) {
      $r4 = "unrestricted";
      $this->currPos += 12;
    } else {
      if (!$silence) {$this->fail(48);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseFloatType($silence);
    // t <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a103($r6);
      goto choice_1;
    }
    // free $p3
    $r1 = $this->parseFloatType($silence);
    choice_1:
    return $r1;
  }
  private function parseinteger($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseopt_minus($silence);
    // m <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // start choice_1
    $r5 = $this->parsedecimal_integer($silence);
    if ($r5!==self::$FAILED) {
      goto choice_1;
    }
    $r5 = $this->parsehex_integer($silence);
    if ($r5!==self::$FAILED) {
      goto choice_1;
    }
    $r5 = $this->parseoctal_integer($silence);
    choice_1:
    // n <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a48($r4, $r5);
    }
    // free $p3
    return $r1;
  }
  private function parseSpecialOperation($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseSpecial($silence);
    // s <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseRegularOperation($silence);
    // o <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a104($r4, $r5);
    }
    // free $p3
    return $r1;
  }
  private function parseStaticMemberRest($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseOptionalReadOnly($silence);
    // ro <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseAttributeRest($silence);
    // a <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a100($r4, $r5);
      goto choice_1;
    }
    // free $p3
    $r1 = $this->parseRegularOperation($silence);
    choice_1:
    return $r1;
  }
  private function parseOptionalType($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === ",") {
      $this->currPos++;
      $r4 = ",";
    } else {
      if (!$silence) {$this->fail(13);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseTypeWithExtendedAttributes($silence);
    // t <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a105($r6);
    } else {
      $r1 = null;
    }
    // free $p3
    return $r1;
  }
  private function parseOptionalArgumentList($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r4 = "(";
    } else {
      if (!$silence) {$this->fail(25);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discard_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseArgumentList($silence);
    // args <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r7 = ")";
    } else {
      if (!$silence) {$this->fail(26);}
      $r7 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->discard_($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a106($r6);
    } else {
      $r1 = null;
    }
    // free $p3
    return $r1;
  }
  private function parseReadOnlyMemberRest($silence) {
    // start choice_1
    $r1 = $this->parseAttributeRest($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseMaplikeRest($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseSetlikeRest($silence);
    choice_1:
    return $r1;
  }
  private function parseOperationNameKeyword($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    // s <- $r4
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "includes", $this->currPos, 8, false) === 0) {
      $r4 = "includes";
      $this->currPos += 8;
    } else {
      if (!$silence) {$this->fail(24);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardi_($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a40($r4);
    }
    // free $p3
    return $r1;
  }
  private function parseIntegerType($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // s <- $r4
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "short", $this->currPos, 5, false) === 0) {
      $r4 = "short";
      $this->currPos += 5;
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      if (!$silence) {$this->fail(83);}
      $r4 = self::$FAILED;
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discardi_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a40($r4);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p5 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "long", $this->currPos, 4, false) === 0) {
      $r7 = "long";
      $this->currPos += 4;
    } else {
      if (!$silence) {$this->fail(75);}
      $r7 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r8 = $this->discardi_($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p5;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    // start seq_3
    $p10 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "long", $this->currPos, 4, false) === 0) {
      $r11 = "long";
      $this->currPos += 4;
    } else {
      if (!$silence) {$this->fail(75);}
      $r11 = self::$FAILED;
      $r9 = self::$FAILED;
      goto seq_3;
    }
    $r12 = $this->parsei_($silence);
    if ($r12===self::$FAILED) {
      $this->currPos = $p10;
      $r9 = self::$FAILED;
      goto seq_3;
    }
    $r9 = [$r11,$r12];
    seq_3:
    if ($r9===self::$FAILED) {
      $r9 = null;
    }
    // free $p10
    // l <- $r9
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a107($r9);
    }
    // free $p5
    choice_1:
    return $r1;
  }
  private function parseFloatType($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "float", $this->currPos, 5, false) === 0) {
      $r4 = "float";
      $this->currPos += 5;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(74);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "double", $this->currPos, 6, false) === 0) {
      $r4 = "double";
      $this->currPos += 6;
    } else {
      if (!$silence) {$this->fail(72);}
      $r4 = self::$FAILED;
    }
    choice_1:
    // v <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discardi_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a86($r4);
    }
    // free $p3
    return $r1;
  }
  private function parseSpecial($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p5 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "getter", $this->currPos, 6, false) === 0) {
      $r4 = "getter";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(38);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "setter", $this->currPos, 6, false) === 0) {
      $r4 = "setter";
      $this->currPos += 6;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(45);}
      $r4 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "deleter", $this->currPos, 7, false) === 0) {
      $r4 = "deleter";
      $this->currPos += 7;
    } else {
      if (!$silence) {$this->fail(37);}
      $r4 = self::$FAILED;
    }
    choice_1:
    // s <- $r4
    if ($r4!==self::$FAILED) {
      $r4 = substr($this->input, $p5, $this->currPos - $p5);
    } else {
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r6 = $this->discardi_($silence);
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a40($r4);
    }
    // free $p3
    return $r1;
  }
  private function parsei_($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p4 = $this->currPos;
    $r5 = $this->input[$this->currPos] ?? '';
    if (preg_match("/^[\\-_0-9A-Za-z]/", $r5)) {
      $this->currPos++;
    } else {
      $r5 = self::$FAILED;
    }
    if ($r5 === self::$FAILED) {
      $r5 = false;
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p4;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p4
    $r6 = $this->parse_($silence);
    // c <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a7($r6);
    }
    // free $p3
    return $r1;
  }

  public function parse($input, $options = []) {
    $this->initInternal($input, $options);
    $startRule = $options['startRule'] ?? '(DEFAULT)';
    $result = null;

    if (!empty($options['stream'])) {
      switch ($startRule) {
        
        default:
          throw new \WikiPEG\InternalError("Can't stream rule $startRule.");
      }
    } else {
      switch ($startRule) {
        case '(DEFAULT)':
        case "start":
          $result = $this->parsestart(false);
          break;
        default:
          throw new \WikiPEG\InternalError("Can't start parsing from rule $startRule.");
      }
    }

    if ($result !== self::$FAILED && $this->currPos === $this->inputLength) {
      return $result;
    } else {
      if ($result !== self::$FAILED && $this->currPos < $this->inputLength) {
        $this->fail(0);
      }
      throw $this->buildParseException();
    }
  }
}

