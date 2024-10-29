<?php
/**
 * Helper functions definition file
 *
 * @package eXtended WordPress
 * @subpackage Helper\Functions
 */

use XWP\Helper\Functions as f;

if ( ! function_exists( 'xwp_parse_args' ) ) :

    /**
     * Same as `wp_parse_args` but recursive.
     *
     * @param  string|array|object $args     Arguments to parse.
     * @param  array               $defaults Optional. Default values. Default empty array.
     * @return array<string,mixed>
     */
    function xwp_parse_args( string|array|object $args, array $defaults = array() ): array {
        match ( true ) {
            is_object( $args ) => $parsed = get_object_vars( $args ),
            is_array( $args )  => $parsed = &$args,
            default          => wp_parse_str( $args, $parsed ),
        };

        $result = $defaults;

        foreach ( $args as $k => $v ) {
            $result[ $k ] = is_array( $v ) && isset( $result[ $k ] ) && is_array( $result[ $k ] )
                ? xwp_parse_args( $v, $result[ $k ] )
                : $v;
        }

        return $result;
    }

endif;

if ( ! function_exists( 'xwp_wpfs' ) ) :
    /**
	 * Loads the WordPress filesystem
	 *
     * @template TFS of \WP_Filesystem_Base
     *
     * @param  class-string<TFS> $method  Optional. Filesystem method classname. Default null.
	 * @param  array|false       $args    Optional. Connection args, These are passed directly to the WP_Filesystem_*() classes. Default false.
     * @param  string|false      $context Optional. Context for get_filesystem_method(). Default false.
	 * @return TFS|false|null
	 */
	function xwp_wpfs(
        string $method = null,
        array|bool $args = false,
        string|bool $context = false,
	): WP_Filesystem_Base|bool|null {
        //phpcs:ignore Universal.Operators.DisallowShortTernary.Found
        $args = array_filter( $args ?: array( 'method' => $method ) );

		return f\WPFS::load( $args, $context );
	}
endif;

if ( ! function_exists( 'wp_load_filesystem' ) ) :
	/**
	 * Loads the WordPress filesystem
	 *
     * @template TFS of \WP_Filesystem_Base
     *
	 * @param  array{method?: class-string<TFS>}|array<string,mixed>|false $args    Optional. Connection args, These are passed directly to the WP_Filesystem_*() classes. Default false.
	 * @param  string|false                                                $context Optional. Context for get_filesystem_method(). Default false.
     *
	 * @return \WP_Filesystem_Base|false|null
     *
     * @deprecated 1.10.0 Use xwp_wpfs instead.
	 */
	function wp_load_filesystem(
        array|bool $args = false,
        string|bool $context = false,
	): WP_Filesystem_Base|bool|null {
		return xwp_wpfs( null, $args, $context );
	}
endif;

if ( ! function_exists( 'xwp_deregister_blocks' ) ) :
    /**
     * Deregister all blocks.
     *
     * @return array<string> The names of the blocks that were deregistered.
     */
    function xwp_deregister_blocks(): array {
        return f\Block::deregister_all();
    }
endif;

if ( ! function_exists( 'xwp_remove_hook_callbacks' ) ) :
    /**
     * Remove callbacks for a given classname.
     *
     * @param class-string $classname   The name of the class to remove callbacks for.
     * @param string|false $target_hook Optional. Hook tag to remove callbacks for.
     * @param string|false $method      Optional. The name of the method to remove callbacks for.
     * @param int|false    $priority    Optional. The priority of the hook to remove callbacks for.
     *
     * @return array<string, array> The names of the callbacks that were removed.
     */
    function xwp_remove_hook_callbacks(
        string $classname,
        string|bool $target_hook = false,
        string|bool $method = false,
        int|bool $priority = false,
	): array {
        return f\Hook_Remover::remove_callbacks( $classname, $target_hook, $method, $priority );
    }
endif;

if ( ! function_exists( 'xwp_clean' ) ) :
    /**
     * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
     * Non-scalar values are ignored.
     *
     * @param  string|array $input Data to sanitize.
     * @return string|array
     */
    function xwp_clean( $input ) {
        return f\Request::clean( $input );
    }
endif;

if ( ! function_exists( 'xwp_uclean' ) ) :
    /**
     * Unslash then clean variables using sanitize_text_field. Arrays are cleaned recursively.
     * Non-scalar values are ignored.
     *
     * @param  string|array $input Data to sanitize.
     * @return string|array
     */
	function xwp_uclean( $input ) {
		return f\Request::uclean( $input );
	}
endif;

if ( ! function_exists( 'xwp_format_term_name' ) ) :
    /**
     * Format a term name with term parents.
     *
     * @param  WP_Term|int|string|null|array|\WP_Error $term WP_Term object, Term ID, Term slug, or Term name.
     * @param  array<string, mixed>                    $args Formatting arguments. Default empty array.
     *   - formatter (callable) Custom formatter for the displayed term name. Default null.
     *   - count (bool) Whether to include the term count in the formatted name. Default false.
     *   - link_format (string|callable|array|bool) URL Link format for the term link. Default false.
     *   - link_items (bool) Whether to link the term items. Default false.
     *   - link_final (bool) Whether to link the final term. Default true.
     *   - separator (string) Separator between terms. Default ' > '.
     *   - taxonomy (string) Taxonomy name. Default null. Mandatory if $term is a string. Optional otherwise.
     * @return string Formatted term name with ancestors.
     */
    function xwp_format_term_name( WP_Term|int|string|null|array|\WP_Error $term, array $args = array() ): string {
        return f\Term::format_hierarhical_name( $term, $args );
    }
endif;


if ( ! function_exists( 'xwp_str_to_bool' ) ) :
    /**
     * Convert a string to a boolean.
     *
     * @param  string|bool|null $str The string to convert.
     * @return bool
     */
    function xwp_str_to_bool( string|bool|null $str = '' ): bool {
        if ( is_bool( $str ) ) {
            return $str;
        }

        if ( xwp_is_int_str( $str ) ) {
            return intval( $str ) > 0;
        }

        return match ( strtolower( $str ) ) {
            'yes', 'true', 'on'  => true,
            'no', 'false', 'off' => false,
            default              => false,
        };
    }
endif;

if ( ! function_exists( 'xwp_bool_to_str' ) ) :
    /**
     * Convert a boolean to a string.
     *
     * @param  bool $boolean The boolean to convert.
     * @return 'yes'|'no'
     */
	function xwp_bool_to_str( bool|string $boolean ): string {
		return xwp_str_to_bool( $boolean ) ? 'yes' : 'no';
	}
endif;
