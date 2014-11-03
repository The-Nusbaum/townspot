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
     * @param string $text
     * @param int $color
     * @param int $bgColor
     */
    private function writeLine($text, $color = null, $bgColor = null)
    {
        $this->console->writeLine($text, $color, $bgColor);
    }
}