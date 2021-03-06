<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\SitemapBundle\Tests\Provider;

use Symfony\Component\HttpFoundation\Request;
use Tadcka\Bundle\SitemapBundle\Model\Manager\NodeTranslationManagerInterface;
use Tadcka\Bundle\SitemapBundle\Model\NodeTranslationInterface;
use Tadcka\Bundle\SitemapBundle\Provider\PageNodeProvider;
use Tadcka\Bundle\SitemapBundle\Security\PageSecurityManager;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 8/7/14 8:43 PM
 */
class PageNodeProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PageNodeProvider
     */
    private $pageNodeProvider;

    /**
     * @var NodeTranslationManagerInterface
     */
    private $nodeTranslationManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->nodeTranslationManager = $this
            ->getMock('Tadcka\\Bundle\\SitemapBundle\\Model\\Manager\\NodeTranslationManagerInterface');

        $this->pageNodeProvider = new PageNodeProvider(
            $this->nodeTranslationManager,
            new PageSecurityManager($this->getMock('Symfony\\Component\\Security\\Core\\SecurityContextInterface'))
        );
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetNodeTranslationOr404WithEmptyRequest()
    {
        $this->pageNodeProvider->getNodeTranslationOr404(new Request());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetNodeTranslationOr404WithEmptyNodeTranslation()
    {
        $this->pageNodeProvider->getNodeTranslationOr404($this->createRequest());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetNodeTranslationOr404WithCanNotView()
    {
        $this->nodeTranslationManager
            ->expects($this->any())
            ->method('findTranslationByRoute')
            ->willReturn($this->getMock('Tadcka\\Bundle\\SitemapBundle\\Model\\NodeTranslation'));

        $this->pageNodeProvider->getNodeTranslationOr404($this->createRequest());
    }

    public function testGetNodeTranslationOr404()
    {
        $nodeTranslation = $this->getMock('Tadcka\\Bundle\\SitemapBundle\\Model\\NodeTranslation');
        $nodeTranslation->expects($this->any())->method('isOnline')->willReturn(true);

        $this->nodeTranslationManager
            ->expects($this->any())
            ->method('findTranslationByRoute')
            ->willReturn($nodeTranslation);

        $this->assertEquals($nodeTranslation, $this->pageNodeProvider->getNodeTranslationOr404($this->createRequest()));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetNodeOr404WithoutNode()
    {
        $nodeTranslation = $this->getMock('Tadcka\\Bundle\\SitemapBundle\\Model\\NodeTranslation');
        $nodeTranslation->expects($this->any())->method('isOnline')->willReturn(true);

        $this->nodeTranslationManager
            ->expects($this->any())
            ->method('findTranslationByRoute')
            ->willReturn($nodeTranslation);

        $this->pageNodeProvider->getNodeOr404($this->createRequest());
    }

    public function testGetNodeOr404()
    {
        $node = $this->getMock('Tadcka\\Bundle\\SitemapBundle\\Model\\Node');
        $nodeTranslation = $this->getMock('Tadcka\\Bundle\\SitemapBundle\\Model\\NodeTranslation');
        $nodeTranslation->expects($this->any())->method('isOnline')->willReturn(true);
        $nodeTranslation->expects($this->any())->method('getNode')->willReturn($node);

        $this->nodeTranslationManager
            ->expects($this->any())
            ->method('findTranslationByRoute')
            ->willReturn($nodeTranslation);

        $this->assertEquals($node, $this->pageNodeProvider->getNodeOr404($this->createRequest()));
    }

    private function createRequest()
    {
        $request = new Request();
        $request->query->replace(
            array(
                '_route_params' => array(
                    '_route_object' => $this->getMock('Tadcka\\Bundle\\RoutingBundle\\Model\\Route')
                )
            )
        );

        return $request;
    }
}
