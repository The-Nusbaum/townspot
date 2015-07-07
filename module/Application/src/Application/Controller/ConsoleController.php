<?php
namespace Application\Controller;

use \Townspot\Lucene\VideoIndex;
use \Townspot\Lucene\ArtistIndex;
use \Townspot\Lucene\SeriesIndex;
use \Townspot\Lucene\LocationIndex;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConsoleController extends AbstractActionController
{
    /**
     * @var Console
     */
    protected $console;

    /**
     * Is quiet mode enabled?
     *
     * @var bool
     */
    protected $isQuiet;

    /**
     * @var DatabaseConfig
     */
    protected $config;

    protected $_amqp;

    /**
     * @param Console $console
     */
    public function __construct(Console $console)
    {
        $this->console = $console;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        if (!($request instanceof ConsoleRequest)) {
            throw new \RuntimeException('You can use this controller only from a console!');
        }
        return parent::dispatch($request, $response);
    }

    /**
     */
    public function buildindexesAction()
    {
        $this->writeLine('Building Artists', Color::GREEN);
		$artistIndex = new ArtistIndex($this->getServiceLocator());
		$artistIndex->build();
        $this->writeLine('Building Series', Color::GREEN);
		$seriesIndex = new SeriesIndex($this->getServiceLocator());
		$seriesIndex->build();
        $this->writeLine('Building Location', Color::GREEN);
		$locationIndex = new LocationIndex($this->getServiceLocator());
		$locationIndex->build();
        $this->writeLine('Building Videos', Color::GREEN);
		$videoIndex = new VideoIndex($this->getServiceLocator());
		$videoIndex->build();
        $this->writeLine('Build completed', Color::GREEN);
    }

    /**
     */
    public function builddeltaAction()
    {
        $this->writeLine('Building Artists - delta', Color::GREEN);
		$artistIndex = new ArtistIndex($this->getServiceLocator());
		$artistIndex->delta();
        $this->writeLine('Building Series - delta', Color::GREEN);
		$seriesIndex = new SeriesIndex($this->getServiceLocator());
		$seriesIndex->delta();
        $this->writeLine('Building Location - delta', Color::GREEN);
		$locationIndex = new LocationIndex($this->getServiceLocator());
		$locationIndex->delta();
        $this->writeLine('Building Videos - delta', Color::GREEN);
		$videoIndex = new VideoIndex($this->getServiceLocator());
		$videoIndex->delta();
        $this->writeLine('Build completed', Color::GREEN);
    }

    public function optimizeindexesAction()
    {
        $this->writeLine('Optimizing Artists', Color::GREEN);
		$artistIndex = new ArtistIndex($this->getServiceLocator());
		$artistIndex->optimize();
        $this->writeLine('Optimizing Series', Color::GREEN);
		$seriesIndex = new SeriesIndex($this->getServiceLocator());
		$seriesIndex->optimize();
        $this->writeLine('Optimizing Location', Color::GREEN);
		$locationIndex = new LocationIndex($this->getServiceLocator());
		$locationIndex->optimize();
        $this->writeLine('Optimizing Videos', Color::GREEN);
		$videoIndex = new VideoIndex($this->getServiceLocator());
		$videoIndex->optimize();
        $this->writeLine('Optimizecompleted', Color::GREEN);
	}

    /**
     */
    public function clearcacheAction()
    {
		$cachePath = APPLICATION_PATH . '/data/cache';
		if ($handle = opendir($cachePath)) {
			while (false !== ($file = readdir($handle))) {
				if (!preg_match('/^\./',$file)) {
					$file = $cachePath . '/' . $file;
					if (is_dir($file)) {
						$this->deleteDirectoryContents($file);
					}
				}
			}
			closedir($handle);
		}
    }

    /**
     * @param string $text
     * @param int $color
     * @param int $bgColor
     */
    private function writeLine($text, $color = null, $bgColor = null)
    {
        $this->console->writeLine($text, $color, $bgColor);
    }
	
	protected function deleteDirectoryContents($path) {
		$path = str_replace('\\','/',$path);
		$files = glob($path . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				$this->deleteDirectoryContents($file);
				//for the LOVE OF GOD SSHTAHP DELETING THE DIRECTORIES
                //rmdir($file);
			} else {
				unlink($file);
			}
		}
	}

    public function getAmqp() {
        if(empty($this->_amqp)) {
            $amqp = $this->getServiceLocator()->get('Config')['amqp'];

            $queue  = 'cache.ping';
            $exchange =  'cache';

            $conn = new AMQPStreamConnection(
                $amqp['host'],
                $amqp['port'],
                $amqp['user'],
                $amqp['pass'],
                $amqp['vhost']
            );
            $ch = $conn->channel();
            $ch->queue_declare($queue, false, true, false, false);
            $ch->exchange_declare($exchange, 'direct', false, true, false);
            $ch->queue_bind($queue, $exchange);

            $this->_amqp = $ch;
        }
        return $this->_amqp;
    }

    public function _getTime($start) {
        $time = time() - $start;
        $mins = floor($time/60);
        $secs = $time - $mins * 60;

        return compact('mins','secs');
    }

    public function buildCacheAction() {
        ini_set('memory_limit','2000M');
        set_time_limit(10000);
        $config = $this->getServiceLocator()->get('Config');
        $server = $config['amqp']['host'];
        $start = time();

        $delay = $this->getRequest()->getParam('delay');
        if(!$delay) $delay = 250000;

        $pages = array_keys($config['buildCache']);
        fputs(STDOUT,sprintf("processing pages\n"));
        array_unshift($pages,'');
	foreach($pages as $page) {
            $this->_output("/".$page,$server,$delay);
        }
        $time = $this->_getTime($start);
        fputs(STDOUT,sprintf("\ntook %s minutes and %s seconds to process %s records\n",$time['mins'],$time['secs'],count($pages)));

        $assets = array_keys($config['asset_manager']['resolver_configs']['collections']);
        fputs(STDOUT,sprintf("processing asset manager\n"));
        foreach($assets as $asset) {
            $this->_output("/".$asset,$server,$delay);
        }
        $time = $this->_getTime($start);
        fputs(STDOUT,sprintf("\ntook %s minutes and %s seconds to process %s records\n",$time['mins'],$time['secs'],count($assets)));
        
	fputs(STDOUT,sprintf("processing videos and discover\n"));
        $start = time();
        $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
        $media = $mediaMapper->findBy(array(
            '_approved' => 1,
        ));

        fputs(STDOUT,sprintf("%s records\n",count($media)));
        $urls = array();
        foreach($media as $m) {
            $this->_output($m->getMediaLink(),$server,$delay);
            $urls = array_merge($urls,$this->_makeUrls($m));
            $urls = array_unique($urls);
        }

        foreach($urls as $u){
            $this->_output($u,$server, $delay);
        }

        $time = $this->_getTime($start);
        fputs(STDOUT,sprintf("\ntook %s minutes and %s seconds to process %s records for %s urls\n",$time['mins'],$time['secs'],count($media), count($urls)));
        fputs(STDOUT,"Processing users\n");
        $start = time();
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $users = $userMapper->findAll();

        fputs(STDOUT,sprintf("%s records\n",count($users)));
        foreach($users as $u) {
            $this->_output($u->getProfileLink(),$server,$delay);
        }

        $time = $this->_getTime($start);
        fputs(STDOUT,sprintf("\ntook %s minutes and %s seconds to process %s records\n",$time['mins'],$time['secs'],count($users)));
    }

    protected function _processParents(\Townspot\Category\Entity $cat,$base = '') {
        $url = '';
        if($cat->getParent() instanceof \Townspot\Category\Entity) {
            $url = $this->_processParents($cat->getParent());
        }
        $url .= '/'.$cat->getName();

        $url = sprintf('%s%s',$base,$url);
        return $url;
    }

    protected function _makeUrls(\Townspot\Media\Entity $media,$base = '/discover') {
        $urls = array();
        $stateBase = sprintf('%s/%s',$base,$media->getProvince()->getName());
        $urls[] = $stateBase;
        $cityBase = sprintf("%s/%s/%s",$base,$media->getProvince()->getName(),$media->getCity()->getName());
        $urls[] = $cityBase;
        foreach($media->getCategories() as $cat) {
            $urls[] = $this->_processParents($cat,$base);
            $urls[] = $this->_processParents($cat,$stateBase);
            $urls[] = $this->_processParents($cat,$cityBase);
        }
        return $urls;
    }

    protected function _output($url,$server = false,$delay = 2000000) {
        if($server) {
	    $url = str_replace(' ','%20',$url);
            fputs(STDOUT,sprintf("."));
            //$url = "$url?sort=created:desc";
            $msg_body = json_encode(compact('url'));
            $msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
            usleep($delay);
            $this->getAmqp()->basic_publish($msg, 'cache');
        } else {
            fputs(STDOUT,"$url\n");
        }
    }

    public function refreshYoutubeAction() {
        $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
        $ytMedia = $mediaMapper->findBy(array('_source' => 'youtube'));

        var_dump(count($ytMedia));
    }
}
