<?php

use DigitLab\BladeIcons\IconRenderer;
use Mockery as m;

class IconRendererTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCannotConstructWithBadCachePath()
    {
        new IconRenderer($this->getFiles(), [__DIR__], null);
    }

    public function testCanGetClassPrefix()
    {
        $renderer = new IconRenderer($this->getFiles(), [__DIR__], __DIR__);

        $this->assertEquals('icon', $renderer->getClassPrefix());
    }

    public function testCanSetClassPrefix()
    {
        $renderer = new IconRenderer($this->getFiles(), [__DIR__], __DIR__);
        $renderer->setClassPrefix('test');

        $this->assertEquals('test', $renderer->getClassPrefix());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCannotFileIconThrowsException()
    {
        $renderer = new IconRenderer($files = $this->getFiles(), [__DIR__], __DIR__);

        $files->shouldReceive('exists')->once()->with(__DIR__.'/foo.svg')->andReturn(false);

        $renderer->render('foo');
    }

    public function testIsExpiredReturnsTrueWhenModificationTimesWarrant()
    {
        $renderer = new IconRenderer($files = $this->getFiles(), [__DIR__], __DIR__);
        $key = $renderer->getCompiledKey('foo');

        $files->shouldReceive('exists')->once()->with(__DIR__.'/'.$key.'.svg')->andReturn(true);
        $files->shouldReceive('lastModified')->once()->with('foo')->andReturn(100);
        $files->shouldReceive('lastModified')->once()->with(__DIR__.'/'.$key.'.svg')->andReturn(0);

        $this->assertTrue($renderer->isExpired('foo'));
    }

    public function testCompilePathIsProperlyCreated()
    {
        $renderer = new IconRenderer($this->getFiles(), [__DIR__], __DIR__);
        $key = $renderer->getCompiledKey('foo');

        $this->assertEquals(__DIR__.'/'.$key.'.svg', $renderer->getCompiledPath('foo'));
    }

    public function testRenderCompilesFileAndReturnsContents()
    {
        $renderer = new IconRenderer($files = $this->getFiles(), [__DIR__], __DIR__);
        $key = $renderer->getCompiledKey(__DIR__.'/foo.svg', ['test' => 'test']);

        $files->shouldReceive('exists')->once()->with(__DIR__.'/foo.svg')->andReturn(true);
        $files->shouldReceive('exists')->once()->with(__DIR__.'/'.$key.'.svg')->andReturn(false);
        $files->shouldReceive('get')->once()->with(__DIR__.'/foo.svg')->andReturn('<tag></tag>');
        $files->shouldReceive('put')->once()->with(__DIR__.'/'.$key.'.svg', '<tag class="icon icon-foo" test="test"/>');
        $files->shouldReceive('get')->once()->with(__DIR__.'/'.$key.'.svg');

        $renderer->render('foo', ['test' => 'test']);
    }

    protected function getFiles()
    {
        return m::mock('Illuminate\Filesystem\Filesystem');
    }
}
