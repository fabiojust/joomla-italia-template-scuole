<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$lang   = Factory::getLanguage();
$user   = Factory::getUser();
$groups = $user->getAuthorisedViewLevels();

if ($this->maxLevel != 0 && isset($this->children[$this->category->id]) && count($this->children[$this->category->id]) > 0) : ?>
    
    <?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
        <?php // Verifica i permessi di accesso alla sottocategoria ?>
        <?php if (in_array($child->access, $groups)) : ?>
            <?php if ($this->params->get('show_empty_categories') || $child->numitems || (is_countable($child->getChildren()) && count($child->getChildren())) ) : ?>
            
            <!-- Card Sottocategoria -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="flex-grow-1">
                            <!-- Titolo Sottocategoria -->
                            <h3 class="h5 card-title font-weight-bold mb-2">
                                <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>" class="text-decoration-none text-primary">
                                    <?php echo htmlspecialchars($child->title, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h3>
                            
                            <!-- Descrizione Sottocategoria -->
                            <?php if ($this->params->get('show_subcat_desc') == 1) : ?>
                                <?php if ($child->description) : ?>
                                <div class="category-desc text-secondary small mb-3">
                                    <?php echo HTMLHelper::_('content.prepare', $child->description, '', 'com_content.category'); ?>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <!-- Numero Articoli -->
                            <?php if ($this->params->get('show_cat_num_articles', 1)) : ?>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-info rounded-pill">
                                    <?php echo $child->getNumItems(true); ?> <?php echo Text::_('COM_CONTENT_NUM_ARTICLES'); ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Pulsante Vai a Categoria -->
                        <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>" 
                           class="btn btn-sm btn-outline-primary ms-3 flex-shrink-0">
                            <?php echo Text::_('COM_CONTENT_VIEW_CATEGORY'); ?>
                        </a>
                    </div>
                    
                    <!-- Sottocategorie Annidate (se presenti) -->
                    <?php if ($this->maxLevel > 1 && is_countable($child->getChildren()) && count($child->getChildren()) > 0) : ?>
                    <div class="border-top mt-3 pt-3">
                        <button class="btn btn-link btn-sm p-0 text-secondary" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#category-<?php echo $child->id; ?>" 
                                aria-expanded="false" 
                                aria-label="<?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?>">
                            <svg class="icon icon-xs me-1" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                            <?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?> (<?php echo is_countable($child->getChildren()) ? count($child->getChildren()) : 0; ?>)
                        </button>
                        
                        <div class="collapse mt-3" id="category-<?php echo $child->id; ?>">
                            <div class="ps-3 border-start border-secondary">
                                <?php
                                    $this->children[$child->id] = $child->getChildren();
                                    $this->category = $child;
                                    $this->maxLevel--;
                                    echo $this->loadTemplate('children');
                                    $this->category = $child->getParent();
                                    $this->maxLevel++;
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>

<?php endif;
