<?php
/**
 * Pure helpers for Order Progress email templates (mirrors order-progress-variant-logic.ts).
 *
 * @package YayMail
 */

namespace YayMail\Utils;

/**
 * Order Progress variant helpers.
 */
class OrderProgressVariantHelpers {

    /**
     * Step-marker column presets (widths sum 100). Must stay in sync with order-progress-variant-logic.ts (STEP_MARKER_PRESETS).
     *
     * @return array Presets keyed by step count 1–5; each has 'widths' and 'aligns' lists.
     */
    public static function get_step_marker_presets() {
        return [
            1 => [
                'widths' => [ 100 ],
                'aligns' => [ 'center' ],
            ],
            2 => [
                'widths' => [ 50, 50 ],
                'aligns' => [ 'left', 'right' ],
            ],
            3 => [
                'widths' => [ 33, 34, 33 ],
                'aligns' => [ 'left', 'center', 'right' ],
            ],
            4 => [
                'widths' => [ 12, 25, 25, 12 ],
                'aligns' => [ 'left', 'center', 'center', 'right' ],
            ],
            5 => [
                'widths' => [ 9, 20, 20, 20, 9 ],
                'aligns' => [ 'left', 'center', 'center', 'center', 'right' ],
            ],
        ];
    }

    /**
     * Connector segment colors for filled-bar inner table (left bar, icon, right bar).
     *
     * @param int    $index Step index (0-based).
     * @param int    $active_index Active step index (0-based).
     * @param int    $step_count Total steps.
     * @param string $active_color Connector active color.
     * @param string $inactive_color Connector inactive color.
     * @return array{left: string, right: string}
     */
    public static function get_connector_segment_colors( $index, $active_index, $step_count, $active_color, $inactive_color ) {
        $index        = (int) $index;
        $active_index = (int) $active_index;
        $step_count   = (int) $step_count;

        $left = ( 0 === $index )
            ? 'transparent'
            : ( $index <= $active_index ? $active_color : $inactive_color );

        $right = ( $index === $step_count - 1 )
            ? 'transparent'
            : ( $index < $active_index ? $active_color : $inactive_color );

        return [
            'left'  => $left,
            'right' => $right,
        ];
    }

    /**
     * Choose displayed step image URL (same rules as step-marker / filled-bar TS).
     *
     * @param bool   $is_step_active Whether step is active or completed.
     * @param string $active_url Active image URL.
     * @param string $inactive_url Inactive image URL.
     * @return string
     */
    public static function resolve_step_image_url( $is_step_active, $active_url, $inactive_url ) {
        $active_url   = (string) $active_url;
        $inactive_url = (string) $inactive_url;
        $chosen       = $is_step_active ? $active_url : $inactive_url;
        if ( '' !== $chosen ) {
            return $chosen;
        }
        return '' !== $active_url ? $active_url : $inactive_url;
    }

    /**
     * Filled-bar icon pill background: inactive steps always use #E2E6EE.
     *
     * @param bool   $is_step_active Whether step is active or completed.
     * @param string $raw_image_bg_color Per-step image background (may be empty).
     * @param string $connector_active_color Fallback when active and no per-step color.
     * @return string
     */
    public static function resolve_filled_bar_icon_background( $is_step_active, $raw_image_bg_color, $connector_active_color ) {
        $raw_image_bg_color = (string) $raw_image_bg_color;
        if ( ! $is_step_active ) {
            return '#E2E6EE';
        }
        return '' !== $raw_image_bg_color ? $raw_image_bg_color : $connector_active_color;
    }

    /**
     * Filled-bar label text color (legacy label_color overrides per-step / global).
     *
     * @param string $legacy_label_color Legacy single label color override.
     * @param bool   $is_step_active Whether label is for active step.
     * @param string $step_label_active_color Per-step active color.
     * @param string $step_label_inactive_color Per-step inactive color.
     * @param string $global_label_active_color Element default active label color.
     * @param string $global_label_inactive_color Element default inactive label color.
     * @return string
     */
    public static function resolve_filled_bar_label_color(
        $legacy_label_color,
        $is_step_active,
        $step_label_active_color,
        $step_label_inactive_color,
        $global_label_active_color,
        $global_label_inactive_color
    ) {
        $legacy_label_color = (string) $legacy_label_color;
        if ( '' !== $legacy_label_color ) {
            return $legacy_label_color;
        }
        $step_label_active_color   = (string) $step_label_active_color;
        $step_label_inactive_color = (string) $step_label_inactive_color;
        if ( $is_step_active ) {
            return '' !== $step_label_active_color ? $step_label_active_color : $global_label_active_color;
        }
        return '' !== $step_label_inactive_color ? $step_label_inactive_color : $global_label_inactive_color;
    }
}
