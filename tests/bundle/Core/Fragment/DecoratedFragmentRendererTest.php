<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\Bundle\Core\Fragment;

use Ibexa\Bundle\Core\Fragment\DecoratedFragmentRenderer;
use Ibexa\Bundle\Core\Fragment\SiteAccessSerializer;
use Ibexa\Core\MVC\Symfony\Component\Serializer\SerializerTrait;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Component\HttpKernel\Fragment\RoutableFragmentRenderer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @covers \Ibexa\Bundle\Core\Fragment\DecoratedFragmentRenderer
 */
class DecoratedFragmentRendererTest extends FragmentRendererBaseTest
{
    use SerializerTrait;

    /** @var \Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected FragmentRendererInterface $innerRenderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->innerRenderer = $this->createMock(FragmentRendererInterface::class);
    }

    public function testSetFragmentPathNotRoutableRenderer()
    {
        $matcher = $this->createMock(SiteAccess\URILexer::class);
        $siteAccess = new SiteAccess('test', 'test', $matcher);
        $matcher
            ->expects(self::never())
            ->method('analyseLink');

        $renderer = $this->getRenderer();
        $renderer->setSiteAccess($siteAccess);
        if ($renderer instanceof RoutableFragmentRenderer) {
            $renderer->setFragmentPath('foo');
        }
    }

    public function testSetFragmentPath()
    {
        $matcher = $this->createMock(SiteAccess\URILexer::class);
        $siteAccess = new SiteAccess('test', 'test', $matcher);
        $matcher
            ->expects(self::once())
            ->method('analyseLink')
            ->with('/foo')
            ->will(self::returnValue('/bar/foo'));

        $innerRenderer = $this->createMock(RoutableFragmentRenderer::class);
        $innerRenderer
            ->expects(self::once())
            ->method('setFragmentPath')
            ->with('/bar/foo');
        $renderer = new DecoratedFragmentRenderer($innerRenderer, new SiteAccessSerializer($this->getSerializer()));
        $renderer->setSiteAccess($siteAccess);
        $renderer->setFragmentPath('/foo');
    }

    public function testGetName()
    {
        $name = 'test';
        $this->innerRenderer
            ->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($name));

        $renderer = $this->getRenderer();
        self::assertSame($name, $renderer->getName());
    }

    public function testRendererAbsoluteUrl()
    {
        $url = 'http://phoenix-rises.fm/foo/bar';
        $request = new Request();
        $options = ['foo' => 'bar'];
        $expectedReturn = '/_fragment?foo=bar';
        $this->innerRenderer
            ->expects(self::once())
            ->method('render')
            ->with($url, $request, $options)
            ->will(self::returnValue($expectedReturn));

        $renderer = $this->getRenderer();
        self::assertSame($expectedReturn, $renderer->render($url, $request, $options));
    }

    public function testRendererControllerReference()
    {
        $reference = new ControllerReference('FooBundle:bar:baz');
        $matcher = new SiteAccess\Matcher\URIElement(1);
        $siteAccess = new SiteAccess(
            'test',
            'test',
            $matcher
        );
        $request = new Request();
        $request->attributes->set('siteaccess', $siteAccess);
        $options = ['foo' => 'bar'];
        $expectedReturn = '/_fragment?foo=bar';
        $this->innerRenderer
            ->expects(self::once())
            ->method('render')
            ->with($reference, $request, $options)
            ->will(self::returnValue($expectedReturn));

        $renderer = $this->getRenderer();
        self::assertSame($expectedReturn, $renderer->render($reference, $request, $options));
        self::assertTrue(isset($reference->attributes['serialized_siteaccess']));
        $serializedSiteAccess = json_encode($siteAccess);
        self::assertSame($serializedSiteAccess, $reference->attributes['serialized_siteaccess']);
        self::assertTrue(isset($reference->attributes['serialized_siteaccess_matcher']));
        self::assertSame(
            $this->getSerializer()->serialize(
                $siteAccess->matcher,
                'json',
                [AbstractNormalizer::IGNORED_ATTRIBUTES => ['request']]
            ),
            $reference->attributes['serialized_siteaccess_matcher']
        );
    }

    public function getRequest(SiteAccess $siteAccess): Request
    {
        $request = new Request();
        $request->attributes->set('siteaccess', $siteAccess);

        return $request;
    }

    /**
     * @return \Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface&\Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware
     */
    public function getRenderer(): FragmentRendererInterface
    {
        return new DecoratedFragmentRenderer($this->innerRenderer, new SiteAccessSerializer($this->getSerializer()));
    }
}
