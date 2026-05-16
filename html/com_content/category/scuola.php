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

$url = Uri::root();

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

$htag    = $this->params->get('show_page_heading') ? 'h2' : 'h1';

//echo json_encode($this->category);
//echo $this->category->parent_id;
$catactive =  $this->category->title;

$imgdesc = $this->category->getParams()->get('image');

$baseImagePath = Uri::root(false) . "media/templates/site/bootstrap-italia-scuole/images/";

// Prepara l'immagine di sfondo: se c'è, assicurati che il percorso sia assoluto
if ($imgdesc) {
    // Se inizia già con http o con /, lo teniamo così, altrimenti aggiungiamo la root
    $bgImage = (str_starts_with($imgdesc, 'http') || str_starts_with($imgdesc, '/')) ? $imgdesc : Uri::root(true) . '/' . $imgdesc;
} else {
    $bgImage = $baseImagePath . 'imgsegnaposto.jpg';
}
// Estrae il nome completo dell'istituto dalle opzioni del template (Titolo Logo) o dal nome sito
$templateParams = $app->getTemplate(true)->params;
$pageTitle = trim($templateParams->get('logoTopTitle', '') . ' ' . $templateParams->get('logoTitle', ''));
if (empty($pageTitle)) {
    $pageTitle = $app->get('sitename');
}
$primaryColor = $templateParams->get('primary_color', '#0066cc');

// Estrazione parametri custom dal menu item
$quoteText = $this->params->get('quote_text', '"Aiutami a fare da me"');
$quoteAuthor = $this->params->get('quote_author', 'M. Montessori');
$statStudenti = $this->params->get('stat_studenti', '1.227');
$statDocenti = $this->params->get('stat_docenti', '103');
$statLaboratori = $this->params->get('stat_laboratori', '7');
$statAule = $this->params->get('stat_aule', '8');
$statAta = $this->params->get('stat_ata', '25');
$statProgetti = $this->params->get('stat_progetti', '12');

// Recupera gli articoli per la sezione "Le nostre sedi" (cerca categoria con alias 'le-sedi' o 'sedi')
$db = Factory::getDbo();
$query = $db->getQuery(true);
$query->select('a.*')
      ->from($db->quoteName('#__content', 'a'))
      ->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON c.id = a.catid')
      ->where('(' . $db->quoteName('c.alias') . ' = ' . $db->quote('edifici-scolastici') . ' OR ' . $db->quoteName('c.alias') . ' = ' . $db->quote('edifici') . ')')
      ->where($db->quoteName('a.state') . ' = 1')
      ->order($db->quoteName('a.ordering') . ' ASC');
$db->setQuery($query);
$sediArticles = $db->loadObjectList();

?>

