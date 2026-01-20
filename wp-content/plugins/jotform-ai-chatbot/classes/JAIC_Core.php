<?php

/**
 * JAIC_Core Class File
 *
 * This file contains the definition of the JAIC_Core class, which handles incoming
 * POST requests and performs corresponding actions. It validates and executes
 * methods dynamically based on the "action" parameter provided in the POST request.
 *
 * PHP version 7.0+
 *
 * @category Core
 * @package  JAIC\Classes
 * @author   Jotform <contact@jotform.com>
 * @license  Jotform <licence>
 * @link     https://www.jotform.com
 */

namespace JAIC\Classes;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit(0);
}

use JAIC\Classes\JAIC_Request;

/**
 * Class JAIC_Core
 *
 * @category Core
 * @package  JAIC\Classes
 * @author   Jotform <contact@jotform.com>
 * @license  Jotform <licence>
 * @link     https://www.jotform.com
 *
 * Handles Core requests and actions.
 */
class JAIC_Core {
    private static $pluginName = "Jotform AI Chatbot";
    private static $pluginNamespace = "jotform_ai_chatbot";
    private static $pluginOptionKey = "jotform_ai_chatbot_options";
    private static $pluginKnowledgeBaseOptionKey = "jotform_ai_chatbot_knowledgebase";
    private static $pluginPendingSyncKey = "jotform_ai_chatbot_pending_sync";
    private static $pluginSyncedPagesKey = "jotform_ai_chatbot_synced_pages";
    private static $pluginAgentUnavailableKey = "jotform_ai_chatbot_agent_unavailable";
    private static $pluginPagesSyncBlockedUntilKey = "jotform_ai_chatbot_pages_sync_blocked_until";
    private static $getPluginPreviewModeKey = "jotform_ai_chatbot_preview";
    private static $serviceURLs = [
        "geu" => [
            "site" => "https://eu.jotform.com",
            "api"  => "https://eu-api.jotform.com",
            "embed" => "https://cdn.jotfor.ms"
        ],
        "hipaa" => [
            "site" => "https://hipaa.jotform.com",
            "api"  => "https://hipaa-api.jotform.com",
            "embed" => "https://cdn.jotfor.ms"
        ],
        "us" => [
            "site" => "https://www.jotform.com",
            "api"  => "https://api.jotform.com",
            "embed" => "https://cdn.jotfor.ms"
        ]
    ];
    private static $siteURL;
    private static $siteAPIURL;
    private static $siteEmbedURL;
    private static $pluginSyncBlockedUntilSeconds = 7 * 24 * 60 * 60; // 7 days

    /**
     * JAIC_Core constructor.
     *
     * Checks the "action" parameter from the POST request and invokes
     * the corresponding method if it exists. If the method does not exist,
     * it returns a 400 error response.
     */
    public function __construct() {
        $this->setServiceURLs();

        // Validate request
        $nounce = isset($_POST["_nonce"]) ? sanitize_text_field(wp_unslash($_POST["_nonce"])) : false;
        if ($nounce && wp_verify_nonce($nounce, "jotform-ai-chatbot")) {
            // Get action data
            $action = isset($_POST["action"]) ? sanitize_text_field(wp_unslash($_POST["action"])) : null;
            // Include required file for handling requests
            require_once JAIC_PLUGIN_DIR . "/classes/JAIC_Request.php";

            if (!empty($action) && is_string($action)) {
                // Check if the method exists in the current class
                if (method_exists($this, $action)) {
                    /**
                    * Check user authorization
                    */
                    add_action("wp_loaded", function () {
                        if (!current_user_can("manage_options")) {
                            wp_die(esc_html(__("You do not have sufficient permissions to access this page.", "jotform-ai-chatbot")));
                        }
                    });

                    // Call the method dynamically
                    $this->{$action}();
                    return;
                }

                // Return a 400 error response for invalid methods
                JAIC_Request::response400("Error! Invalid Method.");
            }
        }
    }

    /**
     * Checks if the application is running in preview mode.
     *
     * @return bool True if preview mode is enabled; otherwise, false.
     */
    public function isPreviewMode(): bool {
        $nounce = isset($_GET["_nonce"]) ? sanitize_text_field(wp_unslash($_GET["_nonce"])) : false;
        return (!empty($nounce) && wp_verify_nonce($nounce, "jotform-ai-chatbot") && isset($_GET[self::$getPluginPreviewModeKey])) ? sanitize_text_field(wp_unslash($_GET[self::$getPluginPreviewModeKey])) : false;
    }

