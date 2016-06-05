<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests\Stemmer;

use Joomla\Language\Stemmer\Porteren;

/**
 * Test class for Porteren.
 */
class PorterenTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var  Porteren
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Porteren;
	}

	/**
	 * Data provider for testStem()
	 *
	 * @return  array
	 */
	public function dataStemProvider()
	{
		return [
			['Car', 'Car', 'en'],
			['Cars', 'Car', 'en'],
			['fishing', 'fish', 'en'],
			['fished', 'fish', 'en'],
			['fish', 'fish', 'en'],
			['powerful', 'power', 'en'],
			['Reflect', 'Reflect', 'en'],
			['Reflects', 'Reflect', 'en'],
			['Reflected', 'Reflect', 'en'],
			['stemming', 'stem', 'en'],
			['stemmed', 'stem', 'en'],
			['walk', 'walk', 'en'],
			['walking', 'walk', 'en'],
			['walked', 'walk', 'en'],
			['walks', 'walk', 'en'],
			['allowance', 'allow', 'en'],
			['us', 'us', 'en'],
			['I', 'I', 'en'],
			['Standardabweichung', 'Standardabweichung', 'de'],

			// Step 1a
			['caresses', 'caress', 'en'],
			['ponies', 'poni', 'en'],
			['ties', 'ti', 'en'],
			['caress', 'caress', 'en'],
			['cats', 'cat', 'en'],

			// Step 1b
			['feed', 'feed', 'en'],
			['agreed', 'agre', 'en'],
			['plastered', 'plaster', 'en'],
			['bled', 'bled', 'en'],
			['motoring', 'motor', 'en'],
			['sing', 'sing', 'en'],
			['conflated', 'conflat', 'en'],
			['troubled', 'troubl', 'en'],
			['sized', 'size', 'en'],
			['hopping', 'hop', 'en'],
			['tanned', 'tan', 'en'],
			['falling', 'fall', 'en'],
			['hissing', 'hiss', 'en'],
			['fizzed', 'fizz', 'en'],
			['failing', 'fail', 'en'],
			['filing', 'file', 'en'],

			// Step 1c
			['happy', 'happi', 'en'],
			['sky', 'sky', 'en'],

			// Step 2
			['relational', 'relat', 'en'],
			['conditional', 'condit', 'en'],
			['rational', 'ration', 'en'],
			['valenci', 'valenc', 'en'],
			['hesitanci', 'hesit', 'en'],
			['digitizer', 'digit', 'en'],
			['antropologi', 'antropolog', 'en'],
			['conformabli', 'conform', 'en'],
			['radicalli', 'radic', 'en'],
			['differentli', 'differ', 'en'],
			['vileli', 'vile', 'en'],
			['analogousli', 'analog', 'en'],
			['vietnamization', 'vietnam', 'en'],
			['predication', 'predic', 'en'],
			['operator', 'oper', 'en'],
			['feudalism', 'feudal', 'en'],
			['decisiveness', 'decis', 'en'],
			['hopefulness', 'hope', 'en'],
			['callousness', 'callous', 'en'],
			['formaliti', 'formal', 'en'],
			['sensitiviti', 'sensit', 'en'],
			['sensibiliti', 'sensibl', 'en'],

			// Step 3
			['triplicate', 'triplic', 'en'],
			['formative', 'form', 'en'],
			['formalize', 'formal', 'en'],
			['electriciti', 'electr', 'en'],
			['electrical', 'electr', 'en'],
			['hopeful', 'hope', 'en'],
			['goodness', 'good', 'en'],

			// Step 4
			['revival', 'reviv', 'en'],
			['allowance', 'allow', 'en'],
			['inference', 'infer', 'en'],
			['airliner', 'airlin', 'en'],
			['gyroscopic', 'gyroscop', 'en'],
			['adjustable', 'adjust', 'en'],
			['defensible', 'defens', 'en'],
			['irritant', 'irrit', 'en'],
			['replacement', 'replac', 'en'],
			['adjustment', 'adjust', 'en'],
			['dependent', 'depend', 'en'],
			['adoption', 'adopt', 'en'],
			['homologou', 'homolog', 'en'],
			['communism', 'commun', 'en'],
			['activate', 'activ', 'en'],
			['angulariti', 'angular', 'en'],
			['homologous', 'homolog', 'en'],
			['effective', 'effect', 'en'],
			['bowdlerize', 'bowdler', 'en'],

			// Step 5a
			['probate', 'probat', 'en'],
			['rate', 'rate', 'en'],
			['cease', 'ceas', 'en'],

			// Step 5b
			['controll', 'control', 'en'],
			['roll', 'roll', 'en'],
		];
	}

	/**
	 * @param   string  $token   The token to stem.
	 * @param   string  $result  The expected result
	 * @param   string  $lang    The language of the token.
	 *
	 * @covers        Joomla\Language\Stemmer\Porteren::stem
	 * @covers        Joomla\Language\Stemmer\Porteren::<!public>
	 * @dataProvider  dataStemProvider
	 */
	public function testTheCorrectStemIsReturnedFromAGivenString($token, $result, $lang)
	{
		$this->assertEquals($result, $this->object->stem($token, $lang));
	}
}
