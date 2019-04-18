<?php

    namespace BoltPlugin\Twig;

    use Bolt\IPlugin;
    use Bolt\Component;
    use Twig\Loader\FilesystemLoader;
    use Twig\Environment;

    class Renderer extends Component implements IPlugin {

        function activate() {
            $this->on( 'getRenderer', [ $this, 'getRenderer' ] );
        }
        
        function getRenderer() {
			return function() {

                $viewsFolder = $this->getConfig( 'defaults/viewPath', __DIR__ . '/../../' . 'application/view/' );
                $loader = new FilesystemLoader([
                    $viewsFolder . $this->getName() . '/',
                    $viewsFolder
                ]);
                $loader = $this->trigger( 'twigLoaderInitialized', $loader );
                $caching = $this->getConfig( 'twig/cache', sys_get_temp_dir() );
                $debug = $this->getConfig( 'twig/debug', false );
                $twig = new Environment( $loader, [
                    'debug' => $debug,
                    'cache' => $caching
                ]);
                $debug && $twig->addExtension( new \Twig\Extension\DebugExtension() );
                $twig->addGlobal( 'view', $this );
                $params = ( array )$this->getViewBag();
                $params[ 'model' ] = $this->model;
                $twig = $this->trigger( 'twigEnvironmentInitialized', $twig );
                return $twig->render( str_replace( $this->getConfig( 'defaults/viewPath', 'application/view/' ), '', $this->template ), $params );
            };
		}
    }
