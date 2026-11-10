<?php

namespace Jiejia\ArsPetal\Podcast;

defined('ABSPATH') || exit;

/**
 * One A Ripple Song episode, read from the plugin's post meta (_ars_podcast_{field}).
 */
final class Episode
{
    /**
     * Episode numbers by publish order (oldest = 1), keyed by post ID, like the plugin's player.
     *
     * @var array<int,int>|null
     */
    private static ?array $numbers = null;

    public function __construct(public readonly \WP_Post $post)
    {
    }

    /**
     * Return the episode for a post, or null when it is not an episode.
     *
     * @param int|\WP_Post|null $post Post, or the current one.
     * @return self|null
     */
    public static function from(int|\WP_Post|null $post = null): ?self
    {
        $post = get_post($post);

        return $post instanceof \WP_Post && $post->post_type === Podcast::POST_TYPE ? new self($post) : null;
    }

    public function id(): int
    {
        return $this->post->ID;
    }

    /**
     * Read a plugin field.
     *
     * @param string $key Field key, e.g. "audio_file".
     * @return mixed
     */
    public function meta(string $key): mixed
    {
        return get_post_meta($this->post->ID, '_' . Podcast::POST_TYPE . '_' . $key, true);
    }

    /**
     * The short episode label, e.g. "EP.12".
     *
     * @return string
     */
    public function label(): string
    {
        /* translators: %s: episode number. */
        return sprintf(__('EP.%s', 'ars-petal'), $this->number());
    }

    /**
     * The explicit episode number, else its place in publish order.
     *
     * @return int
     */
    public function number(): int
    {
        $number = (int) $this->meta('episode_number');

        if ($number > 0) {
            return $number;
        }

        if (self::$numbers === null) {
            $ids = get_posts([
                'post_type' => Podcast::POST_TYPE,
                'post_status' => 'publish',
                'numberposts' => -1,
                'orderby' => 'date',
                'order' => 'ASC',
                'fields' => 'ids',
            ]);
            self::$numbers = $ids === [] ? [] : array_combine(array_map('intval', $ids), range(1, count($ids)));
        }

        return self::$numbers[$this->post->ID] ?? 0;
    }

    /**
     * Duration in seconds.
     *
     * @return int
     */
    public function duration(): int
    {
        return max(0, (int) $this->meta('duration'));
    }

    public function audio(): string
    {
        return (string) $this->meta('audio_file');
    }

    /**
     * The episode image, else the featured image, else one of the theme's stand-in covers, else the podcast cover.
     *
     * @param string $size Image size for the featured image.
     * @return string
     */
    public function cover(string $size = 'medium_large'): string
    {
        return (string) $this->meta('episode_image')
            ?: (string) get_the_post_thumbnail_url($this->post, $size)
            ?: Covers::for($this->id())
            ?: Podcast::setting('cover');
    }

    /**
     * The first tag, which the theme shows as the episode's topic.
     *
     * @return \WP_Term|null
     */
    public function tag(): ?\WP_Term
    {
        $terms = get_the_terms($this->post, 'post_tag');

        return is_array($terms) && $terms !== [] ? $terms[0] : null;
    }

    /**
     * The episode's hosts.
     *
     * @return array<int,\WP_User>
     */
    public function hosts(): array
    {
        return $this->users($this->meta('hosts'));
    }

    /**
     * The episode's guests.
     *
     * @return array<int,\WP_User>
     */
    public function guests(): array
    {
        return $this->users($this->meta('guests'));
    }

    /**
     * Host names joined like "Asen / Xiaolu".
     *
     * @return string
     */
    public function hostNames(): string
    {
        return implode(' / ', array_map(static fn (\WP_User $user): string => $user->display_name, $this->hosts()));
    }

    public function plays(): int
    {
        return max(0, (int) $this->meta('play_count'));
    }

    /**
     * Whether the page's player can play this episode.
     *
     * @return bool
     */
    public function playable(): bool
    {
        return $this->audio() !== '' && Podcast::playerActive();
    }

    /**
     * @return array<int,array{start:int,end:int,title:string,url:string}>
     */
    public function chapters(): array
    {
        return Chapters::forEpisode($this);
    }

    /**
     * @return array{url:string,cues:array<int,array{start:int|null,speaker:string,text:string}>}
     */
    public function transcript(): array
    {
        return Transcript::forEpisode($this);
    }

    /**
     * The episode in the shape the plugin's player stores, for ARippleSongPlayer.register().
     *
     * @return array<string,mixed>
     */
    public function playerData(): array
    {
        $text = static fn (string $html): string => trim(html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES, 'UTF-8'));
        $tag = $this->tag();

        return [
            'id' => $this->post->ID,
            'number' => $this->number(),
            'date' => $text(get_the_date('', $this->post)),
            'title' => $text(get_the_title($this->post)),
            'url' => (string) get_permalink($this->post),
            'audio' => $this->audio(),
            'cover' => $this->cover('medium'),
            'duration' => $this->duration(),
            'excerpt' => $text(get_the_excerpt($this->post)),
            'category' => $tag ? $tag->name : '',
            'hosts' => $this->hostNames(),
            'plays' => $this->plays(),
        ];
    }

    /**
     * Return the users behind stored user IDs.
     *
     * @param mixed $ids Stored user IDs.
     * @return array<int,\WP_User>
     */
    private function users(mixed $ids): array
    {
        $users = [];

        foreach (array_unique(array_map('intval', is_array($ids) ? $ids : [])) as $userId) {
            $user = $userId > 0 ? get_userdata($userId) : false;

            if ($user instanceof \WP_User) {
                $users[] = $user;
            }
        }

        return $users;
    }
}
