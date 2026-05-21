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

$app = Factory::getApplication();
$this->category->text = $this->category->description;
$app->triggerEvent('onContentPrepare', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$this->category->description = $this->category->text;

$results = $app->triggerEvent('onContentAfterTitle', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$afterDisplayTitle = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentBeforeDisplay', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$beforeDisplayContent = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentAfterDisplay', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$afterDisplayContent = trim(implode("\n", $results));

?>
<div class="container py-5" id="template-lista-accent-bar">
  
  <!-- Intestazione Categoria -->
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

  <!-- Lista Articoli (da default_articles.php) -->
  <?php echo $this->loadTemplate('articles'); ?>

  <!-- Sottocategorie (da default_children.php se presenti) -->
  <?php if (!empty($this->children[$this->category->id]) && $this->maxLevel != 0) : ?>
      <div class="cat-children mt-5">
          <?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
              <h3>
                  <?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?>
              </h3>
          <?php endif; ?>
          <?php echo $this->loadTemplate('children'); ?>
      </div>
  <?php endif; ?>

</div>
