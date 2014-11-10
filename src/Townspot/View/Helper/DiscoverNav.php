<?php
namespace Townspot\View\Helper;

use Zend\View\Helper\AbstractHelper;  
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;  

class DiscoverNav extends AbstractHelper implements ServiceLocatorAwareInterface  
{  
    public function __invoke()
    {
		$html = '';
		$helperPluginManager	= $this->getServiceLocator();
		$serviceManager 		= $helperPluginManager->getServiceLocator();  	
		$provinceMapper 		= new \Townspot\Province\Mapper($serviceManager);
		$cityMapper 			= new \Townspot\City\Mapper($serviceManager);
		$provinces 				= $provinceMapper->getProvincesHavingMedia();
		$cities					= array();
		$provinceId				= (isset($_SESSION['Discover_Province'])) ? $_SESSION['Discover_Province'] : null;
		$cityId					= (isset($_SESSION['Discover_City'])) ? $_SESSION['Discover_City'] : null;
		$categoriesSelected		= (isset($_SESSION['Discover_Categories'])) ? $_SESSION['Discover_Categories'] : null;
		$subCategories			= (isset($_SESSION['Discover_Subcategories'])) ? $_SESSION['Discover_Subcategories'] : null;
		$provinceSelected 		= null;
		$citySelected 			= null;
		$media_count		 	= 0;
		$city_media_count	 	= 0;
		foreach ($provinces as $province) {
			$media_count += $province['media_count'];
			if ($province['id'] == $provinceId) {
				$provinceSelected = $province['name'];
			}
		}
		if ($provinceId) {
			$cities			= $cityMapper->getCitiesHavingMedia($provinceId);
			foreach ($cities as $city) {
				$city_media_count += $city['media_count'];
				if ($city['id'] == $cityId) {
					$citySelected = $city['name'];
				}
			}
		}
		$html .= '<nav class="navbar navbar-townspot" id="discover-nav" role="navigation">';
		$html .= '    <div class="container-fluid">';
		$html .= '        <div id="townspot-nav">';
		$html .= '			<ul class="nav navbar-nav navbar-left">';
		$html .= '				<li class="dropdown">';
		$html .= '					<a class="dropdown-toggle" data-toggle="dropdown" href="#">';
		if ($provinceSelected) { 
			$html .= '                  	' . $provinceSelected . ' <i class="fa fa-angle-down"></i>';
		} else { 
			$html .= '                  	States <i class="fa fa-angle-down"></i>';
		}
		$html .= '					</a>';
		$html .= '					<ul class="dropdown-menu">';
		$html .= '						<li data-id="0" class="state-selector">';
		$html .= '						    <span class="state-name">All</span>'; 
		$html .= '						    <span class="badge pull-right">' . $media_count . '</span>';
		$html .= '						</li>';
		foreach ($provinces as $province) {
			$html .= '						<li data-id="' . htmlentities(strtolower($province['name'])) . '" class="state-selector">';
			$html .= '							<span class="state-name">' . $province['name'] . '</span>';
			$html .= '							<span class="badge pull-right">' . $province['media_count'] . '</span></li>';
		}
		$html .= '					</ul>';
		$html .= '				</li>';
		if ($cities) {
			$html .= '				<li class="dropdown" id="cities-list">';
			$html .= '					<a class="dropdown-toggle" data-toggle="dropdown" href="#">';
			if ($citySelected) { 
				$html .= '                  	' . $citySelected . ' <i class="fa fa-angle-down"></i>';
			} else { 
				$html .= '                  	Cities <i class="fa fa-angle-down"></i>';
			}
			$html .= '					</a>';
			$html .= '					<ul class="dropdown-menu">';
			$html .= '						<li data-id="0" class="city-selector">';
			$html .= '						    <span class="city-name">All</span>'; 
			$html .= '						    <span class="badge pull-right">' . $city_media_count . '</span>';
			$html .= '						</li>';
			foreach ($cities as $city) {
				$html .= '						<li data-id="' . htmlentities(strtolower($city['name'])) . '" class="city-selector">';
				$html .= '							<span class="city-name">' . $city['name'] . '</span>';
				$html .= '							<span class="badge pull-right">' . $city['media_count'] . '</span></li>';
			}
			$html .= '					</ul>';
			$html .= '				</li>';
		}
		$html .= '				</ul>';
		$html .= '        </div>';
		$html .= '    </div>';
		$html .= '</nav>';
		if (($provinceSelected)||
            ($citySelected) ||
		    ($categoriesSelected)) {
		$html .= '<nav id="discover-status-display" class="navbar navbar-townspot tablet-wide" role="navigation">';
		$html .= '    <div class="container-fluid">';
		$html .= '        <div class="wrapper">';
		$html .= '			<table>';
		$html .= '				<tr>';
		if ($provinceSelected) {
			$html .= '					<td class="nowrap"><h3>State:</h3></td>';
		}
		if ($citySelected) {
			$html .= '					<td class="nowrap"><h3>City:</h3></td>';
		}
		if ($categoriesSelected) {
			$html .= '					<td class="nowrap"><h3>Category:</h3></td>';
		}
		$html .= '				</tr>';
		$html .= '				<tr>';
		if ($provinceSelected) {
			$html .= '					<td class="nowrap">' . $provinceSelected . '&nbsp;<img class="remove-state" data-id="' . $provinceId . '" src="/img/delete.png">';
			$html .= '					</td>';
		}
		if ($citySelected) {
			$html .= '					<td class="nowrap">' . $citySelected . '&nbsp;<img class="remove-city" data-id="' . $cityId . '" src="/img/delete.png">';
			$html .= '					</td>';
		}
		if ($categoriesSelected) {
			$html .= '					<td class="nowrap">';
			foreach ($categoriesSelected as $index => $category) {
				$displayName = $category['name'];
				if ($displayName == 'all videos') {
					$displayName = 'All';
				}
				$html .= '					    <span class="selected-category" data-name="' . strtolower(htmlentities($category['name'])) . '">' . $displayName . '&nbsp;<img class="remove-category-' .  $index . '" src="/img/delete.png"></span>';
			}
			$html .= '					</td>';
		}
		if ($subCategories) {
			$html .= '					<td>';
			$html .= '						<table class="sub-categories">';
			$html .= '							<tr>';
			foreach ($subCategories as $category) {
				$html .= '								<td class="update-subcategory" data-id="' . $category['id'] . '" data-name="' . strtolower(htmlentities($category['name'])) . '">' . $category['name'] . '</td>';
			}
			$html .= '							</tr>';
			$html .= '						</table>';
			$html .= '					</td>';
		}
		$html .= '				</tr>';
		$html .= '			</table>';
		$html .= '        </div>';
		$html .= '    </div>';
		$html .= '</nav>';
		$html .= '<nav id="discover-status-display" class="navbar navbar-townspot tablet-small" role="navigation">';
		$html .= '    <div class="container-fluid">';
		$html .= '        <div class="wrapper">';
		$html .= '			<table>';
		if ($provinceSelected) {
			$html .= '				<tr>';
			$html .= '					<td class="selected-category-heading">State:</td>';
			$html .= '					<td class="nowrap">' . $provinceSelected . '&nbsp;<img class="remove-state" data-id="' . $provinceId . '" src="/img/delete.png">';
			$html .= '				</tr>';
		}
		if ($citySelected) {
			$html .= '				<tr>';
			$html .= '					<td class="selected-category-heading">City:</td>';
			$html .= '					<td class="nowrap">' . $citySelected . '&nbsp;<img class="remove-city" data-id="' . $cityId . '" src="/img/delete.png">';
			$html .= '				</tr>';
		}
		if ($categoriesSelected) {
			$html .= '				<tr>';
			$html .= '					<td class="selected-category-heading">Category:</td>';
			$html .= '					<td>';
			$html .= '						<ul class="category-list">';
			foreach ($categoriesSelected as $index => $category) {
			$html .= '							<li class="selected-category">' . $category['name'] . '&nbsp;<img class="remove-category-' . $index . '" src="/img/delete.png"></li>';
			}
			$html .= '						</ul>';
			$html .= '					</td>';
			$html .= '				</tr>';
		}
		if ($subCategories) {
			$html .= '				<tr>';
			$html .= '					<td colspan="2" class="dropdown" id="subcategories-list">';
			$html .= '						<a class="dropdown-toggle" data-toggle="dropdown" href="#">';
			$html .= '							SubCategories <i class="fa fa-angle-down"></i>';
			$html .= '						</a>';
			$html .= '						<ul class="dropdown-menu">';
			foreach ($subCategories as $category) {
				$html .= '							<li class="update-subcategory" data-id="' . $category['id'] . '" data-name="' . strtolower(htmlentities($category['name'])) . '">' . $category['name'] . '</li>';
			}
			$html .= '						</ul>';
			$html .= '					</td>';
			$html .= '				</tr>';
		}
		
			$html .= '							</tr>';
			$html .= '						</table>';
			$html .= '					</td>';

		$html .= '			</table>';
		$html .= '        </div>';
		$html .= '    </div>';
		$html .= '</nav>';
		}
		return $html;
    }
	
	/** 
     * Set the service locator. 
     * 
     * @param ServiceLocatorInterface $serviceLocator 
     * @return CustomHelper 
     */  
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)  
    {  
        $this->serviceLocator = $serviceLocator;  
        return $this;  
    }  
    /** 
     * Get the service locator. 
     * 
     * @return \Zend\ServiceManager\ServiceLocatorInterface 
     */  
    public function getServiceLocator()  
    {  
        return $this->serviceLocator;  
    }  	
}