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
use Joomla\CMS\Language\Text;

// Create a shortcut for params.
$params = $this->item->params;
$canEdit = $this->item->params->get('access-edit');
$info    = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

$currentDate   = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED || $this->item->publish_up > $currentDate)
    || ($this->item->publish_down < $currentDate && $this->item->publish_down !== null);

$app           = Factory::getApplication();
$template      = $app->getTemplate(true)->template;
$baseImagePath = Uri::root(false) . 'media/templates/site/' . $template . '/images/';

$images = json_decode($this->item->images);
$img = !empty($images->image_intro) ? Uri::root(true) . '/' . ltrim($images->image_intro, '/') : $baseImagePath . 'imgsegnaposto.jpg';

// Estrai i campi aggiuntivi per l'indirizzo
$customFields = \Joomla\Component\Fields\Administrator\Helper\FieldsHelper::getFields('com_content.article', $this->item, true);
$customAddress = '';
foreach ($customFields as $field) {
    if ($field->name === 'indirizzo' && !empty($field->value)) {
        $customAddress = is_array($field->value) ? implode(', ', $field->value) : $field->value;
        break;
    } elseif ($field->type === 'subform') {
        // Metodo standard Joomla 4/5 per sottomoduli preparati
        if (isset($field->subformRows) && is_array($field->subformRows)) {
            foreach ($field->subformRows as $row) {
                foreach ($row as $subField) {
                    if ($subField->name === 'indirizzo' && !empty($subField->value)) {
                        $customAddress = is_array($subField->value) ? implode(', ', $subField->value) : $subField->value;
                        break 3;
                    }
                }
            }
        }
        
        // Metodo di fallback con ispezione del rawvalue JSON
        if (empty($customAddress) && !empty($field->rawvalue)) {
            $subData = is_string($field->rawvalue) ? json_decode($field->rawvalue, true) : $field->rawvalue;
            if (is_array($subData)) {
                array_walk_recursive($subData, function($val, $key) use (&$customAddress) {
                    if (empty($customAddress) && is_string($val)) {
                        // Se la chiave è esattamente indirizzo, o se il valore sembra palesemente un indirizzo (inizia con Via, Piazza, ecc.)
                        if ($key === 'indirizzo' || preg_match('/^(Via|Piazza|Viale|Corso|Largo|Piazzale|Contrada|Strada|Loc\.)\b/i', trim($val))) {
                            $customAddress = trim($val);
                        }
                    }
                });
            }
        }
        if (!empty($customAddress)) break;
    }
}

// Fallback: Custom Field -> Didascalia Immagine -> Titolo
$address = !empty($customAddress) ? $customAddress : (!empty($images->image_intro_caption) ? $images->image_intro_caption : $this->item->title);

$articleUrl = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));

$urls = json_decode($this->item->urls);
$busLines = !empty($urls->urlatext) ? $urls->urlatext : (!empty($urls->urlbtext) ? $urls->urlbtext : '');

// Alterna il colore del tag tra primary e secondary per visual design - potremmo usare l'id o un numero random
$tagColor = ($this->item->id % 2 == 0) ? 'bg-primary' : 'bg-secondary';
?>

<div class="card shadow-sm h-100 border-0 <?php echo $isUnpublished ? ' system-unpublished' : ''; ?>">
    <div class="it-card-image-wrapper">
    <img src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid w-100 rounded-top" alt="<?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>" style="height: 250px; object-fit: cover;">
    <div class="it-card-tag <?php echo $tagColor; ?> text-white text-uppercase p-2 position-absolute top-0 start-0 small font-weight-bold m-3">
        <?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    </div>
    <div class="card-body p-4">
    <h3 class="h5 card-title font-weight-bold"><?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?></h3>
    <div class="card-text small">
        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $this->item->introtext, '', 'com_content.category'); ?>
    </div>
    
    <div class="d-flex flex-wrap align-items-center mt-3 gap-3 border-top pt-3">
        <a href="<?php echo $articleUrl; ?>" class="btn btn-link btn-xs p-0 text-primary d-flex align-items-center text-decoration-none">
        <svg class="icon icon-sm icon-primary me-1" aria-hidden="true"><use href="<?= Uri::root(true) ?>/media/templates/site/bootstrap-italia-scuole/images/sprites.svg#it-arrow-right"></use></svg>
        <span class="small font-weight-bold">Scopri la sede</span>
        </a>
        
        <?php if ($busLines) : ?>
        <div class="d-flex align-items-center text-secondary">
        <svg class="icon icon-sm icon-secondary me-1" aria-hidden="true"><use href="<?= Uri::root(true) ?>/media/templates/site/bootstrap-italia-scuole/images/sprites.svg#it-bus"></use></svg>
        <span class="small"><?php echo htmlspecialchars($busLines, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($canEdit) : ?>
        <?php echo LayoutHelper::render('joomla.content.icons', ['params' => $params, 'item' => $this->item]); ?>
    <?php endif; ?>
    </div>
</div>
