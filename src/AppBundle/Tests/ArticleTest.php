<?php

namespace AppBundle\Tests;

use AppBundle\AppBundle;
use AppBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

final class ArticleTest extends KernelTestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self:self::bootKernel();
        $this->container = static::$kernel->getContainer();
        $this->container->get('doctrine')->getConnection()->beginTransaction();
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->rollback();

        parent::tearDown();

        $this->container->get('doctrine')->getManager()->close();
    }

    public function testIfSluggableExtensionIsWorking()
    {
        $article = new Article();
        $article->setTitle('Article title');
        $article->setBody('Article body');
        $em = $this->container->get('doctrine')->getManager();

        $em->persist($article);
        $em->flush();

        $this->assertEquals('article-title', $article->getSlug());
    }
}
