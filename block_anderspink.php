<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version details
 *
 * @package    block_anderspink
 * @copyright  2016 onwards Anders Pink Ltd <info@anderspink.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

use block_anderspink\entity\block_anderspink_apikey;
use block_anderspink\entity\block_anderspink_audiences;
use core\entity\user;

defined('MOODLE_INTERNAL') || die();

// Unfortunatly due to a bug in moodle, filelib wasn't always being included, and we need it!
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/ap_cache.php');

require_once($CFG->libdir . '/filelib.php');

class block_anderspink extends block_base
{
    private const API_HOST_V2 = 'https://anderspink.com/api/v2';
    private const API_HOST_V3 = 'https://anderspink.com/api/v3';

    /**
     * @return void
     * @throws coding_exception
     */
    public function init()
    {
        $this->title = get_string('pluginname', 'block_anderspink');
        $this->cache = new ap_cache();
    }

    /**
     * @param $article
     * @param  string  $imagePosition
     * @param  bool  $contentPreview
     * @param  bool  $showComments
     *
     * @return string
     * @throws coding_exception
     */
    public function render_article(
        $article,
        string $imagePosition = 'side',
        bool $contentPreview = false,
        bool $showComments = false
    ) {
        global $OUTPUT;

        $side  = $imagePosition === 'side';
        $extra = [];

        if ($article['domain']) {
            $extra[] = $article['domain'];
        }

        if ($article['date_published']) {
            $extra[] = $this->time2str($article['date_published']);
        }

        $image = "";
        if ($article['image']) {
            $image .= html_writer::start_div(
                ($side ? "ap-article-image-container-side" : "ap-article-image-container-top")
            );
            $image .= html_writer::div(
                '',
                ($side ? "ap-article-image-container-side-inner" : "ap-article-image-container-top-inner"),
                ['style' => "background-image:url({$article['image']})"]
            );
            $image .= html_writer::end_div();
        }

        $cutoff = 75;
        $title  = strlen(trim($article['title'])) > $cutoff ? substr(
                $article['title'],
                0,
                $cutoff
            ) . "..." : $article['title'];

        $content          = $contentPreview ? $article['content'] : '';
        $featured_comment = null;

        if ($showComments && count($article['comments']) > 0) {
            foreach ($article['comments'] as $comment) {
                if (isset($comment['pinned']) && $comment['pinned']) {
                    $featured_comment = $comment;
                }
            }

            if (!$featured_comment) {
                $featured_comment = $article['comments'][count($article['comments']) - 1];
            }
        }
        if ($featured_comment) {
            // Render links from markdown
            $featured_comment['text'] = preg_replace(
                '/\[(.*)\]\((.*)\)/',
                '<a target="_blank" href="$2">$1</a>',
                $featured_comment['text']
            );
        }

        return $OUTPUT->render_from_template('block_anderspink/render_article', [
            'ap_link'               => $article['url'],
            'ap_link_title'         => htmlspecialchars($article['title'], ENT_QUOTES),
            'image'                 => $image,
            'ap_link_content_class' => (($side && $article['image']) ? 'ap-margin-right' : ''),
            'ap_link_article_title' => htmlspecialchars($title),
            'ap_link_article_text'  => implode(' - ', $extra),
            'ap_article_content'    => (!empty($content) ? $content : false),
            'ap_article_comments'   => (!empty($featured_comment) ? $featured_comment['text'] : false),
        ]);
    }

