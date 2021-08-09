<?php

namespace Wikimedia\WebIDL;

class Grammar extends \Wikimedia\WikiPEG\PEGParserBase {
	/**
	 * @param string $contents
	 * @param array $options
	 * @return array
	 */
	public static function load( string $contents, array $options = [] ) {
	}

	/**
	 * @param string $input Input string
	 * @param array $options Parse options
	 * @return mixed Result of the parse
	 */
	public function parse( $input, $options = [] ) {
		return null;
	}
}