<section class="it-hero-wrapper bg-primary py-5 text-white" style="background-image: linear-gradient(<?php echo $primaryColor; ?>cc, <?php echo $primaryColor; ?>cc), url('<?php echo $bgImage; ?>'); background-size: cover; background-position: center;">
<div class="container py-5">
<div class="row align-items-center"><!-- Colonna Testo/Citazione -->
<div class="col-lg-6 mb-4 mb-lg-0">
<div class="it-hero-text-wrapper text-white">
<h1 class="display-4 font-weight-bold mb-3"><?php echo $pageTitle; ?></h1>
<blockquote class="blockquote text-white border-left pl-3" style="border-width: 4px !important; border-color: rgba(255,255,255,0.5) !important;">
<p class="mb-0 fst-italic"><?php echo $quoteText; ?></p>
<footer class="blockquote-footer text-white-50 mt-2"><?php echo $quoteAuthor; ?></footer></blockquote>
</div>
</div>
<!-- Colonna Numeri/Card -->
<div class="col-lg-6">
<div class="row g-3"><!-- Studenti -->
<div class="col-6 mb-3">
<div class="card h-100 text-white border-0 shadow-sm" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">
<div class="card-body p-3"><span class="h2 d-block fw-bold mb-0 text-white"><?php echo $statStudenti; ?></span> <span class="text-uppercase small fw-semibold">Studenti attivi</span></div>
</div>
</div>
<!-- Docenti -->
<div class="col-6 mb-3">
<div class="card h-100 text-white border-0 shadow-sm" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">
<div class="card-body p-3"><span class="h2 d-block fw-bold mb-0 text-white"><?php echo $statDocenti; ?></span> <span class="text-uppercase small fw-semibold">Docenti</span></div>
</div>
</div>
<!-- Laboratori -->
<div class="col-6 mb-3">
<div class="card h-100 text-white border-0 shadow-sm" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">
<div class="card-body p-3"><span class="h2 d-block fw-bold mb-0 text-white"><?php echo $statLaboratori; ?></span> <span class="text-uppercase small fw-semibold">Laboratori</span></div>
</div>
</div>
<!-- Aule Tematiche -->
<div class="col-6 mb-3">
<div class="card h-100 text-white border-0 shadow-sm" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">
<div class="card-body p-3"><span class="h2 d-block fw-bold mb-0 text-white"><?php echo $statAule; ?></span> <span class="text-uppercase small fw-semibold">Aule tematiche</span></div>
</div>
</div>
<!-- Personale ATA -->
<div class="col-6 mb-3">
<div class="card h-100 text-white border-0 shadow-sm" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">
<div class="card-body p-3"><span class="h2 d-block fw-bold mb-0 text-white"><?php echo $statAta; ?></span> <span class="text-uppercase small fw-semibold">Personale ATA</span></div>
</div>
</div>
<!-- Progetti Attivi -->
<div class="col-6 mb-3">
<div class="card h-100 text-white border-0 shadow-sm" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">
<div class="card-body p-3"><span class="h2 d-block fw-bold mb-0 text-white"><?php echo $statProgetti; ?></span> <span class="text-uppercase small fw-semibold">Progetti attivi</span></div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>

<?php if (!empty($sediArticles)) : ?>
<!-- Sezione Le Nostre Sedi (Bootstrap Italia per Joomla) -->
<section class="py-5 bg-white" id="le-nostre-sedi">
  <div class="container">
    <div class="row mb-4">
      <div class="col-12">
        <h2 class="h3 font-weight-bold">Le nostre sedi</h2>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($sediArticles as $index => $sede) : 
          $images = json_decode($sede->images);
          $img = !empty($images->image_intro) ? Uri::root(true) . '/' . ltrim($images->image_intro, '/') : $baseImagePath . 'imgsegnaposto.jpg';
          
          // Estrai i campi aggiuntivi per l'indirizzo
          $customFields = \Joomla\Component\Fields\Administrator\Helper\FieldsHelper::getFields('com_content.article', $sede, true);
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
          $address = !empty($customAddress) ? $customAddress : (!empty($images->image_intro_caption) ? $images->image_intro_caption : $sede->title);
          
          $articleUrl = Route::_(RouteHelper::getArticleRoute($sede->id, $sede->catid, $sede->language));
          
          $urls = json_decode($sede->urls);
          $busLines = !empty($urls->urlatext) ? $urls->urlatext : (!empty($urls->urlbtext) ? $urls->urlbtext : '');
          
          // Alterna il colore del tag tra primary e danger
          $tagColor = ($index % 2 == 0) ? 'bg-primary' : 'bg-secondary';
      ?>
      <div class="col-12 col-md-6">
        <div class="card shadow-sm h-100 border-0">
          <div class="it-card-image-wrapper">
            <img src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid w-100 rounded-top" alt="<?php echo htmlspecialchars($sede->title, ENT_QUOTES, 'UTF-8'); ?>" style="height: 250px; object-fit: cover;">
            <div class="it-card-tag <?php echo $tagColor; ?> text-white text-uppercase p-2 position-absolute top-0 start-0 small font-weight-bold m-3">
              <?php echo htmlspecialchars($sede->title, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          </div>
          <div class="card-body p-4">
            <h3 class="h5 card-title font-weight-bold"><?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?></h3>
            <div class="card-text small">
              <?php echo HTMLHelper::_('content.prepare', $sede->introtext, '', 'com_content.category'); ?>
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
            
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php echo $afterDisplayTitle; ?>
<?php echo $beforeDisplayContent; ?>
<?php echo $afterDisplayContent; ?>
