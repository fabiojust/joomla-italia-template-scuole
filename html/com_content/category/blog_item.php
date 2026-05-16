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
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Parametri articolo
$params      = $this->item->params;
$canEdit     = $params->get('access-edit');
$baseImagePath = Uri::root(false) . "media/templates/site/bootstrap-italia-scuole/images/";

// Controlla se l'articolo è unpublished
$currentDate   = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED || $this->item->publish_up > $currentDate)
    || ($this->item->publish_down < $currentDate && $this->item->publish_down !== null);

// Estrae le immagini
$images = json_decode($this->item->images ?? '{}');
$imageUrl = !empty($images->image_intro) ? Uri::root(true) . '/' . ltrim($images->image_intro, '/') : $baseImagePath . 'imgsegnaposto.jpg';
$imageAlt = !empty($images->image_intro_alt) ? $images->image_intro_alt : $this->item->title;

// URL articolo
$articleUrl = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));

// Categoria articolo
$categoryTitle = $this->item->category_title ?? '';
if (empty($categoryTitle) && !empty($this->item->catid)) {
    $db = Factory::getContainer()->get('DatabaseDriver');
    $query = $db->getQuery(true)
        ->select($db->quoteName('title'))
        ->from($db->quoteName('#__categories'))
        ->where($db->quoteName('id') . ' = ' . (int) $this->item->catid);
    $db->setQuery($query);
    $categoryTitle = $db->loadResult() ?: '';
}

if (empty($categoryTitle)) {
    $categoryTitle = Text::_('UNCATEGORISED');
}

// Colore tag coerente per categoria
$tagColors = ['bg-primary', 'bg-secondary', 'bg-danger', 'bg-success', 'bg-warning', 'bg-info'];
$tagKey = $categoryTitle . '_' . ((int) $this->item->catid);
$tagColor = $tagColors[crc32($tagKey) % count($tagColors)];

?>

<!-- Card Articolo Moderno Bootstrap Italia -->
<div class="card h-100 shadow-sm border-0 it-card-wrapper">
  <div class="it-card-img-wrapper position-relative">
    <img src="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>" 
         class="img-fluid rounded-top w-100" 
         alt="<?php echo htmlspecialchars($imageAlt, ENT_QUOTES, 'UTF-8'); ?>" 
         style="aspect-ratio: 16/9; object-fit: cover;">
    <div class="it-card-tag <?php echo $tagColor; ?> text-white text-uppercase p-2 position-absolute top-0 start-0 small font-weight-bold m-3">
      <?php 
        echo htmlspecialchars(strlen($categoryTitle) > 15 ? substr($categoryTitle, 0, 15) . '...' : $categoryTitle, ENT_QUOTES, 'UTF-8');
      ?>
    </div>
  </div>
  
  <div class="card-body p-4 d-flex flex-column">
    <!-- Data Pubblicazione -->
    <div class="d-flex align-items-center mb-2 text-muted small">
      <svg class="icon icon-xs me-1" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="16" y1="2" x2="16" y2="6"></line>
        <line x1="8" y1="2" x2="8" y2="6"></line>
        <line x1="3" y1="10" x2="21" y2="10"></line>
      </svg>
      <span><?php echo HTMLHelper::_('date', $this->item->created, Text::_('DATE_FORMAT_LC4')); ?></span>
    </div>
    
    <!-- Titolo Articolo -->
    <h2 class="h4 card-title font-weight-bold mb-3">
      <?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>
    </h2>
    
    <!-- Testo Introduttivo -->
    <p class="card-text small mb-4">
      <?php 
        $introtext = strip_tags($this->item->introtext ?? '');
        echo htmlspecialchars(strlen($introtext) > 120 ? substr($introtext, 0, 120) . '...' : $introtext, ENT_QUOTES, 'UTF-8');
      ?>
    </p>
    
    <!-- Pulsante Leggi Articolo -->
    <a href="<?php echo $articleUrl; ?>" class="btn btn-link btn-sm p-0 text-primary mt-auto d-flex align-items-center font-weight-bold text-decoration-none">
      <?php echo Text::_('COM_CONTENT_READ_MORE'); ?>
      <svg class="icon icon-xs ms-2" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="5" y1="12" x2="19" y2="12"></line>
        <polyline points="12 5 19 12 12 19"></polyline>
      </svg>
    </a>
  </div>
</div>

<?php if ($canEdit) : ?>
  <!-- Pulsanti Modifica (visibili solo agli autori) -->
  <div class="mt-2">
    <?php echo LayoutHelper::render('joomla.content.icons', ['params' => $params, 'item' => $this->item]); ?>
  </div>
<?php endif; ?>

<?php if ($isUnpublished) : ?>
  <!-- Indicatore Unpublished -->
  <div class="alert alert-warning mt-2 small mb-0" role="alert">
    <?php echo Text::_('COM_CONTENT_ARTICLE_UNPUBLISHED'); ?>
  </div>
<?php endif; ?>
