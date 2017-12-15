<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NoXmlrpcPingbackFeature;

class NoXmlrpcPingbackFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NoXmlrpcPingbackFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(NoXmlrpcPingbackFeature::class);

        parent::tearDown();
    }

    public function testFilterXmlrpcMethod()
    {
        $wpXmlrpcServer = new \wp_xmlrpc_server();

        $this->assertArrayNotHasKey('pingback.ping', $wpXmlrpcServer->methods);
        $this->assertArrayNotHasKey('pingback.extensions.getPingbacks', $wpXmlrpcServer->methods);
    }

    public function testXmlrpc()
    {
        $this->expectException(\WPDieException::class);

        $wpXmlrpcServer = new \wp_xmlrpc_server();
        $wpXmlrpcServer->pingback_ping([0, PHP_INT_MAX]);
    }

    public function testXmlrpcAction()
    {
        $wpXmlrpcServer = new \wp_xmlrpc_server();
        $this->assertEquals('Hello!', $wpXmlrpcServer->sayHello());
        $this->assertTrue(is_array($wpXmlrpcServer->mt_supportedMethods()));
    }

    public function testXmlrpcPingbackPingAction()
    {
        $this->expectException(\WPDieException::class);

        $wpXmlrpcServer = new \wp_xmlrpc_server();
        $wpXmlrpcServer->pingback_ping([0, PHP_INT_MAX]);
    }

    public function testXmlrpcPingbackExtensionsGetPingbacksAction()
    {
        $this->expectException(\WPDieException::class);

        $wpXmlrpcServer = new \wp_xmlrpc_server();
        $wpXmlrpcServer->pingback_extensions_getPingbacks('http://example.com');
    }

    // Todo best testing
    public function testSendHeaders()
    {
        global $wp;

        add_filter('wp_headers', function ($header) {
            $this->assertArrayNotHasKey('X-Pingback', $header);
        }, PHP_INT_MAX);

        $wp->send_headers();
    }

    public function testKillPingbackUrl()
    {
        $this->assertEmpty(get_bloginfo('pingback_url', 'display'));
    }
}
