<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\RelativeUrlsFeature;

class RelativeUrlsFeatureTest extends \WP_UnitTestCase
{
    protected static $editorId = null;

    public static function wpSetUpBeforeClass($factory)
    {
        self::$editorId = $factory->user->create([
            'role' => 'editor'
        ]);
    }

    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(RelativeUrlsFeature::class);

        wp_set_current_user(self::$editorId);
        _set_cron_array([]);
    }

    public function tearDown()
    {
        FeatureFactory::unload(RelativeUrlsFeature::class);

        parent::tearDown();
    }

    public function testFeedLink()
    {
        apply_filters('template_include', get_index_template());

        $this->assertStringStartsNotWith(network_home_url(), get_feed_link());
    }

    public function testThePermalink()
    {
        apply_filters('template_include', get_index_template());

        $this->set_permalink_structure('/%year%/%monthnum%/%day%/%postname%/');

        $post = [
            'post_author' => self::$editorId,
            'post_status' => 'publish',
            'post_content' => rand_str(),
            'post_title' => '',
            'post_date' => '2007-10-31 06:15:00'
        ];

        // insert a post and make sure the ID is ok
        $id = wp_insert_post($post);

        $plink = get_echo('the_permalink', [$id]);

        $this->assertEquals('/2007/10/31/' . $id . '/', $plink);
    }

    public function testWpGetAttachmentUrl()
    {
        apply_filters('template_include', get_index_template());

        $this->set_permalink_structure('/%postname%/');

        $postName = 'foobar';
        $id = wp_insert_attachment([
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_name' => $postName
        ]);

        $attachmentURL = wp_get_attachment_url($id);

        $this->assertEquals('/' . $postName . '/', $attachmentURL);
    }

    public function testGetCommentLink()
    {
        apply_filters('template_include', get_index_template());

        $this->set_permalink_structure('/%postname%/');

        $now = time();
        $postName = 'foo';
        $postId = self::factory()->post->create([
            'post_name' => $postName
        ]);
        $commentId = self::factory()->comment->create([
            'comment_post_ID' => $postId,
            'comment_content' => '1',
            'comment_date_gmt' => date('Y-m-d H:i:s', $now - 100)
        ]);

        $commentLink = get_comment_link($commentId);

        $this->assertEquals('/' . $postName . '/#comment-' . $commentId, $commentLink);
    }

    public function testGetCommentsLink()
    {
        apply_filters('template_include', get_index_template());

        $this->set_permalink_structure('/%postname%/');

        $postName = 'foobar';
        $id = wp_insert_attachment([
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_name' => $postName
        ]);

        $commentsLink = get_comments_link($id);

        $this->assertEquals('/' . $postName . '/#respond', $commentsLink);
    }

    public function testMonthLink()
    {
        apply_filters('template_include', get_index_template());

        $this->set_permalink_structure('/%postname%/');

        $monthLink = get_month_link(2017, 02);

        $this->assertEquals('/2017/02/', $monthLink);
    }

    public function testDayLink()
    {
        apply_filters('template_include', get_index_template());

        $this->set_permalink_structure('/%postname%/');

        $dayLint = get_day_link(2017, 02, 04);

        $this->assertEquals('/2017/02/04/', $dayLint);
    }

    public function testYearLink()
    {
        apply_filters('template_include', get_index_template());

        $this->set_permalink_structure('/%postname%/');

        $yearLink = get_year_link(2017);

        $this->assertEquals('/2017/', $yearLink);
    }

    public function testTermLink()
    {
        apply_filters('template_include', get_index_template());

        $this->set_permalink_structure('/%year%/%monthnum%/%day%/%postname%/');

        $taxonomyName = 'wptests_tax';
        $termSlug = 'foo';
        register_taxonomy($taxonomyName, 'post', [
            'hierarchical' => true
        ]);

        $termId = self::factory()->term->create([
            'taxonomy' => $taxonomyName,
            'slug' => $termSlug
        ]);

        $termLink = get_term_link($termId);

        $this->assertEquals('/' . $taxonomyName . '/' . $termSlug . '/', $termLink);

        wp_delete_term($termId, 'wptests_tax');
    }

    public function testTheAuthorLink()
    {
        apply_filters('template_include', get_index_template());

        $this->set_permalink_structure('/%postname%/');

        $authorPostsURL = get_author_posts_url(self::$editorId, 'foo');

        $this->assertEquals('/author/foo/', $authorPostsURL);
    }

    public function testTheAuthorPostsLink()
    {
        apply_filters('template_include', get_index_template());

        $this->set_permalink_structure('/%postname%/');

        global $authordata;

        $authordata = get_user_by('ID', self::$editorId);

        $a = new \SimpleXMLElement(get_the_author_posts_link());

        $this->assertEquals('/author/' . $authordata->user_nicename . '/', $a['href']);
    }
}
