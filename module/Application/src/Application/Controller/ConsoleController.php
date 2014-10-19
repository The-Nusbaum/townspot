<?php
namespace Application\Controller;

use \Townspot\Lucene\VideoIndex;
use \Townspot\Lucene\ArtistIndex;
use \Townspot\Lucene\SeriesIndex;
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
        $this->writeLine('Building Videos', Color::GREEN);
		$videoIndex = new VideoIndex($this->getServiceLocator());
		$videoIndex->build();
        $this->writeLine('Build completed', Color::GREEN);
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