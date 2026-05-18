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
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$params    = $this->item->params;
$canEdit   = $this->item->params->get('access-edit');
$info      = $params->get('info_block_position', 0);

$currentDate   = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED || $this->item->publish_up > $currentDate)
    || ($this->item->publish_down < $currentDate && $this->item->publish_down !== null);

$introimg = json_decode($this->item->images);

$app           = Factory::getApplication();
$template      = $app->getTemplate(true)->template;
$baseImagePath = Uri::root(false) . 'media/templates/site/' . $template . '/images/';

$link = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));

// ── Parsing del campo "Didascalia immagine intro"
$captionRaw   = $introimg->image_intro_caption ?? '';
$captionParts = explode('|', $captionRaw, 2);
$iconName     = trim($captionParts[0]);

$hasImage = !empty($introimg->image_intro);
$showImage = false;

if (empty($iconName) && $hasImage) {
    $showImage = true;
} else {
    $iconName = $iconName ?: 'it-map-marker';
}

// Ricava il nome colore
$colorName    = isset($captionParts[1]) ? preg_replace('/[^a-z0-9_-]/i', '', trim($captionParts[1])) : '';
$colorName    = $colorName ?: 'primary';
$borderClass  = 'border-' . $colorName;
$iconClass    = 'icon-' . $colorName;
$btnClass     = 'btn-outline-' . $colorName;

// Scegli lo sprite corretto
$spriteFile = str_starts_with($iconName, 'bi-') ? 'bootstrap-icons.svg' : 'sprites.svg';
$iconAnchor = ($spriteFile === 'bootstrap-icons.svg') ? substr($iconName, 3) : $iconName;

?>

<article class="it-card rounded border-top border-4 <?php echo $borderClass; ?> shadow-sm h-100 d-flex flex-column<?php echo $isUnpublished ? ' system-unpublished' : ''; ?>">

    <?php if ($showImage) : ?>
    <div class="it-card-image-wrapper p-3 pb-0 text-center">
        <img src="<?php echo htmlspecialchars(Uri::root(true) . '/' . ltrim($introimg->image_intro, '/'), ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid rounded w-100" alt="<?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>" style="height: 150px; object-fit: cover;">
    </div>
    <?php else : ?>
    <div class="it-card-icon-area text-center pt-4 pb-2">
        <svg class="icon icon-xl <?php echo $iconClass; ?>" aria-hidden="true">
            <use href="<?= $baseImagePath ?><?= $spriteFile ?>#<?php echo htmlspecialchars($iconAnchor, ENT_QUOTES, 'UTF-8'); ?>"></use>
        </svg>
    </div>
    <?php endif; ?>

    <div class="it-card-body d-flex flex-column flex-grow-1 p-3">

        <h3 class="it-card-title h6 mb-2">
            <a href="<?php echo $link; ?>"><?php echo $this->item->title; ?></a>
        </h3>

        <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
            <div class="mb-2">
                <?php foreach ($this->item->tags->itemTags as $tag) : ?>
                    <a href="<?php echo Route::_(\Joomla\Component\Tags\Site\Helper\RouteHelper::getComponentTagRoute($tag->tag_id . ':' . $tag->alias, $tag->language)); ?>" data-element="topic-list" class="badge rounded-pill badge-outline text-<?php echo $colorName; ?> border-<?php echo $colorName; ?> text-decoration-none">
                        <?php echo htmlspecialchars($tag->title, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p class="it-card-text small flex-grow-1 px-3">
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('string.truncate', strip_tags($this->item->introtext), 160, false, false); ?>
        </p>

        <footer class="it-card-related mt-auto pt-2">
            <a href="<?php echo $link; ?>" class="btn <?php echo $btnClass; ?> btn-sm w-100">
                DETTAGLI
            </a>
        </footer>

        <?php if ($canEdit) : ?>
            <?php echo LayoutHelper::render('joomla.content.icons', ['params' => $params, 'item' => $this->item]); ?>
        <?php endif; ?>

    </div>
</article>
