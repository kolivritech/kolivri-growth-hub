<?php
/**
 * Plugin Name: Kolivri Growth Hub (Plays #1, #2, #6, #7, #10)
 * Plugin URI: https://kolivri.com/he/
 * Description: Last-Mile Interactive Growth Hub for Kolivri AI Voice: Dead Invoice Recovery ([kolivri_invoice_recovery_app]), Trojan Horse Caller ([kolivri_trojan_caller_app]), Parallel-Dial FOMO Dispatch ([kolivri_fomo_dispatch_app]), MCP Developer Hub ([kolivri_mcp_developer_hub]), and Ghost Mini-Site Preview ([kolivri_ghost_minisite_preview]).
 * Version: 1.0.0
 * Author: Kolivri
 * Author URI: https://kolivri.com/he/
 * License: GPL v2 or later
 * Text Domain: kolivri-growth-hub
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('KOLIVRI_GROWTH_HUB_VERSION')) {
    define('KOLIVRI_GROWTH_HUB_VERSION', '1.0.0');
}

if (!defined('KOLIVRI_GROWTH_HUB_PATH')) {
    $kgh_dir = function_exists('plugin_dir_path')
        ? plugin_dir_path(__FILE__)
        : rtrim(dirname(__FILE__), '/\\') . '/';
    define('KOLIVRI_GROWTH_HUB_PATH', $kgh_dir);
}

require_once KOLIVRI_GROWTH_HUB_PATH . 'includes/class-kolivri-growth-hub.php';

class Kolivri_Growth_Hub {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if (function_exists('add_shortcode')) {
            add_shortcode('kolivri_invoice_recovery_app', array($this, 'render_invoice_recovery_shortcode'));
            add_shortcode('kolivri_trojan_caller_app', array($this, 'render_trojan_caller_shortcode'));
            add_shortcode('kolivri_fomo_dispatch_app', array($this, 'render_fomo_dispatch_shortcode'));
            add_shortcode('kolivri_mcp_developer_hub', array($this, 'render_mcp_developer_hub_shortcode'));
            add_shortcode('kolivri_ghost_minisite_preview', array($this, 'render_ghost_minisite_shortcode'));
            add_shortcode('kolivri_competitor_alternative', array($this, 'render_competitor_alternative_shortcode'));
        }
        if (function_exists('add_action')) {
            add_action('init', array($this, 'handle_ai_discovery_endpoints'), 1);
            add_action('template_redirect', array($this, 'handle_ai_discovery_endpoints'), 1);
            add_action('rest_api_init', array($this, 'register_rest_routes'));
            add_action('wp_footer', array($this, 'render_receiver_continuity_script'));
        }
        if (function_exists('add_filter')) {
            add_filter('the_content', array($this, 'filter_blog_post_content'), 20);
            add_filter('robots_txt', array($this, 'filter_robots_txt'), 20, 2);
        }
    }

    public function filter_robots_txt($output, $public) {
        if ((string) $public === '0') {
            return $output;
        }
        $extra = "\n# AI & LLM Discovery (GEO)\nLLMs-Txt: https://kolivri.com/llms.txt\nLLMs-Full-Txt: https://kolivri.com/llms-full.txt\nPricing-Md: https://kolivri.com/pricing.md\n";
        if (strpos((string) $output, 'LLMs-Txt:') === false) {
            $output = rtrim((string) $output) . "\n" . $extra;
        }
        return $output;
    }

    /**
     * Serve `/llms.txt`, `/llms-full.txt`, `/pricing.md`, and `/pricing.txt` as
     * `Content-Type: text/plain; charset=utf-8` with `200 OK`.
     *
     * @param string|null $request_uri Optional URI override for unit testing.
     * @param bool        $exit        Whether to echo and exit (true in WP runtime, false in tests).
     * @return array|null
     */
    public function handle_ai_discovery_endpoints($request_uri = null, $exit = true) {
        $uri = (is_string($request_uri) && $request_uri !== '')
            ? $request_uri
            : (isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '');
        if ($uri === '') {
            return null;
        }

        $path = (string) parse_url($uri, PHP_URL_PATH);
        $path = '/' . ltrim(rtrim($path, '/'), '/');
        if (strpos($path, '/he/') === 0) {
            $path = substr($path, 3);
        }

        $body = null;
        $content_type = 'text/plain; charset=utf-8';
        $cors = false;
        if ($path === '/llms.txt') {
            $body = Kolivri_Growth_Hub_Engine::getLlmsTxtContent();
        } elseif ($path === '/llms-full.txt') {
            $body = Kolivri_Growth_Hub_Engine::getLlmsTxtContent() . "\n\n---\n\n" . Kolivri_Growth_Hub_Engine::getPricingMdContent();
        } elseif ($path === '/pricing.md' || $path === '/pricing.txt') {
            $body = Kolivri_Growth_Hub_Engine::getPricingMdContent();
        } elseif ($path === '/kolivri2026indexnow88a1b2c3.txt') {
            $body = 'kolivri2026indexnow88a1b2c3';
        } elseif ($path === '/widget.js') {
            $body = Kolivri_Growth_Hub_Engine::getEmbedWidgetJsContent();
            $content_type = 'application/javascript; charset=utf-8';
            $cors = true;
        }

        if ($body === null) {
            return null;
        }

        $response = array(
            'status'       => 200,
            'content_type' => $content_type,
            'body'         => $body,
        );

        if ($exit) {
            if (function_exists('status_header')) {
                status_header(200);
            }
            if (!headers_sent()) {
                header('HTTP/1.1 200 OK');
                header('Content-Type: ' . $content_type);
                header('X-Robots-Tag: all');
                if ($cors) {
                    header('Access-Control-Allow-Origin: *');
                }
            }
            echo $body;
            exit;
        }

        return $response;
    }

    public function getLlmsTxtContent() {
        return Kolivri_Growth_Hub_Engine::getLlmsTxtContent();
    }

    public function getPricingMdContent() {
        return Kolivri_Growth_Hub_Engine::getPricingMdContent();
    }

    public function getEmbedWidgetJsContent() {
        return Kolivri_Growth_Hub_Engine::getEmbedWidgetJsContent();
    }

    /**
     * Register local WordPress REST fallback routes mirroring `https://api.kolivri.co.il/api/v1/growth/*`.
     */
    public function register_rest_routes() {
        if (!function_exists('register_rest_route')) {
            return;
        }

        register_rest_route('kolivri/v1', '/growth/invoice-recovery/preview', array(
            'methods'             => 'POST',
            'callback'            => array($this, 'handle_rest_invoice_preview'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('kolivri/v1', '/growth/trojan-caller/mission', array(
            'methods'             => 'POST',
            'callback'            => array($this, 'handle_rest_trojan_mission'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('kolivri/v1', '/growth/fomo-arbitrage/race', array(
            'methods'             => 'POST',
            'callback'            => array($this, 'handle_rest_fomo_race'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('kolivri/v1', '/growth/fomo-arbitrage/claim', array(
            'methods'             => 'POST',
            'callback'            => array($this, 'handle_rest_fomo_race'),
            'permission_callback' => '__return_true',
        ));
    }

    public function handle_rest_invoice_preview($request) {
        $params = is_object($request) && method_exists($request, 'get_json_params')
            ? (array) $request->get_json_params()
            : (array) $request;
        $invoices = isset($params['invoices']) && is_array($params['invoices']) ? $params['invoices'] : array();
        $result = Kolivri_Growth_Hub_Engine::previewInvoiceRecovery($invoices);
        return function_exists('rest_ensure_response') ? rest_ensure_response($result) : $result;
    }

    public function handle_rest_trojan_mission($request) {
        $params = is_object($request) && method_exists($request, 'get_json_params')
            ? (array) $request->get_json_params()
            : (array) $request;
        $result = Kolivri_Growth_Hub_Engine::buildTrojanMission($params);
        return function_exists('rest_ensure_response') ? rest_ensure_response($result) : $result;
    }

    public function handle_rest_fomo_race($request) {
        $params = is_object($request) && method_exists($request, 'get_json_params')
            ? (array) $request->get_json_params()
            : (array) $request;
        $result = Kolivri_Growth_Hub_Engine::simulateFomoDispatch($params);
        return function_exists('rest_ensure_response') ? rest_ensure_response($result) : $result;
    }

    public function render_invoice_recovery_shortcode($atts = array()) {
        return Kolivri_Growth_Hub_Engine::renderInvoiceRecoveryApp((array) $atts);
    }

    public function render_trojan_caller_shortcode($atts = array()) {
        return Kolivri_Growth_Hub_Engine::renderTrojanCallerApp((array) $atts);
    }

    public function render_fomo_dispatch_shortcode($atts = array()) {
        return Kolivri_Growth_Hub_Engine::renderFomoDispatchApp((array) $atts);
    }

    public function render_mcp_developer_hub_shortcode($atts = array()) {
        return Kolivri_Growth_Hub_Engine::renderMcpDeveloperHub((array) $atts);
    }

    public function render_ghost_minisite_shortcode($atts = array()) {
        return Kolivri_Growth_Hub_Engine::renderGhostMinisitePreview((array) $atts);
    }

    public function render_ghost_minisite_preview($atts = array()) {
        return Kolivri_Growth_Hub_Engine::renderGhostMinisitePreview((array) $atts);
    }

    public function render_competitor_alternative_shortcode($atts = array()) {
        return Kolivri_Growth_Hub_Engine::renderCompetitorAlternative((array) $atts);
    }

    public function render_competitor_alternative($atts = array()) {
        return Kolivri_Growth_Hub_Engine::renderCompetitorAlternative((array) $atts);
    }

    public function render_receiver_continuity_script() {
        $html = Kolivri_Growth_Hub_Engine::render_receiver_continuity_script();
        if (function_exists('doing_action') && doing_action('wp_footer')) {
            echo $html;
        }
        return $html;
    }

    public function filter_blog_post_content($content) {
        if (function_exists('is_singular') && !is_singular('post')) {
            return $content;
        }
        return Kolivri_Growth_Hub_Engine::renderBlogInlineCta((string) $content);
    }

    public function render_blog_inline_cta($content, $lang = null) {
        return Kolivri_Growth_Hub_Engine::renderBlogInlineCta((string) $content, $lang);
    }
}

Kolivri_Growth_Hub::get_instance();
