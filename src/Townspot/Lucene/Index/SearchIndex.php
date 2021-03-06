<?php
namespace Townspot\Lucene\Index;

use ZendSearch\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;
	
abstract class SearchIndex implements ServiceLocatorAwareInterface
{
	protected $_index_name;

    public function __construct(ServiceLocatorInterface $serviceLocator) 
	{
		$this->setServiceLocator($serviceLocator);
	}

	/**
	 * Set Index
	 *
	 * @return Object
	 */
	public function setIndexName($index) 
	{
		$this->_index = $index;
		return $this;
	}

	/**
	 * Get Index
	 *
	 * @return Object
	 */
	public function getIndexName() 
	{
		return $this->_index;
	}

	/**
	 * Get Lucene Index
	 *
	 * @param string $index
	 * @param boolean $create
	 * @return Object
	 */
	public function getIndex($create = false) 
	{
		$index_path = $this->_getIndexPath($this->getIndexName());
		if (realpath($index_path)) {
			$index = $this->open($this->getIndexName());
			\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
			return $index;
		} else {
			if ($create) {
				$index = $this->create($this->getIndexName());
				\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
				return $index;
			} else {
				throw new \Exception('Index ' . $this->getIndexName() . ' does not exist');
			}
		}
	}
	
	/**
	 * List Lucene Indexes
	 *
	 * @return array
	 */
	public function listIndexes() 
	{
		$config = $this->getServiceLocator()->get('config');
		$config = $config['lucene'];
		$_indexes = array();
		if ($handle = opendir($config['path'])) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$_indexes[] = $entry;
				}
			}
			closedir($handle);
		}		
		return $_indexes;
	}
	
	/**
	 * Optimize Indexes
	 *
	 * @param string $index
	 * @return array
	 */
	public function optimize() 
	{
		$this->getIndex($this->getIndexName())->optimize();
	}
	
	/**
	 * Clear Index
	 *
	 * @param string $index
	 * @param boolean $recreate
	 * @return array
	 */
	public function clear($recreate = true) 
	{
		$index_path = $this->_getIndexPath($this->getIndexName());
		if (@$handle = opendir($index_path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					unlink($index_path . DIRECTORY_SEPARATOR . $entry);
				}
			}
			closedir($handle);
		}		
		if ($recreate) {
			$this->create();
		}
	}

	public function delete() 
	{
		$index_path = $this->_getIndexPath($this->getIndexName());
		$this->clear(false);
		rmdir($index_path);
	}
	
	public function create() 
	{
		$index_path = $this->_getIndexPath($this->getIndexName());
		return Lucene\Lucene::create($index_path);
	}
	
	public function open() 
	{
		$index_path = $this->_getIndexPath($this->getIndexName());
		return Lucene\Lucene::open($index_path);
	}	
	
	/**
	 * Set Service Locator instance
	 *
	 * @param ServiceLocator $locator
	 * @return Object
	 */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
        return $this;
    }
	
	/**
	 * Gets the value of isDebug
	 *
	 * @return boolean
	 */
	public function getServiceLocator()
	{
		return $this->_serviceLocator;
	}
	
	protected function _getIndexPath($index)
	{
		$config = $this->getServiceLocator()->get('config');
		$config = $config['lucene'];
		return $config['path'] . DIRECTORY_SEPARATOR . $index;
	}
	
	/**
	 * Build Index
	 *
	 * @return Object
	 */
	abstract public function build();

	/**
	 * Add Item To Index
	 *
	 * @param Doctrine Entity $object
	 * @return Object
	 */
	abstract public function add($object);

	/**
	 * Remove Item From Index
	 *
	 * @param Doctrine Entity $object
	 * @return Object
	 */
	abstract public function remove($object);

	/**
	 * Update Item In Index
	 *
	 * @param Doctrine Entity $object
	 * @return Object
	 */
	abstract public function update($object);
}