    /**
     * @return stdClass|string|null
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_content()
    {
        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';

            return $this->content;
        }

        if (!$this->config) {
            $this->config = new stdClass();
        }

        // defaults
        if (!isset($this->config->image) || !$this->config->image) {
            $this->config->image = 'side';
        }

        if (!isset($this->config->column) || !$this->config->column) {
            $this->config->column = 1;
        }

        if (!isset($this->config->limit) || !$this->config->limit) {
            $this->config->limit = 5;
        }

        $this->config->filter_imageless = isset($this->config->filter_imageless) && $this->config->filter_imageless === '1';
        $this->config->limit            = max(min($this->config->limit, 30), 1); // Cap betwen 1-30
        $this->config->content_preview  = isset($this->config->content_preview) && $this->config->content_preview === '1';
        $this->config->comment          = isset($this->config->comment) && $this->config->comment === '1';
        $this->config->viewtype         = $this->config->viewtype ?? 'unmixed';

        if ($this->config->viewtype === 'mixed') {
            $this->config->time = $this->config->time ?? 'auto';
        }

        if (isset($this->config->title) && $this->config->title) {
            $this->title = $this->config->title;
        }

        $this->content         = new stdClass();
        $this->content->items  = [];
        $this->content->icons  = [];
        $this->content->footer = '';

        $apiKeys = block_anderspink_apikey::repository()->get();

        if (empty($apiKeys)) {
            $this->content->text = get_string('please_configure_block_api_key', 'block_anderspink');

            return $this->content;
        }

        $date        = new DateTime();
        $datenow     = $date->format('Y-m-d\TH:i:s');
        $articleHtml = [];


        // Cache key is based on the config and api key, so that it's invalidated when the config changes
        foreach ($apiKeys as $apiKey) {
            $key                     = md5(json_encode($this->config)) . $apiKey->apikey;
            $blockAnderspinkSettings = block_anderspink_audiences::repository()
                ->where('instance', $this->instance->id)
                ->where('team', $apiKey->id);

            if (empty($blockAnderspinkSettings) || !$blockAnderspinkSettings->exists()) {
                continue;
            }

            $blockAnderspinkSettings = $blockAnderspinkSettings->get();

            switch ($this->config->viewtype) {
                case "mixed":
                    $requestData = $this->getMixedTypeRequestData($blockAnderspinkSettings);
                    break;
                case "unmixed":
                    $requestData = $this->getUnmixedTypeRequestData($blockAnderspinkSettings);
                    break;
                default:
                    throw new moodle_exception('exception_wrong_view_type', 'block_anderspink');
            }

            if (empty($requestData['urls'])) {
                $this->content->text = '';
                return $this->content;
            }

            // Check the cache first...
            $response       = null;
            $stringresponse = $this->cache->get($key);

            if ($stringresponse) {
                $response = json_decode($stringresponse, true);

                if ($datenow > $response['ttl']) {
                    $response = null;
                }
            }

            if (!$response) {
                $content = [];

                foreach ($requestData['urls'] as $url) {
                    $fullresponse = download_file_content($url, ['X-Api-Key' => $apiKey->apikey], null, true);
                    $response     = json_decode($fullresponse->results, true);

                    if ($response && $response['status'] === 'success') {
                        $response['ttl'] = $requestData['dateofexpiry'];
                    }

                    if (!$response) {
                        $this->content->text = get_string('issue_loading_content', 'block_anderspink', ['err' => $fullresponse->error]);

                        return $this->content;
                    }
                    if ($response['status'] !== 'success') {
                        $this->content->text = get_string('issue_response_not_success', 'block_anderspink', ['err' => $response['message']]);
                        return $this->content;
                    }

                    $content[] = $response;
                    $articles  = array_slice($response['data']['articles'], 0, $this->config->limit);

                    foreach ($articles as $article) {
                        $articleHtml[$response['data']['name']][] = $this->render_article(
                            $article,
                            $this->config->image,
                            $this->config->content_preview,
                            $this->config->comment,
                        );
                    }
                }

                $this->cache->set($key, json_encode($content));
            }
        }

        //Clear content for actual output
        $this->content->text = '';

        foreach ($articleHtml as $index => $article) {
            if ($this->config->viewtype === 'unmixed') {
                $this->content->text .= html_writer::tag('strong', $index);
                $this->content->text .= html_writer::tag('hr', '');
            }

            if ($this->config->column === 1) {
                $this->content->text .= implode("\n", $article);
            }

            if ($this->config->column === 2) {
                $article = array_map(function ($item) {
                    return html_writer::div($item, 'ap-two-column');
                }, $article);

                $this->content->text .= html_writer::start_div('ap-columns');
                $this->content->text .= implode("\n", $article);
                $this->content->text .= html_writer::end_div();
            }
        }

        return $this->content;
    }

    /**
     * @return bool[]
     */
    public function applicable_formats(): array
    {
        return ['all' => true];
    }

