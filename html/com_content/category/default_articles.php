<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Uri\Uri;

$baseImagePath = Uri::root(true) . "/media/templates/site/bootstrap-italia-scuole/images/";
?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="com-content-category__articles">

  <!-- Lista Articoli -->
  <div class="row g-0">
    <div class="col-12">
      <?php if (empty($this->items)) : ?>
        <?php if ($this->params->get('show_no_articles', 1)) : ?>
          <div class="alert alert-info">
            <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
            <?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?>
          </div>
        <?php endif; ?>
      <?php else : ?>
        <?php 
        $colors = ['primary', 'secondary', 'info', 'warning']; 
        foreach ($this->items as $i => $article) : 
            $color = $colors[$i % count($colors)];
            $articleLink = Route::_(RouteHelper::getArticleRoute($article->slug, $article->catid, $article->language));
        ?>
          <article class="p-4 border-start border-<?php echo $color; ?> border-4 mb-4 shadow bg-white">
            <div class="mb-2 small">
              <span class="text-primary text-uppercase"><strong><?php echo $this->escape($article->category_title ?: $this->category->title); ?></strong></span>
              <span class="text-muted">• ultima modifica <?php echo HTMLHelper::_('date', $article->modified, Text::_('DATE_FORMAT_LC3')); ?></span>
            </div>
            
            <h2 class="h4 font-weight-bold mb-2">
              <a href="<?php echo $articleLink; ?>" class="text-decoration-none text-dark hover:text-primary">
                <?php echo $this->escape($article->title); ?>
              </a>
            </h2>
            
            <div class="d-lg-flex justify-content-lg-between align-items-lg-end">
                <p class="text-secondary small mb-3 mb-lg-0 pe-lg-4">
                  <?php echo HTMLHelper::_('string.truncate', strip_tags($article->introtext ?? ''), 150, false, false); ?>
                </p>
                
                <a href="<?php echo $articleLink; ?>" class="btn btn-link btn-xs p-0 text-primary d-inline-flex align-items-center font-weight-bold text-decoration-none flex-shrink-0 mt-3 mt-lg-0">
                  LEGGI TUTTO
                  <svg class="icon icon-xs ms-2" aria-hidden="true"><use href="<?php echo $baseImagePath; ?>sprites.svg#it-arrow-right"></use></svg>
                </a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Paginazione -->
  <?php if (!empty($this->items) && (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1))) : ?>
    <nav class="pagination-wrapper mt-5 d-flex justify-content-center" aria-label="Navigazione pagine">
      <?php echo $this->pagination->getPagesLinks(); ?>
    </nav>
  <?php endif; ?>

  <div>
      <input type="hidden" name="filter_order" value="">
      <input type="hidden" name="filter_order_Dir" value="">
      <input type="hidden" name="limitstart" value="">
      <input type="hidden" name="task" value="">
  </div>
</form>
