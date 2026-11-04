<?php

namespace Jiejia\ArsPetal\Comments;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;

/**
 * Comment form and comment list markup: Petal's rounded fields and black pill button.
 */
final class CommentForm
{
    /**
     * Register comment hooks.
     *
     * @return void
     */
    public function register(): void
    {
        add_filter('comment_form_defaults', [$this, 'defaults']);
        add_filter('comment_form_default_fields', [$this, 'fields']);
    }

    /**
     * Style the form wrapper, textarea and submit button.
     *
     * @param array<string,mixed> $defaults Form arguments.
     * @return array<string,mixed>
     */
    public function defaults(array $defaults): array
    {
        return array_merge($defaults, [
            'class_form' => 'comment-form mt-4 grid gap-3 sm:grid-cols-2',
            'title_reply' => __('Leave a comment', 'ars-petal'),
            'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title display text-xl">',
            'title_reply_after' => '</h3>',
            'cancel_reply_before' => ' <small class="ml-2 font-normal">',
            'comment_notes_before' => '<p class="comment-notes mute text-xs sm:col-span-2">' . esc_html__('Your e-mail address will not be published. Required fields are marked *', 'ars-petal') . '</p>',
            'comment_field' => $this->field('comment', __('Comment', 'ars-petal'), '<textarea id="comment" name="comment" rows="4" maxlength="65525" required class="field"></textarea>', true),
            'class_submit' => 'btn-black h-12 cursor-pointer rounded-full px-7 text-sm',
            'submit_field' => '<p class="form-submit sm:col-span-2">%1$s %2$s</p>',
        ]);
    }

    /**
     * Name, e-mail and website fields.
     *
     * @param array<string,string> $fields Field markup.
     * @return array<string,string>
     */
    public function fields(array $fields): array
    {
        $commenter = wp_get_current_commenter();
        $input = static fn (string $name, string $type, string $value, bool $required): string => sprintf(
            '<input id="%1$s" name="%1$s" type="%2$s" value="%3$s"%4$s class="field">',
            esc_attr($name),
            esc_attr($type),
            esc_attr($value),
            $required ? ' required' : ''
        );
        $required = (bool) get_option('require_name_email');

        $fields['author'] = $this->field('author', __('Name', 'ars-petal'), $input('author', 'text', $commenter['comment_author'], $required), $required, false);
        $fields['email'] = $this->field('email', __('E-mail', 'ars-petal'), $input('email', 'email', $commenter['comment_author_email'], $required), $required, false);
        $fields['url'] = $this->field('url', __('Website', 'ars-petal'), $input('url', 'url', $commenter['comment_author_url'], false), false);

        if (isset($fields['cookies'])) {
            $fields['cookies'] = str_replace('comment-form-cookies-consent', 'comment-form-cookies-consent flex items-center gap-2 text-sm sm:col-span-2', $fields['cookies']);
        }

        return $fields;
    }

    /**
     * Print one comment (wp_list_comments callback; WordPress closes the <li>).
     *
     * @param \WP_Comment $comment Comment.
     * @param array<string,mixed> $args List arguments.
     * @param int $depth Nesting depth.
     * @return void
     */
    public static function comment(\WP_Comment $comment, array $args, int $depth): void
    {
        Template::render('comments/comment', ['comment' => $comment, 'args' => $args, 'depth' => $depth]);
    }

    /**
     * Wrap a field in a label.
     *
     * @param string $name Field name.
     * @param string $label Label text.
     * @param string $control Control markup.
     * @param bool $required Whether to mark it required.
     * @param bool $wide Whether it spans both columns.
     * @return string
     */
    private function field(string $name, string $label, string $control, bool $required, bool $wide = true): string
    {
        return sprintf(
            '<p class="comment-form-%1$s flex flex-col gap-1.5 text-sm%2$s"><label for="%1$s">%3$s%4$s</label>%5$s</p>',
            esc_attr($name),
            $wide ? ' sm:col-span-2' : '',
            esc_html($label),
            $required ? ' *' : '',
            $control
        );
    }
}