    /**
     * @return bool
     */
    public function instance_allow_multiple(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function has_config()
    {
        return true;
    }

    public function cron()
    {
        // Not needed just yet
        return true;
    }

    /**
     * @param $ts
     *
     * @return false|string
     */
    private function time2str($ts)
    {
        if (!ctype_digit($ts)) {
            $ts = strtotime($ts);
        }
        $diff = time() - $ts;
        if ($diff == 0) {
            return 'now';
        } elseif ($diff > 0) {
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 60) {
                    return 'just now';
                }
                if ($diff < 120) {
                    return '1m';
                }
                if ($diff < 3600) {
                    return floor($diff / 60) . 'm';
                }
                if ($diff < 7200) {
                    return '1h';
                }
                if ($diff < 86400) {
                    return floor($diff / 3600) . 'h';
                }
            }
            if ($day_diff == 1) {
                return '1d';
            }
            if ($day_diff < 7) {
                return $day_diff . 'd';
            }
            if ($day_diff < 31) {
                return ceil($day_diff / 7) . 'w';
            }
        }

        return date('F Y', $ts);
    }

    /**
     * @param $settings
     *
     * @return array
     * @throws moodle_exception
     */
    private function getMixedTypeRequestData($settings): array
    {
        $user        = user::logged_in();
        $date        = new DateTime();
        $requestData = [];
        $briefingIds = [];
        $boardsIds   = [];

        foreach ($settings as $setting) {
            if (!$this->checkUserAudienceStatus($user->id, $setting->audience)) {
                continue;
            }

            switch ($setting->type) {
                case 'briefing':
                    $dateOfExpiry  = $date->add(new DateInterval('PT1M'))->format('Y-m-d\TH:i:s'); // 1 minute
                    $briefingIds[] = $setting->item;
                    break;
                case 'board':
                    $dateOfExpiry = $date->add(new DateInterval('PT1M'))->format('Y-m-d\TH:i:s'); // 1 minute
                    $boardsIds[]  = $setting->item;
                    break;
                default:
                    throw new moodle_exception('exception_wrong_setting_type', 'block_anderspink');
            }

            $requestData['dateofexpiry'] = $dateOfExpiry;
        }

        if (!empty($briefingIds)) {
            $briefingIds = implode(',', $briefingIds);
            $briefingUrl = self::API_HOST_V3 . "/combined-briefings/{$briefingIds}";

            $briefingUrl .= "?time={$this->config->time}";
            $briefingUrl .= "&limit={$this->config->limit}";
            $briefingUrl .= ($this->config->filter_imageless ? "&filter_imageless" : null);

            $requestData['urls'][] = $briefingUrl;
        }

        if (!empty($boardsIds)) {
            $boardsIds = implode(',', $boardsIds);
            $boardsUrl = self::API_HOST_V3 . "/combined-boards/{$boardsIds}";

            $boardsUrl .= "&limit={$this->config->limit}";
            $boardsUrl .= ($this->config->filter_imageless ? "&filter_imageless" : null);

            $requestData['urls'][] = $boardsUrl;
        }

        return $requestData;
    }

    /**
     * @param $settings
     *
     * @return array
     * @throws moodle_exception
     */
    private function getUnmixedTypeRequestData($settings): array
    {
        $user        = user::logged_in();
        $date        = new DateTime();
        $requestData = [];

        foreach ($settings as $setting) {
            if (!$this->checkUserAudienceStatus($user->id, $setting->audience)) {
                continue;
            }

            switch ($setting->type) {
                case 'briefing':
                    $dateOfExpiry = $date->add(new DateInterval('PT1M'))->format('Y-m-d\TH:i:s'); // 1 minute
                    $url          = self::API_HOST_V2 . "/briefings/{$setting->item}";
                    break;
                case 'board':
                    $dateOfExpiry = $date->add(new DateInterval('PT1M'))->format('Y-m-d\TH:i:s'); // 1 minute
                    $url          = self::API_HOST_V2 . "/boards/{$setting->item}";
                    break;
                default:
                    throw new moodle_exception('exception_wrong_setting_type', 'block_anderspink');
            }

            $url .= "?time={$setting->time}";
            $url .= "&limit={$this->config->limit}";
            $url .= ($this->config->filter_imageless ? "&filter_imageless" : null);

            $requestData['urls'][]       = $url;
            $requestData['dateofexpiry'] = $dateOfExpiry;
        }

        return $requestData;
    }

    /**
     * @param  int  $userId
     * @param   $audiences
     *
     * @return bool
     */
    private function checkUserAudienceStatus(int $userId, $audiences = null): bool
    {
        if (empty($audiences)) {
            return true;
        }

        $isCohortMember = false;
        $audiences      = explode(',', $audiences);

        foreach ($audiences as $audience) {
            if (cohort_is_member($audience, $userId) || is_primary_admin($userId)) {
                $isCohortMember = true;
            }
        }

        return $isCohortMember;
    }
}
