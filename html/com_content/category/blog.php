<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Uri\Uri;

$app = Factory::getApplication();

// Preparazione evento onContentPrepare
$this->category->text = $this->category->description;
$app->triggerEvent('onContentPrepare', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$this->category->description = $this->category->text;

$results = $app->triggerEvent('onContentAfterTitle', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$afterDisplayTitle = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentBeforeDisplay', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$beforeDisplayContent = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentAfterDisplay', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$afterDisplayContent = trim(implode("\n", $results));

$catactive = $this->category->title;
$baseImagePath = Uri::root(false) . "media/templates/site/bootstrap-italia-scuole/images/";
$childCategories = !empty($this->children[$this->category->id]) ? $this->children[$this->category->id] : [];
?>

<!-- Layout Blog Griglia Moderna - Bootstrap Italia Scuole -->
<div class="container py-5" id="blog-grid-modern">
  
    <!-- Titolo e Descrizione Categoria -->
  <div class="row mb-5">
    <div class="col-12">
      <?php if ($this->params->get('show_category_title', 1)) : ?>
        <h1 class="display-4 font-weight-bold text-primary mb-3"><?php echo $this->category->title; ?></h1>
      <?php endif; ?>
      <p class="lead text-secondary">
        <?php 
          if ($this->params->get('show_description') && $this->category->description) {
            echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category');
          }
        ?>
      </p>
      <?php echo $beforeDisplayContent; ?>
    </div>
  </div>

  <!-- Filtri Categoria -->
  <?php if (!empty($childCategories)) : ?>
    <div class="row mb-5">
      <div class="col-12 d-flex flex-wrap gap-2" id="category-filters">
        <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($this->category->id, $this->category->language)); ?>" class="btn btn-primary btn-sm rounded-pill px-3">Tutti</a>
        <?php foreach ($childCategories as $childCategory) : ?>
          <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($childCategory->id, $childCategory->language)); ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <?php echo htmlspecialchars($childCategory->title, ENT_QUOTES, 'UTF-8'); ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Nessun Articolo -->
  <?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
    <?php if ($this->params->get('show_no_articles', 1)) : ?>
      <div class="alert alert-info">
        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
        <?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?>
      </div>
    <?php endif; ?>
  <?php else : ?>

    <!-- Griglia Articoli -->
    <div class="row g-4" id="blog-articles-grid">
      <?php foreach ($this->lead_items as $index => &$item) :
        $this->item = &$item;
      ?>
        <div class="col-12 col-md-6 col-lg-4 mb-4 blog-article-item" data-cat-id="<?php echo (int) $this->item->catid; ?>" data-cat-title="<?php echo htmlspecialchars($this->item->category_title ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <?php echo $this->loadTemplate('item'); ?>
        </div>
      <?php endforeach; ?>

      <?php foreach ($this->intro_items as $index => &$item) :
        $this->item = &$item;
      ?>
        <div class="col-12 col-md-6 col-lg-4 mb-4 blog-article-item" data-cat-id="<?php echo (int) $this->item->catid; ?>" data-cat-title="<?php echo htmlspecialchars($this->item->category_title ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <?php echo $this->loadTemplate('item'); ?>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($this->link_items)) : ?>
      <div class="items-more">
        <?php echo $this->loadTemplate('links'); ?>
      </div>
    <?php endif; ?>

    <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
      <div class="com-content-category-blog__navigation w-100">
        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
          <p class="com-content-category-blog__counter counter float-end pt-3 pe-2">
            <?php echo $this->pagination->getPagesCounter(); ?>
          </p>
        <?php endif; ?>
        <div class="com-content-category-blog__pagination">
          <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
      </div>
    <?php endif; ?>

  <?php endif; ?>

  <?php echo $afterDisplayContent; ?>

</div>
