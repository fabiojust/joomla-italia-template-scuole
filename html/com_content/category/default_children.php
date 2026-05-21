<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Uri\Uri;

$lang   = Factory::getLanguage();
$user   = Factory::getUser();
$groups = $user->getAuthorisedViewLevels();
$baseImagePath = Uri::root(true) . "/media/templates/site/bootstrap-italia-scuole/images/";

?>

<?php if (count($this->children[$this->category->id]) > 0) : ?>
    <div class="row g-4">
        <?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
            <?php // Check whether category access level allows access to subcategories.?>
            <?php if (in_array($child->access, $groups)) : ?>
                <?php if ($this->params->get('show_empty_categories') || $child->getNumItems(true) || count($child->getChildren())) : ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-wrapper h-100">
                        <div class="card card-bg shadow-sm h-100 p-4 border-top border-primary border-4 rounded">
                            <div class="card-body p-0 d-flex flex-column">
                                <h3 class="card-title h5 font-weight-bold mb-3">
                                    <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>" class="text-decoration-none text-dark hover:text-primary">
                                        <?php echo $this->escape($child->title); ?>
                                    </a>
                                    <?php if ($this->params->get('show_cat_num_articles', 1)) : ?>
                                        <span class="badge bg-primary rounded-pill small ms-1" title="<?php echo HTMLHelper::_('tooltipText', 'COM_CONTENT_NUM_ITEMS'); ?>">
                                            <?php echo $child->getNumItems(true); ?>
                                        </span>
                                    <?php endif; ?>
                                </h3>
                                
                                <?php if ($this->params->get('show_subcat_desc') == 1 && $child->description) : ?>
                                    <p class="card-text text-secondary small mb-4 flex-grow-1">
                                        <?php echo HTMLHelper::_('string.truncate', strip_tags($child->description), 120); ?>
                                    </p>
                                <?php else: ?>
                                    <div class="flex-grow-1"></div>
                                <?php endif; ?>
                                
                                <div class="mt-auto pt-2">
                                    <a class="read-more d-inline-flex align-items-center text-primary font-weight-bold text-decoration-none small" href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>">
                                        <span>Vedi categoria</span>
                                        <svg class="icon icon-xs ms-1 text-primary"><use href="<?php echo $baseImagePath; ?>sprites.svg#it-arrow-right"></use></svg>
                                    </a>
                                </div>

                                <?php if (count($child->getChildren()) > 0 && $this->maxLevel > 1) : ?>
                                    <div class="mt-4 pt-3 border-top">
                                        <p class="small text-muted mb-2 text-uppercase font-weight-bold">Sottocategorie</p>
                                        <ul class="list-unstyled mb-0">
                                            <?php foreach ($child->getChildren() as $subchild) : ?>
                                                <?php if (in_array($subchild->access, $groups)) : ?>
                                                    <li class="mb-2">
                                                        <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($subchild->id, $subchild->language)); ?>" class="text-decoration-none small d-flex align-items-center">
                                                            <svg class="icon icon-xs me-1 text-primary"><use href="<?php echo $baseImagePath; ?>sprites.svg#it-chevron-right"></use></svg>
                                                            <?php echo $this->escape($subchild->title); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