    /**
     * Outputs the JotForm AI chatbot embed code if conditions are met.
     *
     * The method checks for preview mode or if the current page is configured
     * for displaying the chatbot. If valid, it renders the embed code inside a div.
     *
     * @return void
     */
    public function renderChatbot(): void {
        // Get the current page ID
        $pageID = get_the_ID();

        // Get device type
        $device = $this->getDevice();

        // Retrieve chatbot options from the WordPress settings
        $options = get_option(self::$pluginOptionKey);
        $options = !empty($options) ? json_decode($options, true) : [];

        // Check and format pages value
        $pluginPageList = $this->getPluginPageList();

        $pluginDisabledForVisitedURL = $pluginEnabledForVisitedURL = false;
        $requestedURI = !empty($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : false;
        if (!empty($pluginPageList["showOn"]) && is_array($pluginPageList["showOn"])) {
            $pluginActivatedPageList = array_map(function ($pluginPageOption) {
                if (!empty($pluginPageOption["type"]) && ($pluginPageOption["type"] === "page")) {
                    return $pluginPageOption["value"];
                }
            }, $pluginPageList["showOn"]);

            $pluginActivatedURLList = array_map(function ($pluginPageOption) {
                if (
                    (!empty($pluginPageOption["type"]) && ($pluginPageOption["type"] === "url")) &&
                    (!empty($pluginPageOption["match"]) && ($pluginPageOption["match"] === "is"))
                ) {
                    $url = $pluginPageOption["value"];
                    if ($this->isAValidDomainURL($url) && strpos($url, "http") !== 0) {
                        $url = "https://" . $url;
                    }

                    $parsedURL = wp_parse_url($url);
                    if (!empty($parsedURL['path'])) {
                        return $parsedURL['path'];
                    }
                }
            }, $pluginPageList["showOn"]);

            foreach ($pluginPageList["showOn"] as $pluginPageOption) {
                if (
                    (!empty($pluginPageOption["type"]) && ($pluginPageOption["type"] === "url")) &&
                    (!empty($pluginPageOption["match"]) && ($pluginPageOption["match"] === "startsWith"))
                ) {
                    $url = $pluginPageOption["value"];
                    if ($this->isAValidDomainURL($url) && strpos($url, "http") !== 0) {
                        $url = "https://" . $url;
                    }

                    $parsedURL = wp_parse_url($url);
                    $path = trim($parsedURL['path'], "/");
                    if (!empty($parsedURL['path']) && strpos($requestedURI, ("/" . $path)) === 0) {
                        $pluginEnabledForVisitedURL = true;
                        break;
                    }
                }
            }
        }

        if (!empty($pluginPageList["hideOn"]) && is_array($pluginPageList["hideOn"])) {
            $pluginDisabledPageList = array_map(function ($pluginPageOption) {
                if (!empty($pluginPageOption["type"]) && ($pluginPageOption["type"] === "page")) {
                    return $pluginPageOption["value"];
                }
            }, $pluginPageList["hideOn"]);

            $pluginDisabledURLList = array_map(function ($pluginPageOption) {
                if (
                    (!empty($pluginPageOption["type"]) && ($pluginPageOption["type"] === "url")) &&
                    (!empty($pluginPageOption["match"]) && ($pluginPageOption["match"] === "is"))
                ) {
                    $url = $pluginPageOption["value"];
                    if ($this->isAValidDomainURL($url) && strpos($url, "http") !== 0) {
                        $url = "https://" . $url;
                    }

                    $parsedURL = wp_parse_url($url);
                    if (!empty($parsedURL['path'])) {
                        return $parsedURL['path'];
                    }
                }
            }, $pluginPageList["hideOn"]);

            foreach ($pluginPageList["hideOn"] as $pluginPageOption) {
                if (
                    (!empty($pluginPageOption["type"]) && ($pluginPageOption["type"] === "url")) &&
                    (!empty($pluginPageOption["match"]) && ($pluginPageOption["match"] === "startsWith"))
                ) {
                    $url = $pluginPageOption["value"];
                    if ($this->isAValidDomainURL($url) && strpos($url, "http") !== 0) {
                        $url = "https://" . $url;
                    }

                    $parsedURL = wp_parse_url($url);
                    $path = trim($parsedURL['path'], "/");
                    if (!empty($parsedURL['path']) && strpos($requestedURI, ("/" . $path)) === 0) {
                        $pluginDisabledForVisitedURL = true;
                        break;
                    }
                }
            }
        }

        if (
            $this->isPreviewMode() ||
            (
                isset($pluginPageList["showOn"]) &&
                empty($pluginPageList["showOn"]) &&
                isset($pluginPageList["active"]) &&
                ($pluginPageList["active"] === "showOn")
            ) ||
            (
                (
                    ($pluginPageList["active"] === "showOn") && (
                        $pluginEnabledForVisitedURL || (
                            !empty($pluginActivatedPageList) &&
                            (
                                (in_array($pageID, $pluginActivatedPageList) || in_array("all", $pluginActivatedPageList)) ||
                                is_array($pluginActivatedPageList) && (is_single() && in_array("all-posts", $pluginActivatedPageList)) ||
                                is_array($pluginActivatedPageList) && (is_category() && in_array("all-categories", $pluginActivatedPageList))
                            )
                        ) ||
                        (!empty($pluginActivatedURLList) && is_array($pluginActivatedURLList) && in_array($requestedURI, $pluginActivatedURLList))
                    )
                ) ||
                (
                    ($pluginPageList["active"] === "hideOn") && (
                        !$pluginDisabledForVisitedURL && (
                            empty($pluginDisabledPageList) ||
                            (
                                (!in_array($pageID, $pluginDisabledPageList) && !in_array("all", $pluginDisabledPageList)) ||
                                (is_single() && !in_array("all-posts", $pluginDisabledPageList)) ||
                                (is_category() && !in_array("all-categories", $pluginDisabledPageList))
                            )
                        ) &&
                        (empty($pluginDisabledURLList) || !is_array($pluginDisabledURLList) || !in_array($requestedURI, $pluginDisabledURLList))
                    )
                )
            )
        ) {
            // Render the chatbot if embed code and device type is available
            $chatbotEmbedCode = ($this->isPreviewMode() && !empty($options["preview"])) ? $options["preview"] : (!empty($options["embed"]) ? $options["embed"] : false);
            if (!empty($chatbotEmbedCode) && !is_404()) {
                if (
                    $this->isPreviewMode() ||
                    ($device === "all") ||
                    ($device === "mobile" && $this->isMobileDevice()) ||
                    ($device === "desktop" && !$this->isMobileDevice())
                ) {
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->getEmbedRendererCode($chatbotEmbedCode);
                }
            }
        }
    }

    /**
     * Deletes the Jotform AI Chatbot configuration and performs cleanup.
     *
     * @param bool $isWPHook Indicates whether the deletion is triggered by a WordPress hook.
     */
    public function delete(bool $isWPHook = false): void {
        $apiKey = $this->getAPIKey();

        try {
            if (!empty($apiKey)) {
                // Construct the API endpoint URL for deleting the platform agent
                $url = $this->getSiteAPIURL() . "/ai-chatbot/delete-platform-agent/wordpress/" . urlencode($this->getDomain());

                // Request params
                $args = [
                    "method"    => "DELETE",
                    "headers"   => [
                        "Content-Type"  => "application/json",
                        "APIKEY" => $apiKey
                    ],
                    "timeout"   => 10
                ];

                // Make the request
                wp_remote_request($url, $args);
            }
        } catch (\Exception $e) {
        }

        if ($isWPHook) { // If the deletion is triggered by a WordPress hook
            // Delete the all chatbot options from the database
            delete_option(self::$pluginOptionKey);
            delete_option(self::$pluginKnowledgeBaseOptionKey);
            delete_option(self::$pluginPendingSyncKey);
            delete_option(self::$pluginSyncedPagesKey);
            delete_option(self::$pluginAgentUnavailableKey);
            delete_option(self::$pluginPagesSyncBlockedUntilKey);

            if (!empty($apiKey)) {
                // Construct the API endpoint URL for deleting the api key
                $url = $this->getSiteURL() . "/API/user/apps/" . $apiKey . "/delete";

                // Request params
                $args = [
                    "method"    => "POST",
                    "headers"   => [
                        "Content-Type"  => "application/json",
                        "APIKEY" => $apiKey,
                        "Referer" => $this->getSiteURL()
                    ],
                    "timeout"   => 10
                ];

                // Make the request
                wp_remote_request($url, $args);
            }

            // Clear all WP caches
            $this->clearWPCaches();
        } else {
            // Delete partial chatbot options from the database
            update_option(self::$pluginOptionKey, wp_json_encode([
                "apiKey" => (!empty($apiKey) ? $apiKey : ""),
                "agentId" => ""
            ]));

            // Clear all WP caches
            $this->clearWPCaches();

            // Send a JSON response indicating successful deletion accordingly
            JAIC_Request::responseJSON(
                200,
                ["message" => self::$pluginName . " successfully deleted!"]
            );
        }
    }

    /**
     * Function to create knowledgebase of the site
     *
     * This function fetches the list of pages using the `getPages()` method, then iterates over each page to gather their URLs.
     * Only pages with a valid ID and a non-empty title are included in the knowledge base.
     * The URLs are sanitized using `esc_html()` to ensure they are safe for output.
     *
     * @param int $limit Indicates the max url count for knowledge base
     *
     * @return void
     */
    public function createKnowledgeBase(int $limit = 100) {
        $pages = $this->getPages();
        $categories = get_categories();

        $knowledgeBase = [];
        foreach ($pages as $page) {
            if (empty($page->ID) || empty($page->post_title)) {
                continue;
            }

            array_push($knowledgeBase, esc_html(get_permalink($page->ID)));
        }

        foreach ($categories as $category) {
            if (empty($category->term_id) || empty($category->name)) {
                continue;
            }

            array_push($knowledgeBase, esc_html(get_category_link($category->term_id)));
        }

        if (!empty($knowledgeBase)) {
            update_option(self::$pluginKnowledgeBaseOptionKey, wp_json_encode([
                "urls" => array_slice($knowledgeBase, 0, $limit)
            ]));
        }
    }

    /**
    * Retrieves the knowledge base from the plugin options.
    *
    * This function fetches the stored knowledge base data from the WordPress options table using the
    * plugin's unique key.
    *
    * @return array
    */
    private function getKnowledgeBase(): array {
        $knowledgeBase = get_option(self::$pluginKnowledgeBaseOptionKey);
        return (!is_string($knowledgeBase) || empty($knowledgeBase)) ? [] : json_decode($knowledgeBase, true);
    }

    /**
     * Update the Jotform AI Chatbot options.
     *
     * Update the related options from the database and sends a JSON response
     * indicating successful update.
     *
     * @param string $key The key of the AI Chatbot Plugin option
     * @param string $value The value of the AI Chatbot Plugin option
     * @param string $separator The key and value separator string
     *
     * @return void
     */
    private function update(string $key = "", string $value = "", string $separator = "|"): void {
        //Get Params
        $nounce = isset($_POST["_nonce"]) ? sanitize_text_field(wp_unslash($_POST["_nonce"])) : false;
        $optionKey = !empty($key) ? $key : ((wp_verify_nonce($nounce, "jotform-ai-chatbot") && isset($_POST["key"])) ? sanitize_text_field(wp_unslash($_POST["key"])) : "");
        $optionValue = !empty($value) ? $value : ((wp_verify_nonce($nounce, "jotform-ai-chatbot") && isset($_POST["value"])) ? sanitize_text_field(wp_unslash($_POST["value"])) : "");

        $optionKeys = strstr($optionKey, $separator) ? explode($separator, $optionKey) : [$optionKey];
        $optionValues = strstr($optionValue, $separator) ? explode($separator, $optionValue) : [$optionValue];

        foreach ($optionKeys as $key => $optionKey) {
            // Get Option Value
            $optionValue = $optionValues[$key];

            // Define valid option list
            $optionKeys = [
                "pages",
                "embed",
                "preview",
                "apiKey",
                "enterpriseDomain",
                "device",
                "unpublish",
                "logout",
                "agentId"
            ];

            if (empty($optionKey)) {
                JAIC_Request::response400("Error! Invalid parameters.");
            }
            $optionValue = empty($optionValue) ? "" : trim($optionValue);

            if (!is_string($optionKey) || !in_array($optionKey, $optionKeys)) {
                JAIC_Request::response403(
                    "Error! You are not authorized to update this option key."
                );
            }

            // Logout action
            if ($optionKey === "logout") {
                // Complete logout operation
                $this->delete(true);

                // Send a JSON response indicating successful logged-out
                JAIC_Request::responseJSON(
                    200,
                    ["message" => self::$pluginName . " successfully logged-out!"]
                );
            }

            // Temp option key and will be deleted!
            $optionKey = ($optionKey === "pagesV2") ? "pages" : $optionKey;

            // Get the chatbot options from the database
            $options = get_option(self::$pluginOptionKey);

            // Ensure it's an array
            $options = (!is_string($options) || empty($options)) ?
            [] : json_decode($options, true);

            if ($optionKey == "embed") {
                $options["preview"] = [];
            } elseif ($optionKey == "unpublish") {
                $options["embed"] = $options["preview"] = [];
            }

            // Add new option
            $options[$optionKey] = $optionValue;

            // Save updated options back to the database
            update_option(self::$pluginOptionKey, wp_json_encode($options));

            // Reset service urls according to user location
            if (in_array($optionKey, ["apiKey", "enterpriseDomain"])) {
                $this->setServiceURLs(true);
            }
        }

        // Clear all WP caches
        $this->clearWPCaches();

        // Send a success response in JSON format
        JAIC_Request::responseJSON(
            200,
            [
                "message" => self::$pluginName . " successfully updated!"
            ]
        );
    }

    /**
     * Creates the plugin settings and returns them in a JSON response.
     *
     * This function gathers the necessary settings for the plugin, including platform-specific details,
     * page information, and API credentials. It processes the available pages and determines which pages
     * are active based on the plugin configuration. The final settings are then packaged into an associative
     * array and returned in a JSON response with a success message.
     *
     * @return void
     */
    private function createSettings() {
        $pages = $this->getPages();
        $customPages = [
            ["text" => "All Category Pages", "value" => "all-categories"],
            ["text" => "All Blog Posts", "value" => "all-posts"]
        ];
        $pluginPageList = $this->getPluginPageList();

        $platformPages = [];

        foreach ($pages as $page) {
            if (empty($page->ID) || empty($page->post_title)) {
                continue;
            }

            array_push($platformPages, [
                "text" =>  esc_html($page->post_title),
                "value" => esc_html($page->ID)
            ]);
        }

        foreach ($customPages as $customPage) {
            array_push($platformPages, [
                "text" =>  esc_html($customPage["text"]),
                "value" => esc_html($customPage["value"])
            ]);
        }

        $options = get_option(self::$pluginOptionKey);
        $options = !empty($options) ? json_decode($options, true) : [];

        $isAgentPublished = !empty($options["embed"]);
        $enterpriseDomain = $this->getEnterpriseDomain();

        $settings = [
            "PLATFORM"                       => "wordpress",
            "PLATFORM_PAGES"                 => $platformPages,
            "PLATFORM_CHATBOT_PAGES"         => $pluginPageList,
            "PLATFORM_CHATBOT_PUBLISHED"     => $isAgentPublished,
            "PLATFORM_DEVICE"                => $this->getDevice(),
            "PLATFORM_KNOWLEDGE_BASE"        => $this->getKnowledgeBase(),
            "PLATFORM_URL"                   => $this->getPlatformURL(),
            "PLATFORM_API_URL"               => $this->getPlatformAPIURL(),
            "PLATFORM_DOMAIN"                => $this->getDomain(),
            "PLATFORM_PAGE_CONTENTS"         => $this->getPageContents(),
            "PLATFORM_PREVIEW_URL"           => $this->getPreviewURL(),
            "PLATFORM_PLUGIN_VERSION"        => $this->getPluginVersion(),
            "PLATFORM_WOOCOMMERCE_AVAILABLE" => $this->isPlatformValidToUseWooCommerce(),
            "PLATFORM_PERMALINK_STRUCTURE"   => $this->getPlatformPermalinkURLStructure(),
            "PROVIDER_API_KEY"               => $this->getAPIKey(),
            "PROVIDER_URL"                   => $this->getSiteURL(),
            "PROVIDER_API_URL"               => $this->getSiteAPIURL(),
            "PROVIDER_ENV"                   => !empty($enterpriseDomain) ? "ENTERPRISE" : "REGULAR"
        ];

        JAIC_Request::responseJSON(
            200,
            [
                "data" => $settings,
                "message" => self::$pluginName . " settings successfully created!"
            ]
        );
    }

    /**
     * Retrieves the plugin name
     *
     * @return string The Jotform AI Chatbot plugin name string value.
     */
    private function getPluginName(): string {
        return self::$pluginName;
    }

    /**
     * Retrieves the plugin namespace value
     *
     * @return string The Jotform AI Chatbot plugin namespace string value.
     */
    private function getPluginNamespace(): string {
        return self::$pluginNamespace;
    }

    /**
     * Retrieves the plugin DB option key name value
     *
     * @return string The Jotform AI Chatbot plugin DB option key string value.
     */
    private function getPluginOptionKey(): string {
        return self::$pluginOptionKey;
    }

    /**
     * Retrieves the domain of the current home URL.
     *
     * @return string The domain portion of the home URL.
     */
    private function getDomain(): string {
        return wp_parse_url(home_url(), PHP_URL_HOST);
    }

    /**
     * Returns the appropriate Jotform API URL.
     *
     * @return string The Jotform API URL.
     */
    private function getSiteAPIURL(): string {
        return self::$siteAPIURL;
    }

    /**
     * Returns the appropriate Jotform website URL.
     *
     * @return string The Jotform website URL.
     */
    private function getSiteURL(): string {
        return self::$siteURL;
    }

    /**
     * Function to set Service URLS
     *
     * @param bool $forceUserRegionCheck
     *
     * @return string The Jotform website URL.
     */
    private function setServiceURLs(bool $forceUserRegionCheck = false) {
        // Retrieve chatbot options from the WordPress settings
        $options = get_option(self::$pluginOptionKey);
        $options = !empty($options) ? json_decode($options, true) : [];

        self::$siteURL = self::$serviceURLs["us"]["site"];
        self::$siteAPIURL = self::$serviceURLs["us"]["api"];
        self::$siteEmbedURL = self::$serviceURLs["us"]["embed"];

        // Check user enterprise domain
        $enterpriseDomain = $this->getEnterpriseDomain();
        if (!empty($enterpriseDomain)) {
            self::$siteURL = "https://" . $options["enterpriseDomain"];
            self::$siteAPIURL = "https://" . $options["enterpriseDomain"] . "/API";
            self::$siteEmbedURL = "https://" . $options["enterpriseDomain"];
            return;
        }

        if (!$forceUserRegionCheck) {
            // Check user region settings
            $region = (isset($options["region"]) && in_array($options["region"], array_keys(self::$serviceURLs))) ? $options["region"] : false;
            if (!empty($region)) {
                if (in_array($region, ["geu", "hipaa"])) {
                    self::$siteURL = self::$serviceURLs[$region]["site"];
                    self::$siteAPIURL = self::$serviceURLs[$region]["api"];
                    self::$siteEmbedURL = self::$serviceURLs[$region]["embed"];
                }
                return;
            }
        }

        // Check user location
        $response = wp_remote_request($this->getSiteAPIURL() . "/user/", [
            "method"    => "GET",
            "headers"   => [
                "Content-Type"  => "application/json",
                "APIKEY" => $this->getAPIKey()
            ],
            "timeout"   => 10
        ]);

        if (!is_wp_error($response)) {
            $statusCode = wp_remote_retrieve_response_code($response);
            if ($statusCode == 200) {
                $response = wp_remote_retrieve_body($response);
                $response = json_decode($response, true);
                if (!empty($response["location"]) && strstr($response["location"], "https://eu-api.jotform.com")) {
                    self::$siteURL = self::$serviceURLs["geu"]["site"];
                    self::$siteAPIURL = self::$serviceURLs["geu"]["api"];
                    self::$siteEmbedURL = self::$serviceURLs["geu"]["embed"];

                    // Update user region settings
                    $options["region"] = "geu";
                    update_option(self::$pluginOptionKey, wp_json_encode($options));
                    return;
                } elseif (!empty($response["location"]) && strstr($response["location"], "https://hipaa-api.jotform.com")) {
                    self::$siteURL = self::$serviceURLs["hipaa"]["site"];
                    self::$siteAPIURL = self::$serviceURLs["hipaa"]["api"];
                    self::$siteEmbedURL = self::$serviceURLs["hipaa"]["embed"];

                    // Update user region settings
                    $options["region"] = "hipaa";
                    update_option(self::$pluginOptionKey, wp_json_encode($options));
                    return;
                }

                // Update user region settings
                $options["region"] = "us";
                update_option(self::$pluginOptionKey, wp_json_encode($options));
                return;
            }
        }

        $response = wp_remote_request((self::$siteAPIURL . '/user/location'), [
            'method'  => 'GET',
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) {
            return;
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        if ($statusCode !== 200) {
            return;
        }

        $location = wp_remote_retrieve_body($response);
        if (empty($location)) {
            return;
        }

        $location = json_decode($location, true);
        $excludedCountries = [];
        if (json_last_error() === JSON_ERROR_NONE && $location['responseCode'] === 200 && is_array($location['content']) && $location['content']['continent_code'] === 'EU' && !in_array($location['content']['country_code'], $excludedCountries)) {
            self::$siteURL = self::$serviceURLs["geu"]["site"];
            self::$siteAPIURL = self::$serviceURLs["geu"]["api"];

            // Update user region settings
            $options["region"] = "geu";
            update_option(self::$pluginOptionKey, wp_json_encode($options));
            return;
        }

        // Update user region settings
        $options["region"] = "us";
        update_option(self::$pluginOptionKey, wp_json_encode($options));
    }

    /**
     * Returns the appropriate Platform URL based on the current domain.
     *
     * @return string The Platform URL.
     */
    public function getPlatformURL(): string {
        return get_site_url();
    }

    /**
     * Returns the appropriate Platform Plugin API URL based on the current domain.
     *
     * @return string The Platform plugin API URL.
     */
    public function getPlatformAPIURL(): string {
        return get_site_url() . "/wp-admin/admin.php?page=" . self::$pluginNamespace;
    }

    /**
     * Returns the plugin preview mode url
     *
     * @return string The Jotform AI Chatbot Plugin preview mode url.
     */
    private function getPreviewURL(): string {
        return get_site_url() . "?" . self::$getPluginPreviewModeKey . "=1";
    }

    /**
     * Retrieves the API key for the chatbot from the WordPress settings.
     *
     * This function fetches the stored chatbot options from the WordPress
     * settings, decodes them into an array, and checks if an API key exists.
     * If a valid API key is found, it is returned; otherwise, an empty string
     * is returned.
     *
     * @return string The API key if available, or an empty string if not.
     */
    private function getAPIKey(): string {
        // Retrieve chatbot options from the WordPress settings
        $options = get_option(self::$pluginOptionKey);
        $options = !empty($options) ? json_decode($options, true) : [];

        // Return the API Key If already generated
        if (isset($options["apiKey"]) && !empty($options["apiKey"])) {
            return $options["apiKey"];
        }

        return "";
    }

    /**
     * Retrieves the agent ID for the chatbot from the WordPress settings.
     *
     * This function fetches the stored chatbot options from the WordPress
     * settings, decodes them into an array, and checks if an agent ID key exists.
     * If a valid agent ID key is found, it is returned; otherwise, an empty string
     * is returned.
     *
     * @return string The agent ID if available, or an empty string if not.
     */
    private function getAgentID(): string {
        // Retrieve chatbot options from the WordPress settings
        $options = get_option(self::$pluginOptionKey);
        $options = !empty($options) ? json_decode($options, true) : [];

        // Return the Agent ID If already generated
        if (isset($options["agentId"]) && !empty($options["agentId"])) {
            return $options["agentId"];
        }

        return "";
    }

    /**
     * Retrieves the Enterprise Domain key for the chatbot from the WordPress settings.
     *
     * This function fetches the stored chatbot options from the WordPress
     * settings, decodes them into an array, and checks if an Enterprise Domain key exists.
     * If a valid Enterprise Domain key is found, it is returned; otherwise, an empty string
     * is returned.
     *
     * @return string The Enterprise Domain key if available, or an empty string if not.
     */
    private function getEnterpriseDomain(): string {
        // Retrieve chatbot options from the WordPress settings
        $options = get_option(self::$pluginOptionKey);
        $options = !empty($options) ? json_decode($options, true) : [];

        // Return the Enterprise Domain If already generated
        if (!empty($options["enterpriseDomain"]) && $this->isAValidDomainURL($options["enterpriseDomain"])) {
            return $options["enterpriseDomain"];
        }

        return "";
    }

    /**
     * Retrieves the page list for the chatbot from the WordPress settings.
     *
     * This function fetches the stored chatbot options from the WordPress
     * settings, decodes them into an array, and checks if an pages key exists.
     * If a valid pages key is found, it is returned; otherwise, an empty array
     * is returned.
     *
     * @return array The page list if available, or an empty array if not.
     */
    private function getPluginPageList(): array {
        // Default page option data
        $pageList = ["showOn" => [], "hideOn" => [], "active" => "showOn"];

        // Retrieve chatbot options from the WordPress settings
        $options = get_option(self::$pluginOptionKey);
        $options = !empty($options) ? json_decode($options, true) : [];

        // Return the empty forced list
        if (isset($options["pages"]) && empty($options["pages"])) {
            return $pageList;
        }

        // Return the page list
        if (isset($options["pages"]) && !empty($options["pages"]) && is_string($options["pages"])) {
            return json_decode($options["pages"], true);
        }

        // Handle chatbot plugin v1 page data
        if (isset($options["pages"]) && !empty($options["pages"]) && is_array($options["pages"])) {
            if ($options["pages"] === ["all"]) {
                return $pageList;
            }

            foreach (["all-posts", "all-categories"] as $pageType) {
                if (in_array($pageType, $options["pages"])) {
                    array_push($pageList["showOn"], [
                        "id" => (string) crc32($pageType),
                        "type" => "page",
                        "match" => "is",
                        "value" => $pageType
                    ]);

                    return $pageList;
                }
            }

            if (
                (isset($options["pages"][0]) && is_string($options["pages"][0])) &&
                (filter_var($options["pages"][0], FILTER_VALIDATE_URL) !== false)
            ) {
                $path = wp_parse_url($options["pages"][0], PHP_URL_PATH);
                if (!empty($path) && is_string($path)) {
                    array_push($pageList["showOn"], [
                        "id" => (string) crc32(trim($path, "/")),
                        "type" => "url",
                        "match" => "startsWith",
                        "value" => trim($path, "/")
                    ]);

                    return $pageList;
                }
            }

            foreach ($options["pages"] as $page) {
                array_push($pageList["showOn"], [
                    "id" => (string) crc32($page),
                    "type" => "page",
                    "match" => "is",
                    "value" => (string) $page
                ]);
            }
        }

        return $pageList;
    }

    /**
     * Retrieves the content of all published pages in WordPress and formats it into a structured array.
     *
     * This function queries the WordPress database to fetch page titles and content for all published pages.
     *
     * @global $wpdb WordPress database abstraction object.
     * @return array An array where the keys are page IDs, and the values are arrays containing 'title' and 'content'.
     */
    private function getPageContents(): array {
        $knowledge_base = [];

        try {
            // Arguments to fetch published pages
            $args = [
                "post_type"      => "page",
                "post_status"    => "publish",
                "numberposts"    => 10
            ];

            $pages = get_posts($args);
            if (!empty($pages)) {
                foreach ($pages as $page) {
                    $knowledge_base["pages"][] = [
                        "title"   => esc_html($page->post_title),
                        "content" => esc_html($page->post_content)
                    ];
                }
            }
        } catch (\Exception $e) {
            $knowledge_base["pages"] = [];
        }

        return $knowledge_base;
    }

    /**
     * Retrieves and sorts all pages by their IDs.
     *
     * This function fetches all pages using the `getPages()` function
     * and sorts them in ascending order based on their ID.
     *
     * @return array An array of page objects sorted by ID.
     */
    private function getPages(): array {
        $pages = get_pages();
        if (empty($pages) || !is_array($pages)) {
            return [];
        }

        $pages = array_slice($pages, 0, 100);
        usort($pages, function ($a, $b) {
            return $a->ID <=> $b->ID;
        });

        return $pages;
    }

    /**
     * Retrieves the selected platform device
     *
     * This function fetches fetch device using the `getDevice()` function
     *
     * @return string An string of device type
     */
    private function getDevice(): string {
        $options = get_option(self::$pluginOptionKey);
        $options = !empty($options) ? json_decode($options, true) : [];

        // Check and format device value
        $device = !isset($options["device"]) ? "all" : ((!empty($options["device"]) && is_string($options["device"])) ? $options["device"] : "all");
        if (empty($device) || !is_string($device)) {
            return "all";
        }

        return $device;
    }

    /**
     * Detects whether the current user is on a mobile device (including tablets).
     *
     * This function checks the User-Agent string from the HTTP request
     * for keywords commonly associated with mobile or tablet devices.
     *
     * @return bool Returns true if the device is mobile or tablet, false if it's a desktop.
     */
    private function isMobileDevice(): bool {
        $userAgent = strtolower(sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ?? '')));

        if (function_exists('wp_is_mobile') && wp_is_mobile()) {
            return true;
        }

        $mobileKeywords = [
            'iphone', 'ipod', 'blackberry', 'opera mini', 'windows phone',
            'windows mobile', 'iemobile', 'mobile', 'nokia', 'webos', 'palm', 'symbian', 'htc',
            'ipad', 'tablet', 'kindle', 'silk', 'playbook', 'nexus 7', 'nexus 9', 'nexus 10',
            'galaxy tab', 'xoom', 'sch-i800', 'gt-p1000', 'touchpad', 'kfapwi', 'kfthwi', 'kfsawi'
        ];

        foreach ($mobileKeywords as $keyword) {
            if (strpos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        if (strpos($userAgent, 'android') !== false && strpos($userAgent, 'mobile') === false) {
            return true;
        }

        return false;
    }

    /**
     * Updates the knowledge base for the given page
     *
     * @param array $data The data to update the knowledge base with.
     *
     * @return bool True if the knowledge base was updated successfully, false otherwise.
     */
    private function updateKnowledgeBase($data) {
        $apiKey = $this->getAPIKey();
        if (empty($apiKey)) {
            return false;
        }

        $agentId = $this->getAgentID();
        if (empty($agentId)) {
            return false;
        }

        $url = $this->getSiteAPIURL() . "/ai-chatbot/update-url-material";

        // Payload
        $payload = [
            "platform"  => "wordpress",
            "domain"    => $this->getDomain(),
            "title"     => $data["title"],
            "url"       => $data["url"],
            "agent_id"  => $agentId
        ];

        // Request params
        $args = [
            "method"    => "POST",
            "body"      => wp_json_encode($payload),
            "headers"   => [
                "Content-Type" => "application/json",
                "APIKEY"       => $apiKey
            ]
        ];

        // Make the request
        $response = wp_remote_request($url, $args);
        if (!is_wp_error($response)) {
            $statusCode = wp_remote_retrieve_response_code($response);
            if ($statusCode == 200) {
                return true;
            } elseif ($statusCode == 404) {
                update_option(self::$pluginAgentUnavailableKey, wp_json_encode([
                    "agent_id" => $agentId,
                    "marked_at" => time()
                ]));
                return false;
            } elseif ($statusCode == 403) {
                update_option(self::$pluginPagesSyncBlockedUntilKey, time() + self::$pluginSyncBlockedUntilSeconds);
                return false;
            }
        }

        return false;
    }

    /**
     * Handles the post update event by adding the post to the pending sync queue.
     *
     * @param int $post_ID The ID of the post being updated.
     * @param WP_Post $post The post object being updated.
     * @param bool $update Whether this is an update or a new post.
     */
    public function handlePostUpdate($post_ID, $post, $update) {
        $pending_option = get_option(self::$pluginPendingSyncKey);
        $pending = (!is_string($pending_option) || empty($pending_option)) ? [] : json_decode($pending_option, true);

        // Build a content-based hash (title + content). This avoids metadata-only updates.
        $title = get_the_title($post_ID);
        $content = $post->post_content ?? '';
        $hash = md5($title . '||' . $content);

        // If it is already pending with the same hash, skip queueing
        if (isset($pending[$post_ID]) && isset($pending[$post_ID]['hash']) && $pending[$post_ID]['hash'] === $hash) {
            return;
        }

        // Queue / overwrite pending entry with hash and queued_at timestamp
        $pending[$post_ID] = [
            'title'       => $title,
            'url'         => get_permalink($post_ID),
            'hash'        => $hash,
            'queued_at'   => time()
        ];

        update_option(self::$pluginPendingSyncKey, wp_json_encode($pending));
    }

    /**
     * Handles the cron sync event by updating the knowledge base for all pending posts.
     *
     * @return bool True if the cron sync was successful, false otherwise.
     */
    public function handleCronSyncPages() {
        $agentId = $this->getAgentID();
        if (empty($agentId)) {
            return false;
        }

        $unavailableAgent = get_option(self::$pluginAgentUnavailableKey);
        $unavailableAgent = !empty($unavailableAgent) ? json_decode($unavailableAgent, true) : [];
        if (!empty($unavailableAgent) && is_array($unavailableAgent) && isset($unavailableAgent['agent_id'])) {
            // If agent id is unchanged, skip all sync attempts
            if ($unavailableAgent['agent_id'] === $agentId) {
                return false;
            }

            // If agent id changed, clear the block
            delete_option(self::$pluginAgentUnavailableKey);
        }

        // Check if the sync is blocked until
        $blockedUntil = get_option(self::$pluginPagesSyncBlockedUntilKey);
        if (!empty($blockedUntil) && time() < intval($blockedUntil)) {
            return false;
        }

        $pending_option = get_option(self::$pluginPendingSyncKey);
        $synced_option = get_option(self::$pluginSyncedPagesKey);
        $pending = (!is_string($pending_option) || empty($pending_option)) ? [] : json_decode($pending_option, true);
        $synced = (!is_string($synced_option) || empty($synced_option)) ? [] : json_decode($synced_option, true);

        if (empty($pending)) {
            return false;
        }

        foreach ($pending as $post_ID => $data) {
            // Recheck post status
            $post = get_post($post_ID);
            if (!$post || $post->post_status !== 'publish') {
                continue;
            }

            // If synced already and hash same => nothing to do
            if (isset($synced[$post_ID]) && isset($synced[$post_ID]['hash']) && $synced[$post_ID]['hash'] === ($data['hash'] ?? '')) {
                unset($pending[$post_ID]);
                continue;
            }

            try {
                $result = $this->updateKnowledgeBase($data);
                if ($result) {
                    $synced[$post_ID] = $data;
                    unset($pending[$post_ID]);
                }
            } catch (\Exception $e) {
            }
        }

        update_option(self::$pluginSyncedPagesKey, json_encode($synced));
        update_option(self::$pluginPendingSyncKey, json_encode($pending));

        return true;
    }

    /**
     * Handles to clear all WP caches
     */
    public function clearWPCaches() {
        try {
            if (function_exists('wp_cache_flush')) {
                wp_cache_flush();
            }

            if (function_exists('wp_cache_clear_cache')) {
                wp_cache_clear_cache();
            }

            if (function_exists('wpengine_flush_cache')) {
                wpengine_flush_cache();
            }

            if (class_exists('WP_Optimize')) {
                WP_Optimize::clear_cache();
            }

            if (function_exists('flying_press_clear_cache')) {
                flying_press_clear_cache();
            }

            if (class_exists('NitroPack\\SDK\\NitroPack')) {
                $sdk = new \NitroPack\SDK\NitroPack();
                $sdk->cache()->purge();
            }

            if (class_exists('W3_Plugin_TotalCacheAdmin') && method_exists('W3_Plugin_TotalCacheAdmin', 'flush_all')) {
                W3_Plugin_TotalCacheAdmin::flush_all();
            }

            if (class_exists('LiteSpeed_Cache_API')) {
                LiteSpeed_Cache_API::purge_all();
            }

            if (function_exists('sg_cachepress_purge_cache')) {
                sg_cachepress_purge_cache();
            }

            if (function_exists('rocket_clean_domain')) {
                rocket_clean_domain();
            }

            if (class_exists('autoptimizeCache')) {
                autoptimizeCache::clearall();
            }

            if (function_exists('wphb_clear_page_cache')) {
                wphb_clear_page_cache();
            }

            if (function_exists('breeze_clear_cache')) {
                breeze_clear_cache();
            }

            if (class_exists('FastVelocityMinifyCache')) {
                FastVelocityMinifyCache::clear();
            }

            if (function_exists('kinsta_flush_cache')) {
                kinsta_flush_cache();
            }

            if (class_exists('Docket_Cache')) {
                Docket_Cache::flush_all();
            }

            if (class_exists('SpeedyCache')) {
                SpeedyCache::clear_cache();
            }

            if (class_exists('Cloudflare\Plugin')) {
                \Cloudflare\Plugin::purge_cache();
            }

            if (class_exists('Swift_Performance')) {
                $swift = new Swift_Performance();
                $swift->cache->clear_all();
            }

            if (function_exists('comet_cache_flush')) {
                comet_cache_flush();
            }

            if (function_exists('hyper_cache_clear_cache')) {
                hyper_cache_clear_cache();
            }

            if (class_exists('WpFastestCache')) {
                WpFastestCache::deleteCache();
            }

            if (function_exists('super_page_cache_clear_cache')) {
                super_page_cache_clear_cache();
            }

            if (function_exists('flush_rewrite_rules')) {
                flush_rewrite_rules();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Checks if the given string is a valid domain-based URL without requiring a protocol.
     *
     * @param string $url The URL string to validate.
     * @return bool True if the string is a valid domain-based URL, false otherwise.
     */
    private function isAValidDomainURL(string $url): bool {
        return preg_match('/^([a-z0-9-]+\.)+[a-z]{2,}(:\d+)?(\/[^\s]*)?$/i', $url);
    }

    /**
     * Get active plugin version
     *
     * @return string Version of the plugin
     */
    private function getPluginVersion(): string {
        $pluginFile = JAIC_PLUGIN_DIR . '/jotform-ai-chatbot.php';
        $pluginData = get_file_data($pluginFile, ['Version' => 'Version']);
        return $pluginData['Version'] ?? '-';
    }

    /**
     * Get this website is valid to use WooCommerce
     *
     * @return boolean
     */
    private function isPlatformValidToUseWooCommerce(): bool {
        if (class_exists("WooCommerce")) {
            return true;
        }

        return false;
    }

    /**
     * Get this website permalink url structure
     *
     * @return string
     */
    private function getPlatformPermalinkURLStructure(): string {
        $permalinkStructure = get_option("permalink_structure");
        switch ($permalinkStructure) {
            case "":
                return "Plain";
            case "/%postname%/":
                return "PostName";
            case "/%year%/%monthnum%/%day%/%postname%/":
                return "DayAndName";
            case "/%year%/%monthnum%/%postname%/":
                return "MonthAndName";
            default:
                return "Custom";
        }

        return "Custom";
    }

    /**
     * Get chatbot embed code
     *
     * @return string Chatbot embed code
     */
    private function getEmbedRendererCode(string $embedCode = ""): string {
        $embedCode = strip_tags(rawurldecode(base64_decode($embedCode)), '<script>');
        // Old Version Embed Code
        if (strstr($embedCode, "AgentInitializer")) {
            if (preg_match('/formID:\s*"([^"]+)"/', $embedCode, $matches)) {
                if (!empty($matches[1])) {
                    $embedAssetURL = self::$siteEmbedURL . '/agent/embedjs/' . $matches[1] . '/embed.js';
                    if (preg_match('/queryParams:\s*\[([^\]]+)\]/', $embedCode, $matches)) {
                        $paramsArray = explode(',', $matches[1]);
                        $paramsArray = array_map(function ($item) {
                            return trim($item, " \"'");
                        }, $paramsArray);

                        $queryString = implode('&', $paramsArray);
                        if (!empty($queryString)) {
                            $embedAssetURL .= "?" . $queryString;
                        }
                    }

                    return $this->generateEmbedJSCode($embedAssetURL);
                }
            }

            return $embedCode;
        }

        // New Version Embed Code
        if (preg_match("/<script[^>]+src=['\"]([^'\"]+)['\"]/i", $embedCode, $matches)) {
            return $this->generateEmbedJSCode($matches[1]);
        }

        return '';
    }

    /**
     * Function to generate embed JS Code
     *
     * @param string $url The main chatbot embed asset url
     * @return string Embed JS Code to render chatbot on website
     */
    private function generateEmbedJSCode(string $url): string {
        $parts = wp_parse_url($url);
        $path = $parts['path'] ?? '';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        $url = self::$siteEmbedURL . $path . $query;
        return '
            <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function () {
                setTimeout(function () {
                    var s = document.createElement("script");
                    s.src = "' . esc_url($url) . '";
                    s.defer = true;
                    document.head.appendChild(s);
                }, 2000);
            });
            </script>
        ';
    }
}
