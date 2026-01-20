<?php

/**
 * JAIC_Request Class File
 *
 * This file contains the JAIC_Request class, which provides utility methods
 * for sending HTTP responses, including JSON responses and error responses.
 *
 * PHP version 7.0+
 *
 * @category Request
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

/**
 * Class JAIC_Request
 *
 * @category API
 * @package  JAIC\Classes
 * @author   Jotform <contact@jotform.com>
 * @license  Jotform <licence>
 * @link     https://www.jotform.com
 *
 * Handles HTTP response utilities for the application.
 */
class JAIC_Request {
    /**
     * Sends a 403 Forbidden error response with the provided message.
     *
     * @param string $message The error message to send in the response.
     *
     * @return void
     */
    public static function response403(string $message = ""): void {
        header("HTTP/1.0 403 Forbidden");
        die(esc_html($message));
    }

    /**
     * Sends a 400 Forbidden error response with the provided message.
     *
     * @param string $message The error message to send in the response.
     *
     * @return void
     */

    /**
     * Sends a 400 Bad Request response to the client.
     *
     * This function is used to notify the client of a malformed or invalid request.
     * It sets the appropriate HTTP status code
     * and outputs a JSON-formattederror message.
     *
     * @param string $message The error message to send in the response.
     *
     * @return void
     */
    public static function response400(string $message = ""): void {
        http_response_code(400);
        die(esc_html($message));
    }

    /**
     * Sends a 500 Internal Server Error response with a default error message.
     *
     * @return void
     */
    public static function response500(): void {
        header("HTTP/1.0 500 Internal Server Error");
        die(esc_html("An error occurred!"));
    }

    /**
     * Sends a JSON response with the provided HTTP status code and response data.
     *
     * @param int   $code     The HTTP status code for the response. Defaults to 200.
     * @param array $response An associative array containing the response data.
     *
     * @return void
     */
    public static function responseJSON(int $code = 200, array $response = []): void {
        // Set the HTTP response code and JSON content type header
        http_response_code(esc_html($code));
        header("Content-Type: application/json");

        // Map of status codes to status messages
        $status = [
            200 => "200 OK",
            400 => "400 Bad Request",
            404 => "404 Not Found",
            500 => "500 Internal Server Error",
        ];

        // Set the HTTP status header
        header("Status: " . (esc_html($status[$code]) ?? "500 Internal Server Error"));

        // Prepare the response payload
        $responsePayload = [
            "status" => esc_html($code),
            "data" => $response["data"] ?? "",
            "message" => esc_html($response["message"]) ?? "",
        ];

        // Send response payload as JSON
        die(wp_json_encode($responsePayload));
    }
}
