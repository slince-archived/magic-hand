<?php
/**
 * slince magic hand library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\MagicHand\Tests;

use Slince\MagicHand\MagicHand;

class MagicHandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MagicHand
     */
    protected $magicHand;

    function setUp()
    {
    }

    function testConstruct()
    {
        $magicHand = new MagicHand('src', 'dst', [300, 200]);
        $this->assertInstanceOf('\\Symfony\\Component\\Filesystem\\Filesystem', $magicHand->getFilesystem());
        $this->assertInstanceOf('\\Symfony\\Component\\Finder\\Finder', $magicHand->getFinder());
        $this->assertEquals(300, $magicHand->getThumbBox()->getWidth());
        $this->assertEquals(200, $magicHand->getThumbBox()->getHeight());
    }
}