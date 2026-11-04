<?php
/**
 * The list row of any post: episode rows for episodes, post rows for everything else.
 *
 * @var \WP_Post $post
 * @var string $heading Heading tag.
 * @var bool $badge Whether to show the content type badge.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Episode;

$episode = Episode::from($post);
$heading ??= 'h3';
$badge ??= false;

$episode
    ? Template::render('cards/episode-row', ['episode' => $episode, 'heading' => $heading, 'badge' => $badge])
    : Template::render('cards/post-row', ['post' => $post, 'heading' => $heading, 'badge' => $badge]);
