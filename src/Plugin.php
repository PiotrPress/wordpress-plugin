<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress;

use PiotrPress\Singleton;

\defined( 'ABSPATH' ) or exit;

if ( ! \class_exists( __NAMESPACE__ . '\Plugin' ) ) {
    abstract class Plugin {
        use Singleton;

        private array $data = [];

        protected function __construct() {
            if ( ! \function_exists( 'get_plugins' ) )
                require_once( \ABSPATH . 'wp-admin/includes/plugin.php' );

            $traces = \debug_backtrace( \DEBUG_BACKTRACE_IGNORE_ARGS, 1 );
            $file = \reset( $traces )[ 'file' ];
            $dir = \dirname( $file );

            $offset = \strlen( \WP_PLUGIN_DIR . \DIRECTORY_SEPARATOR );
            $length = \strpos( $dir, \DIRECTORY_SEPARATOR, $offset ) + 1;

            $this->data[ 'Dir' ] = \substr( $dir, 0, $length );
            $this->data[ 'Slug' ] = \substr( $this->data[ 'Dir' ], $offset, - 1 );

            $plugins = \get_plugins( \DIRECTORY_SEPARATOR . $this->data[ 'Slug' ] );
            $this->data += \reset( $plugins );

            $this->data[ 'Basename' ] = $this->data[ 'Slug' ] . '/' . \key( $plugins );
            $this->data[ 'File' ] = $this->data[ 'Dir' ] . \key( $plugins );
            $this->data[ 'Url' ] = \WP_PLUGIN_URL . '/' . $this->data[ 'Slug' ] . '/' ;

            $domainpath = $this->data[ 'Slug' ] . $this->data[ 'DomainPath' ];
            if ( $this->data[ 'TextDomain' ] and ! \is_textdomain_loaded( $this->data[ 'TextDomain' ] ) )
                \load_plugin_textdomain( $this->data[ 'TextDomain' ], false, $domainpath );

            \register_activation_hook( $this->data[ 'File' ], [ $this, 'activation' ] );
            \register_deactivation_hook( $this->data[ 'File' ], [ $this, 'deactivation' ] );
        }

        public static function __callStatic( string $name, array $args = [] ) {
            if ( 0 !== \strpos( $name, $prefix = 'get' ) ) return false;
            $name = \substr( $name, \strlen( $prefix ) );

            return static::get( $name );
        }

        protected static function get( $name ) {
            return (static::class)::getInstance()->data[ $name ] ?? false;
        }

        abstract public function activation() : void;
        abstract public function deactivation() : void;
    }
}