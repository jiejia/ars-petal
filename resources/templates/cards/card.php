<?php
/**
 * The grid card of any post: episode cards for episodes, post cards for everything else.
 *
 * @var \WP_Post $post
 * @var string $heading Heading tag.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Episode;

$episode = Episode::from($post);
$heading ??= 'h3';

$episode
    ? Template::render('cards/episode', ['episode' => $episode, 'heading' => $heading])
    : Template::render('cards/post', ['post' => $post, 'heading' => $heading]);
