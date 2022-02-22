<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress;

use PiotrPress\Singleton;

\defined( 'ABSPATH' ) or exit;

if ( ! \class_exists( __NAMESPACE__ . '\Plugin' ) ) {
    abstract class Plugin {
        use Singleton;

        private array $data = [];

        protected function __construct( string $file ) {
            if ( ! \function_exists( 'get_plugins' ) )
                require_once( \ABSPATH . 'wp-admin/includes/plugin.php' );

            $this->data[ 'File' ] = $file;
            $this->data[ 'Dir' ] = \plugin_dir_path( $this->data[ 'File' ] );
            $this->data[ 'Url' ] = \plugin_dir_url( $this->data[ 'File' ] );
            $this->data[ 'BaseName' ] = \plugin_basename( $this->data[ 'File' ] );
            $this->data[ 'Slug' ] = false !== \strpos( $this->data[ 'BaseName' ], '/' )
                ? \dirname( $this->data[ 'BaseName' ] )
                : $this->data[ 'BaseName' ];

            $plugins = \get_plugins( \DIRECTORY_SEPARATOR . $this->data[ 'Slug' ] );
            $this->data += \reset( $plugins );

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